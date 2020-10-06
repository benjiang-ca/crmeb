<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\coupon;


use app\common\model\BaseModel;
use app\common\model\user\User;

class StoreCouponIssueUser extends BaseModel
{

    public static function tablePk(): ?string
    {
        return null;
    }

    public static function tableName(): string
    {
        return 'store_coupon_issue_user';
    }

    public function coupon()
    {
        return $this->hasOne(StoreCoupon::class, 'coupon_id', 'coupon_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }
}