<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/4
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\order\StoreOrderStatusRepository;
use crmeb\interfaces\ListenerInterface;
use crmeb\jobs\OrderReplyJob;
use Swoole\Timer;
use think\facade\Log;
use think\facade\Queue;

class AuthTakeOrderListen implements ListenerInterface
{

    public function handle($event): void
    {
        $storeOrderStatusRepository = app()->make(StoreOrderStatusRepository::class);
        $storeOrderRepository = app()->make(StoreOrderRepository::class);
        Timer::tick(1000 * 60 * 60, function () use ($storeOrderRepository, $storeOrderStatusRepository) {
            $timer = ((int)systemConfig('auto_take_order_timer')) ?: 15;
            $time = date('Y-m-d H:i:s', strtotime("- $timer day"));
            $ids = $storeOrderStatusRepository->getTimeoutDeliveryOrder($time);
            foreach ($ids as $id) {
                try {
                    $storeOrderRepository->takeOrder($id);
                    Queue::push(OrderReplyJob::class, $id);
                } catch (\Exception $e) {
                    Log::error('自动收货失败:' . $e->getMessage());
                }
            }
        });
    }
}