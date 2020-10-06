<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/9/16
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\store\order\StoreOrderRepository;
use crmeb\interfaces\ListenerInterface;
use crmeb\jobs\OrderReplyJob;
use Swoole\Timer;
use think\facade\Queue;

class AutoOrderReplyListen implements ListenerInterface
{

    public function handle($event): void
    {
        $storeOrderRepository = app()->make(StoreOrderRepository::class);

        Timer::tick(1000 * 60 * 60, function () use ($storeOrderRepository) {
            $time = date('Y-m-d H:i:s', strtotime('- 7 day'));
            $ids = $storeOrderRepository->getFinishTimeoutIds($time);
            foreach ($ids as $id) {
                Queue::push(OrderReplyJob::class, $id);
            }
        });
    }
}