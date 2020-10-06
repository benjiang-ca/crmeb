<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/8
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\order;


use app\common\model\BaseModel;
use app\common\model\user\User;
use app\common\repositories\store\coupon\StoreCouponRepository;

class StoreGroupOrder extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'group_order_id';
    }

    public static function tableName(): string
    {
        return 'store_group_order';
    }

    public function orderList()
    {
        return $this->hasMany(StoreOrder::class, 'group_order_id', 'group_order_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    public function getGiveCouponAttr()
    {
        if (count($this->give_coupon_ids))
            return app()->make(StoreCouponRepository::class)->getGiveCoupon($this->give_coupon_ids);
        return [];
    }

    public function getCancelTimeAttr()
    {
        $timer = ((int)systemConfig('auto_close_order_timer')) ?: 15;
        return date('m-d H:i', strtotime("+ $timer minutes", strtotime($this->create_time)));
    }

    public function getGiveCouponIdsAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }
}