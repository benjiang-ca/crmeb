<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-12
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\merchant\store\order;

use app\common\repositories\store\order\MerchantReconciliationRepository;
use app\common\repositories\store\order\StoreRefundStatusRepository;
use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\order\StoreRefundOrderRepository as repository;

class RefundOrder extends BaseController
{
    /**
     * @var repository
     */
    protected $repository;


    /**
     * Order constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @return mixed
     * @author Qinii
     * @day 2020-06-12
     */
    public function lst()
    {
        list($page,$limit) = $this->getPage();
        $where = $this->request->params(['refund_order_sn','status','refund_type','date','order_sn']);
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($this->repository->getList($where,$page,$limit));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-12
     */
    public function detail($id)
    {
        if(!$this->repository->getExistsById($this->request->merId(),$id))
            return app('json')->fail('数据不存在');
        return app('json')->success($this->repository->getOne($id));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-12
     */
    public function switchStatus($id)
    {
        if(!$this->repository->getStatusExists($this->request->merId(),$id))
            return app('json')->fail('信息或状态错误');
        $status = ($this->request->param('status') == 1) ? 1 : -1;
        if($status == 1){
            $data = $this->request->params(['mer_delivery_user','mer_delivery_address','phone']);
            $data['status'] = $status;
            $this->repository->agree($id,$data,$this->request->adminId());
        }else{
            $fail_message = $this->request->param('fail_message','');
            if($status == -1 && empty($fail_message))
                return app('json')->fail('未通过必须填写');
            $data['status'] = $status;
            $data['fail_message'] = $fail_message;
            $this->repository->refuse($id,$data);
        }
        return app('json')->success('审核成功');
    }

    /**
     * TODO 收货后确定退款
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-12
     */
    public function refundPrice($id)
    {
        if(!$this->repository->getRefundPriceExists($this->request->merId(),$id))
            return app('json')->fail('信息或状态错误');
        $this->repository->adminRefund($id,$this->request->adminId());
        return app('json')->success('退款成功');
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-12
     */
    public function switchStatusForm($id)
    {
        if(!$this->repository->getStatusExists($this->request->merId(),$id))
            return app('json')->fail('信息或状态错误');
        return app('json')->success(formToData($this->repository->statusForm($id)));
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-18
     */
    public function delete($id)
    {
        if(!$this->repository->getUserDelExists($this->request->merId(),$id))
            return app('json')->fail('信息或状态错误');
        $this->repository->update($id,['is_system_del' => 1]);
        return app('json')->success('删除成功');
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-18
     */
    public function markForm($id)
    {
        if(!$this->repository->getExistsById($this->request->merId(),$id))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->markForm($id)));
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-18
     */
    public function mark($id)
    {
        if(!$this->repository->getExistsById($this->request->merId(),$id))
            return app('json')->fail('数据不存在');
        $this->repository->update($id,['mer_mark' => $this->request->param('mer_mark','')]);

        return app('json')->success('备注成功');
    }

    public function log($id)
    {
        list($page,$limit) = $this->getPage();
        $make = app()->make(StoreRefundStatusRepository::class);
        return app('json')->success($make->search($id,$page,$limit));
    }

    public function reList($id)
    {
        [$page,$limit] = $this->getPage();
        $make = app()->make(MerchantReconciliationRepository::class);
        if(!$make->getWhereCount(['mer_id' => $this->request->merId(),'reconciliation_id' => $id]))
            return app('json')->fail('数据不存在');
        $where = ['reconciliation_id' => $id,'type' => 1];
        return app('json')->success($this->repository->reconList($where,$page,$limit));
    }

    /**
     * TODO 快递查询
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-25
     */
    public function express($id)
    {
        if(!$this->repository->getWhereCount(['refund_order_id' => $id,'status' =>2]))
            return app('json')->fail('订单信息或状态错误');
        return app('json')->success($this->repository->express($id));
    }
}
