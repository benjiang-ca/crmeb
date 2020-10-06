<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-14
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\coupon;


use app\common\dao\store\coupon\StoreCouponUserDao;
use app\common\repositories\BaseRepository;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class StoreCouponUserRepository
 * @package app\common\repositories\store\coupon
 * @author xaboy
 * @day 2020-05-14
 * @mixin  StoreCouponUserDao
 */
class StoreCouponUserRepository extends BaseRepository
{
    /**
     * @var StoreCouponUserDao
     */
    protected $dao;

    /**
     * StoreCouponUserRepository constructor.
     * @param StoreCouponUserDao $dao
     */
    public function __construct(StoreCouponUserDao $dao)
    {
        $this->dao = $dao;
        //TODO 检查过期优惠券
        $dao->failCoupon();
    }

    /**
     * @param $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/3
     */
    public function userList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->with(['coupon' => function ($query) {
            $query->field('coupon_id,type');
        }])->page($page, $limit)->select();
        return compact('count', 'list');
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/3
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->with(['user' => function ($query) {
            $query->field('avatar,uid,nickname');
        }])->page($page, $limit)->select();
        return compact('count', 'list');
    }

}