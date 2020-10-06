<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/31
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api\store\broadcast;


use app\common\repositories\store\broadcast\BroadcastRoomRepository;
use crmeb\basic\BaseController;
use think\App;

class BroadcastRoom extends BaseController
{
    /**
     * @var BroadcastRoomRepository
     */
    protected $repository;

    public function __construct(App $app, BroadcastRoomRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function lst()
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->repository->userList([], $page, $limit));
    }

    public function hot()
    {
        [$page, $limit] = $this->getPage();
        $where = ['hot' => 1];
        return app('json')->success($this->repository->userList($where, $page, $limit));
    }
}