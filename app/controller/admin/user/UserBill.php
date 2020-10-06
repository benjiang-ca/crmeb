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
use app\common\repositories\user\UserBillRepository;
use think\App;

class UserBill extends BaseController
{
    protected $repository;

    public function __construct(App $app, UserBillRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function getList()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['keyword', 'date', 'type']);
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    public function type()
    {
        return app('json')->success($this->repository->type());
    }
}