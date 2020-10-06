<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-15
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\admin\order;

use crmeb\basic\BaseController;
use app\common\repositories\store\order\MerchantReconciliationorderRepository;
use app\common\repositories\store\order\MerchantReconciliationRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use app\common\repositories\store\order\StoreRefundOrderRepository as repository;
use think\App;

class RefundOrder extends BaseController
{
    protected $repository;

    public function __construct(App $app,repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }


    public function lst($id)
    {
        [$page,$limit] = $this->getPage();
        $where['reconciliation_type'] = $this->request->param('status',1);
        $where['date'] = $this->request->param('date');
        $where['mer_id'] = $id;
        $where['status'] = 3;
        return app('json')->success($this->repository->getAdminList($where,$page,$limit));
    }

    public function markForm($id)
    {
        if(!$this->repository->getWhereCount([$this->repository->getPk() => $id]))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->adminMarkForm($id)));
    }

    public function mark($id)
    {
        if(!$this->repository->getWhereCount([$this->repository->getPk() => $id]))
            return app('json')->fail('数据不存在');
        $data = $this->request->params(['admin_mark']);
        $this->repository->update($id,$data);

        return app('json')->success('备注成功');
    }


    public function getAllList()
    {
        [$page,$limit] = $this->getPage();
        $where = $this->request->params(['refund_order_sn','status','refund_type','date','mer_id','order_sn','is_trader']);
        return app('json')->success($this->repository->getAllList($where, $page, $limit));
    }

    public function reList($id)
    {
        [$page,$limit] = $this->getPage();
        $where = ['reconciliation_id' => $id,'type' => 1];
        return app('json')->success($this->repository->reconList($where,$page,$limit));

    }
}
