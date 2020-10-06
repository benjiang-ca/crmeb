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
use app\common\model\system\merchant\Merchant;
use app\common\model\user\User;

class StoreRefundOrder extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'refund_order_id';
    }

    public static function tableName(): string
    {
        return 'store_refund_order';
    }

    public function getPicsAttr($val)
    {
        return $val ? explode(',', $val) : [];
    }

    public function setPicsAttr($val)
    {
        return $val ? implode(',', $val) : '';
    }

    public function refundProduct()
    {
        return $this->hasMany(StoreRefundProduct::class, 'refund_order_id', 'refund_order_id');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'mer_id', 'mer_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    public function order()
    {
        return $this->hasOne(StoreOrder::class, 'order_id', 'order_id');
    }

    public function searchDataAttr($query, $value)
    {
        return getModelTime($query, $value);
    }

    public function getAutoRefundTimeAttr()
    {
        $merAgree = systemConfig('mer_refund_order_agree') ?: 7;
        return strtotime('+' . $merAgree . ' day', strtotime($this->status_time));
    }
}
