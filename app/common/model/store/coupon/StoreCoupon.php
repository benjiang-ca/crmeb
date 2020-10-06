<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\coupon;


use app\common\model\BaseModel;
use app\common\model\system\merchant\Merchant;
use app\common\repositories\store\coupon\StoreCouponUserRepository;

class StoreCoupon extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'coupon_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_coupon';
    }

    public function product()
    {
        return $this->hasMany(StoreCouponProduct::class, 'coupon_id', 'coupon_id');
    }

    public function issue()
    {
        return $this->hasOne(StoreCouponIssueUser::class, 'coupon_id', 'coupon_id');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'mer_id', 'mer_id');
    }

    public function getUsedNumAttr()
    {
        return app()->make(StoreCouponUserRepository::class)->usedNum($this->coupon_id);
    }

    public function getSendNumAttr()
    {
        return app()->make(StoreCouponUserRepository::class)->sendNum($this->coupon_id);
    }
}
