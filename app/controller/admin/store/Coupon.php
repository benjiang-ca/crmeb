<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\store;

use app\common\repositories\store\coupon\StoreCouponUserRepository;
use think\App;
use crmeb\basic\BaseController;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\common\repositories\store\coupon\StoreCouponRepository;

/**
 * Class CouponIssue
 * @package app\controller\merchant\store\coupon
 * @author xaboy
 * @day 2020-05-13
 */
class Coupon extends BaseController
{
    /**
     * @var StoreCouponRepository
     */
    protected $repository;

    /**
     * CouponIssue constructor.
     * @param App $app
     * @param StoreCouponRepository $repository
     */
    public function __construct(App $app, StoreCouponRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-14
     */
    public function lst()
    {
        $where = $this->request->params(['is_full_give', 'status', 'is_give_subscribe', 'coupon_name', ['mer_id', null],'is_trader']);
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList($where['mer_id'], $where, $page, $limit));
    }

    public function detail($id)
    {
        if (!$this->repository->exists($id))
            return app('json')->fail('数据不存在');
        $coupon = $this->repository->get($id)->append(['used_num', 'send_num']);
        return app('json')->success($coupon->toArray());
    }

    /**
     * @param StoreCouponUserRepository $repository
     * @author xaboy
     * @day 2020/6/2
     */
    public function issue(StoreCouponUserRepository $repository)
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['username', 'coupon_id', 'status', 'mer_id']);
        return app('json')->success($repository->getList($where, $page, $limit));
    }
}
