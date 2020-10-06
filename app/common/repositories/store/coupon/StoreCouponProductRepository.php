<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\coupon;


use app\common\dao\store\coupon\StoreCouponProductDao;
use app\common\repositories\BaseRepository;

/**
 * Class StoreCouponProductRepository
 * @package app\common\repositories\store\coupon
 * @author xaboy
 * @day 2020/6/1
 * @mixin StoreCouponProductDao
 */
class StoreCouponProductRepository extends BaseRepository
{

    /**
     * StoreCouponProductRepository constructor.
     * @param StoreCouponProductDao $dao
     */
    public function __construct(StoreCouponProductDao $dao)
    {
        $this->dao = $dao;
    }
}