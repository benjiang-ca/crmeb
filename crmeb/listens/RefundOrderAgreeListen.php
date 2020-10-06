<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;

use Swoole\Timer;
use think\facade\Log;
use crmeb\interfaces\ListenerInterface;
use app\common\repositories\store\order\StoreRefundOrderRepository;

class RefundOrderAgreeListen implements ListenerInterface
{
    public function handle($event): void
    {
        $make = app()->make(StoreRefundOrderRepository::class);
        Timer::tick(1000 * 60 * 5, function () use ($make) {
            $merAgree = systemConfig('mer_refund_order_agree') ?: 7;
            $time = date('Y-m-d H:i:s', strtotime('-' . $merAgree . ' day'));
            $data = $make->getTimeOutIds($time);
            foreach ($data as $id) {
                try {
                    $make->adminRefund($id, 0);
                } catch (\Exception $e) {
                    Log::info('自动退款失败' . var_export($id, true));
                }
            }
        });
    }
}
