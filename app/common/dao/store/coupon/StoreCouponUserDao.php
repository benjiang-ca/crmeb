<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-14
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\coupon;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\coupon\StoreCouponUser;

/**
 * Class StoreCouponUserDao
 * @package app\common\dao\store\coupon
 * @author xaboy
 * @day 2020-05-14
 */
class StoreCouponUserDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return StoreCouponUser::class;
    }

    public function search(array $where)
    {
        return StoreCouponUser::when(isset($where['username']) && $where['username'] !== '', function ($query) use ($where) {
            $query->hasWhere('user', [['nickname', 'LIKE', "%{$where['username']}%"]]);
        })->alias('StoreCouponUser')->when(isset($where['coupon']) && $where['coupon'] !== '', function ($query) use ($where) {
            $query->whereLike('StoreCouponUser.coupon_title', "%{$where['coupon']}%");
        })->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
            $query->where('StoreCouponUser.status', $where['status']);
        })->when(isset($where['uid']) && $where['uid'] !== '', function ($query) use ($where) {
            $query->where('StoreCouponUser.uid', $where['uid']);
        })->when(isset($where['mer_id']) && $where['mer_id'] !== '', function ($query) use ($where) {
            $query->where('StoreCouponUser.mer_id', $where['mer_id']);
        })->when(isset($where['coupon_id']) && $where['coupon_id'] !== '', function ($query) use ($where) {
            $query->where('StoreCouponUser.coupon_id', $where['coupon_id']);
        })->when(isset($where['statusTag']) && $where['statusTag'] !== '', function ($query) use ($where) {
            if ($where['statusTag'] == 1) {
                $query->where('StoreCouponUser.status', 0);
            } else {
                $query->whereIn('StoreCouponUser.status', [1, 2])->where('StoreCouponUser.create_time', '>', date('Y-m-d H:i:s', strtotime('-60 day')));
            }
        })->order('StoreCouponUser.coupon_user_id DESC');
    }

    public function validIntersection($merId, $uid, array $ids): array
    {
        $time = date('Y-m-d H:i:s');
        return StoreCouponUser::getDB()->whereIn('coupon_user_id', $ids)->where('start_time', '<', $time)->where('end_time', '>', $time)
            ->where('is_fail', 0)->where('status', 0)->where('mer_id', $merId)->where('uid', $uid)->column('coupon_user_id');
    }

    public function validQuery()
    {
        $time = date('Y-m-d H:i:s');
        return StoreCouponUser::getDB()->where('start_time', '<', $time)->where('end_time', '>', $time)->where('is_fail', 0)->where('status', 0);
    }

    public function failCoupon()
    {
        $time = date('Y-m-d H:i:s');
        return StoreCouponUser::getDB()->where('end_time', '<', $time)->where('is_fail', 0)->where('status', 0)->update(['status' => 2]);
    }

    public function userTotal($uid)
    {
        return $this->validQuery()->where('uid', $uid)->count();
    }

    public function usedNum($couponId)
    {
        return StoreCouponUser::getDB()->where('coupon_id', $couponId)->where('status', 1)->count();
    }

    public function sendNum($couponId)
    {
        return StoreCouponUser::getDB()->where('coupon_id', $couponId)->count();
    }
}