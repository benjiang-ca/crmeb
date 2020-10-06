<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/12
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\order;


use app\common\model\BaseModel;

class StoreRefundProduct extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'refund_product_id';
    }

    public static function tableName(): string
    {
        return 'store_refund_product';
    }

    public function product()
    {
        return $this->hasOne(StoreOrderProduct::class,'order_product_id','order_product_id');
    }

    public function refundOrder()
    {
        return $this->hasOne(StoreRefundOrder::class,'refund_order_id','refund_order_id');
    }
}
