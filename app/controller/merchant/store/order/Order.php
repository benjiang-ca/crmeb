<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-10
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\controller\merchant\store\order;

use app\common\repositories\store\ExcelRepository;
use app\common\repositories\store\order\MerchantReconciliationRepository;
use crmeb\exceptions\UploadException;
use think\App;
use crmeb\basic\BaseController;
use app\common\repositories\store\order\StoreOrderRepository as repository;

class Order extends BaseController
{
    protected $repository;

    /**
     * Product constructor.
     * @param App $app
     * @param repository $repository
     */
    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }


    /**
     * 订单列表
     * @return mixed
     * @author Qinii
     */
    public function lst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['status', 'date', 'order_sn','username','order_type','keywords']);
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($this->repository->merchantGetList($where, $page, $limit));
    }

    /**
     * TODO 自提订单列表
     * @return mixed
     * @author Qinii
     * @day 2020-08-17
     */
    public function takeLst()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['date', 'order_sn', 'username','keywords']);
        $where['take_order'] = 1;
        $where['status'] = -1;
        $where['verify_date'] = $where['date'];
        unset($where['date']);
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($this->repository->merchantGetList($where, $page, $limit));
    }

    /**
     *  订单头部统计
     * @return mixed
     * @author Qinii
     */
    public function chart()
    {
        return app('json')->success($this->repository->OrderTitleNumber($this->request->merId(),null));
    }

    /**
     * TODO 自提订单头部统计
     * @return mixed
     * @author Qinii
     * @day 2020-08-17
     */
    public function takeChart()
    {
        return app('json')->success($this->repository->OrderTitleNumber($this->request->merId(),1));
    }


    /**
     * TODO 订单类型
     * @return mixed
     * @author Qinii
     * @day 2020-08-15
     */
    public function orderType()
    {
        $where['mer_id'] = $this->request->merId();
        return app('json')->success($this->repository->orderType($where));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function deliveryForm($id)
    {
        if(!$this->repository->merDeliveryExists($id,$this->request->merId()))
            return app('json')->fail('订单信息或状态错误');
        return app('json')->success(formToData($this->repository->sendProductForm($id)));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function delivery($id)
    {
        if(!$this->repository->merDeliveryExists($id,$this->request->merId()))
           return app('json')->fail('订单信息或状态错误');
        $data = $this->request->params(['delivery_type','delivery_name','delivery_id']);
        if(preg_match('/([\x81-\xfe][\x40-\xfe])/',$data['delivery_id'])) return app('json')->fail('请输入正确的单号/电话');
        $this->repository->delivery($id,$data);
        return app('json')->success('发货成功');
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function updateForm($id)
    {
        if(!$this->repository->merStatusExists($id,$this->request->merId()))
            return app('json')->fail('订单信息或状态错误');
        return app('json')->success(formToData($this->repository->form($id)));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function update($id)
    {
        $data = $this->request->params(['total_price','pay_price','total_postage','pay_postage']);
        if($data['total_price'] < 0 || $data['pay_price'] < 0 || $data['total_postage'] < 0 || $data['pay_postage'] < 0 )
            return app('json')->fail('金额不可未负数');
        if(!$this->repository->merStatusExists($id,$this->request->merId()))
            return app('json')->fail('订单信息或状态错误');
        $this->repository->eidt($id,$data);
        return app('json')->success('修改成功');
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function detail($id)
    {
        $data = $this->repository->getOne($id,$this->request->merId());
        if(!$data)
            return app('json')->fail('数据不存在');
        return app('json')->success($data);
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function status($id)
    {
        [$page, $limit] = $this->getPage();
        if(!$this->repository->getOne($id,$this->request->merId()))
            return app('json')->fail('数据不存在');
        return app('json')->success($this->repository->getOrderStatus($id,$page, $limit));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function remarkForm($id)
    {
        return app('json')->success(formToData($this->repository->remarkForm($id)));
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function remark($id)
    {
        if(!$this->repository->getOne($id,$this->request->merId()))
            return app('json')->fail('数据不存在');
        $data = $this->request->params(['remark']);
        $this->repository->update($id,$data);

        return app('json')->success('备注成功');
    }

    /**
     * 核销
     * @param $code
     * @author xaboy
     * @day 2020/8/15
     */
    public function verify($code)
    {
        $this->repository->verifyOrder($code, $this->request->merId(), 0);
        return app('json')->success('订单核销成功');
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-06-11
     */
    public function delete($id)
    {
        if(!$this->repository->userDelExists($id,$this->request->merId()))
            return app('json')->fail('订单信息或状态错误');
        $data['is_system_del'] = 1;
        $this->repository->update($id,$data);
        return app('json')->success('删除成功');
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
        if(!$this->repository->getWhereCount(['order_id' => $id,'mer_id' => $this->request->merId(),'delivery_type' =>1]))
            return app('json')->fail('订单信息或状态错误');
        return app('json')->success($this->repository->express($id));
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-07-30
     */
    public function reList($id)
    {
        [$page,$limit] = $this->getPage();
        $make = app()->make(MerchantReconciliationRepository::class);
        if(!$make->getWhereCount(['mer_id' => $this->request->merId(),'reconciliation_id' => $id]))
            return app('json')->fail('数据不存在');
        $where = ['reconciliation_id' => $id,'type' => 0];
        return app('json')->success($this->repository->reconList($where,$page,$limit));
    }

    /**
     * TODO 导出文件
     * @author Qinii
     * @day 2020-07-30
     */
    public function excel()
    {
        $where = $this->request->params(['status', 'date', 'order_sn','order_type','username','keywords','take_order']);
        if($where['take_order']){
            $where['status'] = -1;
            $where['verify_date'] = $where['date'];
            unset($where['date']);
            unset($where['order_type']);
        }
        $where['mer_id'] = $this->request->merId();
        app()->make(ExcelRepository::class)->create($where,$this->request->adminId(),'order',$this->request->merId());
        return app('json')->success('开始导出数据');
    }

    /**
     * TODO 打印小票
     * @param $id
     * @return mixed
     * @author Qinii
     * @day 2020-07-30
     */
    public function printer($id)
    {
        $merId = $this->request->merId();
        $this->repository->checkPrinterConfig($merId);
        if(!$this->repository->getOne($id,$merId))
            return app('json')->fail('数据不存在');
        $this->repository->printer($id,$merId);
        return app('json')->success('打印成功');
    }

}

