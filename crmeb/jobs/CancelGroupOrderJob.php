<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/9
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\store\coupon\StoreCouponUserRepository;
use app\common\repositories\store\order\StoreGroupOrderRepository;
use app\common\repositories\store\product\ProductAttrValueRepository;
use app\common\repositories\store\product\ProductRepository;
use crmeb\interfaces\JobInterface;
use think\facade\Db;
use think\facade\Log;

class CancelGroupOrderJob implements JobInterface
{

    public function fire($job, $groupOrderId)
    {
        if (($attempts = $job->attempts()) == 1) {
            return $job->release(30);
        } else if ($attempts > 2) {
            return $job->delete();
        }

        $groupOrderRepository = app()->make(StoreGroupOrderRepository::class);
        $groupOrder = $groupOrderRepository->getCancelDetail($groupOrderId);
        if (!$groupOrder) return $job->delete();
        Db::transaction(function () use ($groupOrder) {
            $couponId = [];
            $productRepository = app()->make(ProductRepository::class);
            foreach ($groupOrder->orderList as $order) {
                if ($order->coupon_id)
                    $couponId = array_merge($couponId, explode(',', $order->coupon_id));
                foreach ($order->orderProduct as $cart) {
                    $productRepository->orderProductIncStock($cart);
                }
            }
            if (count($couponId)) {
                app()->make(StoreCouponUserRepository::class)->updates($couponId, ['status' => 0]);
            }
        });
        return $job->delete();
    }

    public function failed($data)
    {
        Log::info('取消订单执行失败:' . var_export($data, true));
    }
}