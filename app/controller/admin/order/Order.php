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
use app\common\repositories\store\ExcelRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use app\common\repositories\store\order\StoreOrderRepository as repository;
use think\App;

class Order extends BaseController
{
    protected $repository;

    public function __construct(App $app, repository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }


    public function lst($id)
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['date','order_sn','order_type','keywords','username']);
        $where['reconciliation_type'] = $this->request->param('status', 1);
        $where['mer_id'] = $id;
        return app('json')->success($this->repository->adminMerGetList($where, $page, $limit));
    }

    public function markForm($id)
    {
        if (!$this->repository->getWhereCount([$this->repository->getPk() => $id]))
            return app('json')->fail('数据不存在');
        return app('json')->success(formToData($this->repository->adminMarkForm($id)));
    }

    public function mark($id)
    {
        if (!$this->repository->getWhereCount([$this->repository->getPk() => $id]))
            return app('json')->fail('数据不存在');
        $data = $this->request->params(['admin_mark']);
        $this->repository->update($id, $data);
        return app('json')->success('备注成功');
    }

    /**
     * TODO
     * @return mixed
     * @author Qinii
     * @day 2020-06-25
     */
    public function getAllList()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['type', 'date', 'mer_id','keywords','status','username','order_sn','is_trader']);
        return app('json')->success($this->repository->adminGetList($where, $page, $limit));
    }

    /**
     * TODO 自提订单列表
     * @return mixed
     * @author Qinii
     * @day 2020-08-17
     */
    public function getTakeList()
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['date','order_sn','keywords','username','is_trader']);
        $where['take_order'] = 1;
        $where['status'] = '';
        $where['verify_date'] = $where['date'];
        unset($where['date']);
        return app('json')->success($this->repository->adminGetList($where, $page, $limit));
    }

    /**
     * TODO
     * @return mixed
     * @author Qinii
     * @day 2020-08-17
     */
    public function chart()
    {
        return app('json')->success($this->repository->OrderTitleNumber(null,null));
    }

    /**
     * TODO 自提订单头部统计
     * @return mixed
     * @author Qinii
     * @day 2020-08-17
     */
    public function takeChart()
    {
        return app('json')->success($this->repository->OrderTitleNumber(null,1));
    }

    /**
     * TODO 订单类型
     * @return mixed
     * @author Qinii
     * @day 2020-08-15
     */
    public function orderType()
    {
        return app('json')->success($this->repository->orderType([]));
    }

    public function detail($id)
    {
        $data = $this->repository->getOne($id, null);
        if (!$data)
            return app('json')->fail('数据不存在');
        return app('json')->success($data);
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
        if (!$this->repository->getWhereCount(['order_id' => $id, 'delivery_type' => 1]))
            return app('json')->fail('订单信息或状态错误');
        return app('json')->success($this->repository->express($id));
    }

    public function reList($id)
    {
        [$page, $limit] = $this->getPage();
        $where = ['reconciliation_id' => $id, 'type' => 0];
        return app('json')->success($this->repository->reconList($where, $page, $limit));
    }

    /**
     * TODO 导出文件
     * @author Qinii
     * @day 2020-07-30
     */
    public function excel()
    {
        $where = $this->request->params(['type', 'date', 'mer_id','keywords','status','username','order_sn','take_order']);
        if($where['take_order']){
            $where['verify_date'] = $where['date'];
            unset($where['date']);
        }
        app()->make(ExcelRepository::class)->create($where, $this->request->adminId(), 'order',0);
        return app('json')->success('开始导出数据');

    }
}
