<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\service;


use app\common\model\BaseModel;
use app\common\model\store\order\StoreOrder;
use app\common\model\store\order\StoreRefundOrder;
use app\common\model\store\product\Product;
use app\common\model\system\merchant\Merchant;
use app\common\model\user\User;

class StoreServiceLog extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'service_log_id';
    }

    public static function tableName(): string
    {
        return 'store_service_log';
    }

    public function orderInfo()
    {
        return $this->hasOne(StoreOrder::class, 'order_id', 'msn')->with('orderProduct');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'msn');
    }

    public function refundOrder()
    {
        return $this->hasOne(StoreRefundOrder::class, 'refund_order_id', 'msn')->with('refundProduct.product');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid')->field('uid,avatar,nickname');
    }

    public function service()
    {
        return $this->hasOne(StoreService::class, 'service_id', 'service_id')->field('service_id,avatar,nickname');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'mer_id', 'mer_id');
    }
}