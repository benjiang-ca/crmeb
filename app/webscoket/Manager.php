<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\webscoket;


use app\webscoket\handler\AdminHandler;
use app\webscoket\handler\MerchantHandler;
use app\webscoket\handler\UserHandler;
use Swoole\Server;
use Swoole\Table as SwooleTable;
use Swoole\Websocket\Frame;
use think\Config;
use think\facade\Cache;
use think\Request;
use think\response\Json;
use think\swoole\contract\websocket\HandlerInterface;
use think\swoole\Table;

/**
 * Class Manager
 * @package app\webscoket
 * @author xaboy
 * @day 2020-04-29
 */
class Manager implements HandlerInterface
{

    /**
     * @var \Swoole\WebSocket\Server
     */
    protected $server;

    /**
     * @var Ping
     */
    protected $pingService;

    /**
     * @var int
     */
    protected $cache_timeout;

    const USER_TYPE = ['admin', 'user', 'mer'];

    /**
     * Manager constructor.
     * @param Server $server
     * @param Ping $ping
     * @param Config $config
     */
    public function __construct(Server $server, Ping $ping, Config $config)
    {
        $this->server = $server;
        $this->pingService = $ping;
        $this->cache_timeout = intval($config->get('swoole.websocket.ping_timeout', 60000) / 1000) + 2;
        app()->bind('websocket_handler_admin', AdminHandler::class);
        app()->bind('websocket_handler_user', UserHandler::class);
        app()->bind('websocket_handler_mer', MerchantHandler::class);
    }

    /**
     * @param int $fd
     * @param Request $request
     * @return mixed
     * @author xaboy
     * @day 2020-05-06
     */
    public function onOpen($fd, Request $request)
    {
        // var_dump('onOpen');
        $type = $request->get('type');
        $token = $request->get('token');
        if (!$token || !in_array($type, self::USER_TYPE)) {
            // var_dump('type Error', $type);
            return $this->server->close($fd);
        }
        try {
            $data = $this->exec($type, 'login', compact('fd', 'request', 'token'))->getData();
        } catch (\Exception $e) {
            // var_dump($e->getMessage());
            return $this->server->close($fd);
        }
        if ($data['status'] != 200 || !($data['data']['uid'] ?? null))
            return $this->server->close($fd);
        $uid = $data['data']['uid'];
        $type = array_search($type, self::USER_TYPE);
        $this->login($type, $uid, $fd, $data['data']['data']['mer_id'] ?? null);
        $this->getTable()->set($fd, compact('type', 'uid', 'fd'));
        $this->pingService->createPing($fd, time(), $this->cache_timeout);
        return $this->send($fd, app('json')->message('ping', ['now' => time()]));
    }

    public function login($type, $uid, $fd, $merId)
    {
        $key = '_ws_' . $type;
        Cache::sadd($key, $fd);
        Cache::sadd($key . $uid, $fd);
        if ($merId) {
            $this->merLogin($uid, $fd, $merId);
        }
        $this->refresh($type, $uid);
    }

    public function merLogin($uid, $fd, $merId)
    {
        Cache::sadd('_wsm_0' . $merId, $fd);
        Cache::set('_wsm_1' . $uid, $merId);
        $this->refreshMer($uid, $merId);
    }

    public function refreshMer($uid, $merId)
    {
        Cache::expire('_wsm_0' . $merId, 1800);
        Cache::expire('_wsm_1' . $uid, 1800);
    }

    public function refresh($type, $uid)
    {
        $key = '_ws_' . $type;
        Cache::expire($key, 1800);
        Cache::expire($key . $uid, 1800);
    }

    public function logout($type, $uid, $fd)
    {
        $key = '_ws_' . $type;
        Cache::srem($key, $fd);
        Cache::srem($key . $uid, $fd);
        $merId = Cache::get('_wsm_1' . $uid);
        if ($merId) {
            Cache::delete('_wsm_1' . $uid);
            Cache::srem('_wsm_0' . $merId, $fd);
        }
    }

    public static function merFd($merId)
    {
        return Cache::smembers('_wsm_0' . $merId) ?: [];
    }

    public static function userFd($type, $uid = '')
    {
        $key = '_ws_' . $type . $uid;
        return Cache::smembers($key) ?: [];
    }

    /**
     * @return SwooleTable
     * @author xaboy
     * @day 2020-05-06
     */
    protected function getTable()
    {
        return app()->make(Table::class)->get('user');
    }

    /**
     * @param $type
     * @param $method
     * @param $result
     * @return null|Json
     * @author xaboy
     * @day 2020-05-06
     */
    protected function exec($type, $method, $result)
    {
        $handler = app()->make('websocket_handler_' . $type);
        if (!method_exists($handler, $method)) return null;
        /** @var Json $response */
        return $handler->{$method}($result);
    }

    /**
     * @param Frame $frame
     * @return bool
     * @author xaboy
     * @day 2020-04-29
     */
    public function onMessage(Frame $frame)
    {
        $info = $this->getTable()->get($frame->fd);
        $result = json_decode($frame->data, true) ?: [];

        if (!isset($result['type']) || !$result['type']) return true;
        $this->refresh($info['type'], $info['uid']);
        if ($result['type'] == 'ping') {
            return $this->send($frame->fd, app('json')->message('ping', ['now' => time()]));
        }

        $data = $result['data'] ?? [];
        $frame->uid = $info['uid'];
        /** @var Json $response */
        $response = $this->exec(self::USER_TYPE[$info['type']], $result['type'], compact('data', 'frame'));
        if ($response) return $this->send($frame->fd, $response);
        return true;
    }

    protected function send($fd, Json $json)
    {
        $this->pingService->createPing($fd, time(), $this->cache_timeout);
        $this->server->push($fd, json_encode($json->getData()));
        return true;
    }

    /**
     * @param int $fd
     * @param int $reactorId
     * @author xaboy
     * @day 2020-04-29
     */
    public function onClose($fd, $reactorId)
    {
        // var_dump('onClose');
        if ($this->getTable()->exist($fd)) {
            $data = $this->getTable()->get($fd);
            $this->logout($data['type'], $data['uid'], $fd);
            $this->getTable()->del($fd);
            $this->exec(self::USER_TYPE[$data['type']], 'close', $data);
        }
        $this->pingService->removePing($fd);
    }
}