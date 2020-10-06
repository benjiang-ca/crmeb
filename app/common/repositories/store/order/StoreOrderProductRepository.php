<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/8
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreOrderProductDao;
use app\common\repositories\BaseRepository;

/**
 * Class StoreOrderProductRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/8
 * @mixin StoreOrderProductDao
 */
class StoreOrderProductRepository extends BaseRepository
{
    /**
     * StoreOrderProductRepository constructor.
     * @param StoreOrderProductDao $dao
     */
    public function __construct(StoreOrderProductDao $dao)
    {
        $this->dao = $dao;
    }
}