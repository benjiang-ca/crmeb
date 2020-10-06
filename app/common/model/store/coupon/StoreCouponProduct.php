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

class StoreCouponProduct extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk():? string
    {
        return null;
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_coupon_product';
    }
}