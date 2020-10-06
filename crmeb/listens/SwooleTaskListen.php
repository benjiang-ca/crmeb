<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-15
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\store\service\StoreServiceLogRepository;
use app\common\repositories\system\admin\AdminLogRepository;
use app\common\repositories\user\UserRepository;
use app\common\repositories\user\UserVisitRepository;
use app\webscoket\Manager;
use crmeb\interfaces\ListenerInterface;
use crmeb\jobs\SendNewsJob;
use Swoole\Server;
use Swoole\Server\Task;
use think\facade\Queue;

class SwooleTaskListen implements ListenerInterface
{
    /**
     * @var Task
     */
    protected $task;

    public function handle($task): void
    {
        $this->task = $task;
        if (method_exists($this, $task->data['type']))
            $this->{$task->data['type']}($task->data['data']);
    }

    public function message(array $data)
    {
        $server = app()->make(Server::class);
        $uid = is_array($data['uid']) ? $data['uid'] : [$data['uid']];
        $except = $data['except'] ?? [];
        if (!count($uid) && $data['type'] != 'user') {
            $fds = $data['type'] == 'mer' ? Manager::merFd($data['mer_id'] ?? 0) : Manager::userFd(0);
            foreach ($fds as $fd) {
                if (!in_array($fd, $except) && $server->isEstablished($fd))
                    $server->push((int)$fd, json_encode($data['data']));
            }
        } else {
            foreach ($uid as $id) {
                $fds = Manager::userFd(array_search($data['type'], Manager::USER_TYPE), $id);
                foreach ($fds as $fd) {
                    if (!in_array($fd, $except) && $server->isEstablished($fd))
                        $server->push((int)$fd, json_encode($data['data']));
                }
            }
        }
    }

    /**
     * //TODO 用户给客服发送消息
     *
     * @param array $data
     * @author xaboy
     * @day 2020/6/15
     */
    public function chatToService(array $data)
    {
        $serviceLogRepository = app()->make(StoreServiceLogRepository::class);
        if ($serviceLogRepository->getChat($data['uid'], true) == $data['data']['uid']) {
            $this->message([
                'uid' => $data['uid'],
                'type' => 'user',
                'data' => ['type' => 'chat', 'data' => $data['data']],
                'except' => $data['except'] ?? []
            ]);
            $serviceLogRepository->serviceRead($data['data']['mer_id'], $data['data']['service_id']);
        } else {
            //TODO 客服消息提醒
            Queue::push(SendNewsJob::class, [
                $data['uid'],
                [
                    'title' => '收到用户【' . app()->make(UserRepository::class)->getUsername($data['data']['uid']) . '】的咨询消息，请及时查看',
                    'description' => $data['data'],
                    'url' => rtrim(systemConfig('site_url'), '/') . '/pages/chat/customer_list/chat?userId=' . $data['data']['uid'] . '&mer_id=' . $data['data']['mer_id'],
                    'image' => rtrim(systemConfig('site_url'), '/') . '/static/service_wechat_msg.jpg'
                ]
            ]);
        }
    }

    /**
     * //TODO 客服给用户发送消息
     * @param array $data
     * @author xaboy
     * @day 2020/6/15
     */
    public function chatToUser(array $data)
    {
        $serviceLogRepository = app()->make(StoreServiceLogRepository::class);
        if ($serviceLogRepository->getChat($data['uid']) == $data['data']['mer_id']) {
            $this->message([
                'uid' => $data['uid'],
                'type' => 'user',
                'data' => ['type' => 'chat', 'data' => $data['data']],
                'except' => $data['except'] ?? []
            ]);
            $serviceLogRepository->userRead($data['data']['mer_id'], $data['data']['uid']);
        } else {

            //TODO 用户消息提醒
            Queue::push(SendNewsJob::class, [
                $data['uid'],
                [
                    'title' => '您收到新的消息，请及时查看',
                    'description' => $data['data'],
                    'url' => rtrim(systemConfig('site_url'), '/') . '/pages/chat/customer_list/chat?mer_id=' . $data['data']['mer_id'],
                    'image' => rtrim(systemConfig('site_url'), '/') . '/static/service_wechat_msg.jpg'
                ]
            ]);
        }
    }

    public function admin(array $data)
    {
        $this->message([
                'uid' => $data['uid'] ?? [],
                'type' => 'admin',
                'data' => $data['data']
            ]
        );
    }

    public function merchant(array $data)
    {
        $this->message([
                'uid' => $data['uid'] ?? [],
                'mer_id' => $data['mer_id'],
                'type' => 'mer',
                'data' => $data['data']
            ]
        );
    }

    public function visit(array $data)
    {
        /** @var UserVisitRepository $make */
        $make = app()->make(UserVisitRepository::class);
        $make->create($data);
    }

    public function log(array $data)
    {
        app()->make(AdminLogRepository::class)->create($data['merId'], $data['result']);
    }
}
