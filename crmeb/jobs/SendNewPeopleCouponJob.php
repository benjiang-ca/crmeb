<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/18
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\store\coupon\StoreCouponRepository;
use app\common\repositories\store\coupon\StoreCouponUserRepository;
use app\common\repositories\user\UserRepository;
use crmeb\interfaces\JobInterface;
use think\facade\Log;

class SendNewPeopleCouponJob implements JobInterface
{

    public function fire($job, $uid)
    {
        if (!app()->make(UserRepository::class)->exists($uid))
            return $job->delete();

        $storeCouponRepository = app()->make(StoreCouponRepository::class);
        $newPeopleCoupon = $storeCouponRepository->newPeopleCoupon();
        foreach ($newPeopleCoupon as $coupon) {
            if ($coupon->is_limited && 0 == $coupon->remain_count)
                continue;
            try {
                $storeCouponRepository->sendCoupon($coupon, $uid, 'new');
            } catch (\Exception $e) {
                Log::info('自定发放优惠券:' . $e->getMessage());
            }
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}