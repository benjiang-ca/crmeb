<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/11
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\order;


use app\common\model\BaseModel;

class StoreRefundStatus extends BaseModel
{

    public static function tablePk(): ?string
    {
        return null;
    }

    public static function tableName(): string
    {
        return 'store_refund_status';
    }
}