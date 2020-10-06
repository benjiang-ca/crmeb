<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-14
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\coupon;


use app\common\model\BaseModel;
use app\common\model\user\User;

/**
 * Class StoreCouponUser
 * @package app\common\model\store\coupon
 * @author xaboy
 * @day 2020-05-14
 */
class StoreCouponUser extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'coupon_user_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_coupon_user';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    public function coupon()
    {
        return $this->hasOne(StoreCoupon::class, 'coupon_id', 'coupon_id');
    }

    public function product()
    {
        return $this->hasMany(StoreCouponProduct::class, 'coupon_id', 'coupon_id');
    }
}