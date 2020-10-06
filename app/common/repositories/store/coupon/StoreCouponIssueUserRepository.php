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


use app\common\dao\store\coupon\StoreCouponIssueUserDao;
use app\common\repositories\BaseRepository;

/**
 * Class StoreCouponIssueUserRepository
 * @package app\common\repositories\store\coupon
 * @author xaboy
 * @day 2020/6/1
 * @mixin StoreCouponIssueUserDao
 */
class StoreCouponIssueUserRepository extends BaseRepository
{
    /**
     * StoreCouponIssueUserRepository constructor.
     * @param StoreCouponIssueUserDao $dao
     */
    public function __construct(StoreCouponIssueUserDao $dao)
    {
        $this->dao = $dao;
    }

    public function issue($couponId, $uid)
    {
        return $this->dao->create([
            'coupon_id' => $couponId,
            'uid' => $uid,
        ]);
    }

    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->with(['coupon', 'user'])->page($page, $limit)->select();
        return compact('count', 'list');
    }
}