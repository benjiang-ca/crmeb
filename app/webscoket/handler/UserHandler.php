<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\webscoket\handler;


use app\common\repositories\store\service\StoreServiceLogRepository;
use app\common\repositories\store\service\StoreServiceRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use app\common\repositories\user\UserRepository;
use crmeb\services\JwtTokenService;
use crmeb\services\SwooleTaskService;
use Swoole\Server;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Db;
use Throwable;

/**
 * Class UserHandler
 * @package app\webscoket\handler
 * @author xaboy
 * @day 2020-04-29
 */
class UserHandler
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * UserHandler constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @param array $data
     * @return mixed
     * @author xaboy
     * @day 2020-05-06
     */
    public function test(array $data)
    {
        return app('json')->success($data);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-06
     */
    public function login(array $data)
    {
        $token = $data['token'] ?? '';
        if (!$token) return app('json')->message('err_tip', '登录过期');

        /**
         * @var UserRepository $repository
         */
        $repository = app()->make(UserRepository::class);
        $service = new JwtTokenService();
        try {
            $payload = $service->parseToken($token);
        } catch (Throwable $e) {//Token 过期
            app('json')->message('err_tip', '登录过期');
        }
        if ('user' != $payload->jti[1])
            app('json')->message('err_tip', '登录过期');

        $user = $repository->get($payload->jti[0]);
        if (!$user)
            app('json')->message('err_tip', '账号不存在');
        if (!$user['status'])
            app('json')->message('err_tip', '账号已被禁用');

        return app('json')->success(['uid' => $user->uid, 'data' => $user->toArray()]);
    }

    /**
     * @param array $data
     * @return mixed
     * @author xaboy
     * @day 2020-05-06
     */
    public function uid(array $data)
    {
        return app('json')->success(['uid' => $data['frame']->uid]);
    }

    public function service_chat_end(array $result)
    {
        app()->make(StoreServiceLogRepository::class)->unChat($result['frame']->uid, true);
    }

    public function chat_end(array $result)
    {
        app()->make(StoreServiceLogRepository::class)->unChat($result['frame']->uid);
    }

    public function service_chat_start(array $result)
    {
        app()->make(StoreServiceLogRepository::class)->serviceToChat($result['frame']->uid, $result['data']['uid']);
    }

    public function chat_start(array $result)
    {
        app()->make(StoreServiceLogRepository::class)->userToChat($result['frame']->uid, $result['data']['mer_id']);
    }

    public function service_chat(array $result)
    {
        $data = $result['data'];
        $frame = $result['frame'];
        if (!isset($data['msn_type']) || !isset($data['msn']) || !isset($data['uid']))
            return app('json')->message('err_tip', '数据格式错误');
        if (!$data['msn']) return app('json')->message('err_tip', '请输入发送内容');
        if (!in_array($data['msn_type'], [1, 2, 3, 4, 5, 6]))
            return app('json')->message('err_tip', '消息类型有误');
        $service = app()->make(StoreServiceRepository::class)->getService($frame->uid);
        if (!$service || !$service['status'])
            return app('json')->message('err_tip', '没有权限');
        $storeServiceLogRepository = app()->make(StoreServiceLogRepository::class);
        if (!$storeServiceLogRepository->issetLog($data['uid'], $service->mer_id))
            return app('json')->message('err_tip', '不能主动发送给用户');

        $data['msn'] = trim(strip_tags(str_replace(["\n", "\t", "\r", " ", "&nbsp;"], '', htmlspecialchars_decode($data['msn']))));
        $data['mer_id'] = $service->mer_id;
        $data['service_id'] = $service->service_id;
        $data['send_type'] = 1;
        try {
            $storeServiceLogRepository->checkMsn($service->mer_id, $data['uid'], $data['msn_type'], $data['msn']);
        } catch (ValidateException $e) {
            return app('json')->message('err_tip', $e->getMessage());
        }
        $log = $storeServiceLogRepository->create($data);
        $storeServiceLogRepository->getSendData($log);
        $log->set('service', $service->visible(['service_id', 'avatar', 'nickname'])->toArray());
        $storeServiceLogRepository->serviceToChat($frame->uid, $data['uid']);
        $log = $log->toArray();

        SwooleTaskService::chatToUser([
            'uid' => $data['uid'],
            'data' => $log,
            'except'=>[$frame->fd]
        ]);

        return app('json')->message('chat', $log);
    }

    public function send_chat(array $result)
    {
        $data = $result['data'];
        $frame = $result['frame'];
        if (!isset($data['msn_type']) || !isset($data['msn']) || !isset($data['mer_id']))
            return app('json')->message('err_tip', '数据格式错误');
        if (!$data['msn']) return app('json')->message('err_tip', '请输入发送内容');
        if (!in_array($data['msn_type'], [1, 2, 3, 4, 5, 6]))
            return app('json')->message('err_tip', '消息类型有误');
        if (!app()->make(MerchantRepository::class)->exists(intval($data['mer_id'])))
            return app('json')->message('err_tip', '商户不存在');
        $service = app()->make(StoreServiceRepository::class)->getChatService($data['mer_id'], $frame->uid);
        if (!$service)
            return app('json')->message('err_tip', '该商户暂无有效客服');
        $data['msn'] = filter_emoji(trim(strip_tags(str_replace(["\n", "\t", "\r", " ", "&nbsp;"], '', htmlspecialchars_decode($data['msn'])))));
        if (!$data['msn'])
            return app('json')->message('err_tip', '内容字符无效');
        $data['uid'] = $frame->uid;
        $data['service_id'] = $service->service_id;
        $data['send_type'] = 0;
        $storeServiceLogRepository = app()->make(StoreServiceLogRepository::class);
        try {
            $storeServiceLogRepository->checkMsn($data['mer_id'], $frame->uid, $data['msn_type'], $data['msn']);
        } catch (ValidateException $e) {
            return app('json')->message('err_tip', $e->getMessage());
        }
        $log = $storeServiceLogRepository->create($data);
        $storeServiceLogRepository->getSendData($log);
        $storeServiceLogRepository->userToChat($data['uid'], $data['mer_id']);
        $log->user;
        $log = $log->toArray();

        //TODO 发送给客服,是否在线,发送提醒
        SwooleTaskService::chatToService([
            'uid' => $service->uid,
            'data' => $log,
            'except'=>[$frame->fd]
        ]);

        return app('json')->message('chat', $log);
    }

    public function close($result)
    {
        app()->make(StoreServiceLogRepository::class)->unChat($result['uid']);
        app()->make(StoreServiceLogRepository::class)->unChat($result['uid'], true);
    }

}