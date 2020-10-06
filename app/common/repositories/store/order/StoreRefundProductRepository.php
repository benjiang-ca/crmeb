<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/12
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreRefundProductDao;
use app\common\repositories\BaseRepository;

/**
 * Class StoreRefundProductRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/12
 * @mixin StoreRefundProductDao
 */
class StoreRefundProductRepository extends BaseRepository
{
    /**
     * StoreRefundProductRepository constructor.
     * @param StoreRefundProductDao $dao
     */
    public function __construct(StoreRefundProductDao $dao)
    {
        $this->dao = $dao;
    }
}