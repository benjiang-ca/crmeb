<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-15
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\store\order;

use app\common\model\BaseModel;

class MerchantReconciliationOrder extends BaseModel
{
    public static function tablePk(): ?string
    {
        return '';
    }

    public static function tableName(): string
    {
        return 'merchant_reconciliation_order';
    }

    public function Order()
    {
        return $this->hasOne(StoreOrder::class,'order_id','order_id');
    }

    public function refund()
    {
        return $this->hasOne(StoreRefundOrder::class,'refund_order_id','order_id');
    }
}
