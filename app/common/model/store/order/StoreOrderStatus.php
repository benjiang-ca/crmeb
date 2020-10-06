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

/**
 * Class StoreOrderStatus
 * @package app\common\model\store\order
 * @author xaboy
 * @day 2020/6/12
 */
class StoreOrderStatus extends BaseModel
{

    /**
     * @return string|null
     * @author xaboy
     * @day 2020/6/12
     */
    public static function tablePk(): ?string
    {
        return null;
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/12
     */
    public static function tableName(): string
    {
        return 'store_order_status';
    }
}