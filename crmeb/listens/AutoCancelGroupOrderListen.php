<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/9
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\store\order\StoreGroupOrderRepository;
use crmeb\interfaces\ListenerInterface;
use Swoole\Timer;
use think\facade\Log;

class AutoCancelGroupOrderListen implements ListenerInterface
{

    public function handle($event): void
    {
        $storeGroupOrderRepository = app()->make(StoreGroupOrderRepository::class);
        Timer::tick(60000, function () use ($storeGroupOrderRepository) {
            $timer = ((int)systemConfig('auto_close_order_timer')) ?: 15;
            $time = date('Y-m-d H:i:s', strtotime("- $timer minutes"));
            $groupOrderIds = $storeGroupOrderRepository->getTimeOutIds($time);
            foreach ($groupOrderIds as $id) {
                try {
                    $storeGroupOrderRepository->cancel($id);
                } catch (\Exception $e) {
                    Log::info('自动关闭订单失败' . var_export($id, 1));
                }
            }
        });
    }
}