<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-19
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\admin\store;

use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\service\StoreServiceLogRepository;
use app\common\repositories\store\service\StoreServiceRepository;

class StoreService extends BaseController
{
    /**
     * @var StoreServiceRepository
     */
    protected $repository;
    /**
     * @var StoreServiceLogRepository
     */
    protected $logRepository;

    /**
     * StoreService constructor.
     * @param App $app
     * @param StoreServiceRepository $repository
     */
    public function __construct(App $app, StoreServiceRepository $repository,StoreServiceLogRepository $logRepository)
    {
        parent::__construct($app);
        $this->repository = $repository;
        $this->logRepository = $logRepository;
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function lst($id)
    {
        $where = $this->request->params(['keyword', 'status']);
        [$page, $limit] = $this->getPage();
        $where['mer_id'] = $id;
        return app('json')->success($this->repository->getList($where, $page, $limit));
    }

    /**
     * TODO
     * @param $uid
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function getUserMsnByMerchant($uid,$id)
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($this->logRepository->getUserMsn($uid, $page, $limit,$id));
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-19
     */
    public function merchantUserList($id)
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success( $this->logRepository->getMerchantUserList($id, $page, $limit));
    }
}
