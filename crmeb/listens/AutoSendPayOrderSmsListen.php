<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\store\order\StoreGroupOrderRepository;
use crmeb\interfaces\ListenerInterface;
use crmeb\jobs\SendSmsJob;
use Swoole\Timer;
use think\facade\Queue;

class AutoSendPayOrderSmsListen implements ListenerInterface
{

    public function handle($event): void
    {
        $storeGroupOrderRepository = app()->make(StoreGroupOrderRepository::class);
        Timer::tick(1000 * 60 * 5, function () use ($storeGroupOrderRepository) {
            $time = date('Y-m-d H:i:s', strtotime("- 10 minutes"));
            $groupOrderIds = $storeGroupOrderRepository->getTimeOutIds($time, true);
            foreach ($groupOrderIds as $id) {
                Queue::push(SendSmsJob::class, [
                    'tempId' => 'ORDER_PAY_FALSE',
                    'id' => $id
                ]);
                $storeGroupOrderRepository->isRemind($id);
            }
        });
    }
}