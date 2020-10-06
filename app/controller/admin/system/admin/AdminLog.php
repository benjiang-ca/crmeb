<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-16
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\system\admin;


use crmeb\basic\BaseController;
use app\common\repositories\system\admin\AdminLogRepository;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class AdminLog extends BaseController
{
    protected $repository;

    public function __construct(App $app, AdminLogRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-16
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['section_startTime', 'section_endTime', 'admin_id', 'method', 'date']);
        return app('json')->success($this->repository->lst($this->request->merId(), $where, $page, $limit));
    }
}