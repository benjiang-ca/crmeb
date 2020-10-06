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

class StoreOrderProduct extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'order_product_id';
    }

    public static function tableName(): string
    {
        return 'store_order_product';
    }

    public function getCartInfoAttr($value)
    {
        return json_decode($value, true);
    }

    public function orderInfo()
    {
        return $this->hasOne(StoreOrder::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'uid','uid');
    }
}
