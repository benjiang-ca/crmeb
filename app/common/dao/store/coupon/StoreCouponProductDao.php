<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\coupon;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\coupon\StoreCouponProduct;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class StoreCouponProductDao
 * @package app\common\dao\store\coupon
 * @author xaboy
 * @day 2020-05-13
 */
class StoreCouponProductDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return StoreCouponProduct::class;
    }

    /**
     * @param array $data
     * @return int
     * @author xaboy
     * @day 2020-05-13
     */
    public function insertAll(array $data)
    {
        return StoreCouponProduct::getDB()->insertAll($data);
    }

    /**
     * @param $couponId
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-05-13
     */
    public function clear($couponId)
    {
        return StoreCouponProduct::getDB()->where('coupon_id', $couponId)->delete();
    }

    /**
     * @param $productId
     * @return array
     * @author xaboy
     * @day 2020/6/1
     */
    public function productByCouponId($productId)
    {
        return StoreCouponProduct::getDB()->whereIn('product_id', $productId)->column('coupon_id');
    }
}