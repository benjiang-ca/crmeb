<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\coupon;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\coupon\StoreCouponIssueUser;
use think\db\BaseQuery;

/**
 * Class StoreCouponIssueUserDao
 * @package app\common\dao\store\coupon
 * @author xaboy
 * @day 2020/6/2
 */
class StoreCouponIssueUserDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/2
     */
    protected function getModel(): string
    {
        return StoreCouponIssueUser::class;
    }

    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020/6/2
     */
    public function search(array $where)
    {
        return StoreCouponIssueUser::getDB()->when(isset($where['coupon_id']) && $where['coupon_id'] != '', function ($query) use ($where) {
            $query->where('coupon_id', $where['coupon_id']);
        })->when(isset($where['uid']) && $where['uid'] != '', function ($query) use ($where) {
            $query->where('uid', $where['uid']);
        })->order('create_time');
    }
}