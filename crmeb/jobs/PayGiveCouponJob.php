<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/19
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\store\coupon\StoreCouponRepository;
use crmeb\interfaces\JobInterface;
use think\facade\Log;

class PayGiveCouponJob implements JobInterface
{
    public function fire($job, $data)
    {
        $storeCouponRepository = app()->make(StoreCouponRepository::class);
        $coupons = $storeCouponRepository->getGiveCoupon($data['ids']);
        foreach ($coupons as $coupon) {
            if ($coupon->is_limited && 0 == $coupon->remain_count)
                continue;
            try {
                $storeCouponRepository->sendCoupon($coupon, $data['uid'], 'give');
            } catch (\Exception $e) {
                Log::info('自定发放买赠优惠券:' . $e->getMessage());
            }
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}