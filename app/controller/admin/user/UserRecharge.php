<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/23
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\user;


use crmeb\basic\BaseController;
use app\common\repositories\user\UserRechargeRepository;
use think\App;

class UserRecharge extends BaseController
{

    protected $repository;

    public function __construct(App $app, UserRechargeRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function getList()
    {
        $where = $this->request->params(['date', 'paid', 'keyword']);
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    public function total()
    {
        $totalRefundPrice = $this->repository->totalRefundPrice();
        $totalPayPrice = $this->repository->totalPayPrice();
        $totalRoutinePrice = $this->repository->totalRoutinePrice();
        $totalWxPrice = $this->repository->totalWxPrice();
        return app('json')->success(compact('totalWxPrice', 'totalRoutinePrice', 'totalPayPrice', 'totalRefundPrice'));
    }
}