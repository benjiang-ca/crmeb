<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/20
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api\user;


use app\common\repositories\store\order\StoreOrderRepository;
use app\controller\merchant\Common;
use crmeb\basic\BaseController;
use think\App;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Db;
use think\response\Json;

class Admin extends BaseController
{
    protected $merId;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $user = $this->request->userInfo();
        if (isset($user->service->customer) && $user->service->customer == 1) {
            $this->merId = $user->service->mer_id;
        } else {
            throw new HttpResponseException(app('json')->fail('没有权限'));
        }
    }

    public function orderStatistics(StoreOrderRepository $repository)
    {
        $order = $repository->OrderTitleNumber($this->merId, null);
        $common = app()->make(Common::class);
        $data = [];
        $data['today'] = $common->mainGroup('today', $this->merId);
        $data['yesterday'] = $common->mainGroup('yesterday', $this->merId);
        $data['month'] = $common->mainGroup('month', $this->merId);
        return app('json')->success(compact('order', 'data'));
    }

    public function orderDetail(StoreOrderRepository $repository)
    {
        [$page, $limit] = $this->getPage();
        list($start, $stop) = $this->request->params([
            ['start', strtotime(date('Y-m'))],
            ['stop', time()],
        ], true);
        if ($start == $stop) return app('json')->fail('参数有误');
        if ($start > $stop) {
            $middle = $stop;
            $stop = $start;
            $start = $middle;
        }
        $where = $this->request->has('start') ? ['dateRange' => compact('start', 'stop')] : [];
        $list = $repository->orderGroupNumPage($where, $page, $limit, $this->merId);
        return app('json')->success($list);
    }

    public function orderList(StoreOrderRepository $repository)
    {
        [$page, $limit] = $this->getPage();
        $where = $this->request->params(['status']);
        $where['mer_id'] = $this->merId;
        $where['is_del'] = 0;
        return app('json')->success($repository->merchantGetList($where, $page, $limit));
    }

    public function order($id, StoreOrderRepository $repository)
    {
        $detail = $repository->getDetail($id);
        if (!$detail)
            return app('json')->fail('订单不存在');
        if ($detail['mer_id'] != $this->merId)
            return app('json')->fail('没有权限');
        return app('json')->success($detail->toArray());
    }


    protected function checkOrderAuth($id)
    {
        if (!app()->make(StoreOrderRepository::class)->existsWhere(['mer_id' => $this->merId, 'order_id' => $id]))
            throw new ValidateException('没有权限');
    }

    public function mark($id, StoreOrderRepository $repository)
    {
        $this->checkOrderAuth($id);
        $data = $this->request->params(['remark']);
        $repository->update($id, $data);
        return app('json')->success('备注成功');
    }

    public function price($id, StoreOrderRepository $repository)
    {
        $this->checkOrderAuth($id);
        $data = $this->request->params(['pay_price']);
        if ($data['pay_price'] < 0)
            app('json')->fail('支付金额不能小于0');
        if (!$repository->merStatusExists((int)$id, $this->merId))
            return app('json')->fail('订单信息或状态错误');
        $repository->eidt($id, $data);
        return app('json')->success('修改成功');
    }

    public function delivery($id, StoreOrderRepository $repository)
    {
        $this->checkOrderAuth($id);
        if (!$repository->merDeliveryExists((int)$id, $this->merId))
            return app('json')->fail('订单信息或状态错误');
        $data = $this->request->params(['delivery_type', 'delivery_name', 'delivery_id']);
        $repository->delivery($id, $data);
        return app('json')->success('发货成功');
    }

    public function payPrice(StoreOrderRepository $repository)
    {
        list($start, $stop) = $this->request->params([
            ['start', strtotime(date('Y-m'))],
            ['stop', time()]
        ], true);

        if ($start == $stop) return app('json')->fail('参数有误');
        if ($start > $stop) {
            $middle = $stop;
            $stop = $start;
            $start = $middle;
        }
        $space = bcsub($stop, $start, 0);//间隔时间段
        $front = bcsub($start, $space, 0);//第一个时间段

        $front = date('Y/m/d H:i:s', $front);
        $start = date('Y/m/d H:i:s', $start);
        $stop = date('Y/m/d H:i:s', $stop);
        $frontPrice = $repository->dateOrderPrice($front . '-' . date('Y/m/d H:i:s', strtotime($start . '-1 day')), $this->merId);
        $afterPrice = $repository->dateOrderPrice($start . '-' . date('Y/m/d H:i:s', strtotime($stop . '-1 day')), $this->merId);
        $chartInfo = $repository->chartTimePrice($start, date('Y/m/d H:i:s', strtotime($stop . '-1 day')), $this->merId);
        $data['chart'] = $chartInfo;//营业额图表数据
        $data['time'] = $afterPrice;//时间区间营业额
        $increase = (float)bcsub((string)$afterPrice, (string)$frontPrice, 2); //同比上个时间区间增长营业额
        $growthRate = abs($increase);
        if ($growthRate == 0) $data['growth_rate'] = 0;
        else if ($frontPrice == 0) $data['growth_rate'] = $growthRate;
        else $data['growth_rate'] = (int)bcmul((string)bcdiv((string)$growthRate, (string)$frontPrice, 2), '100', 0);//时间区间增长率
        $data['increase_time'] = abs($increase); //同比上个时间区间增长营业额
        $data['increase_time_status'] = $increase >= 0 ? 1 : 2; //同比上个时间区间增长营业额增长 1 减少 2

        return app('json')->success($data);
    }

    /**
     * @param StoreOrderRepository $repository
     * @return Json
     * @author xaboy
     * @day 2020/8/27
     */
    public function payNumber(StoreOrderRepository $repository)
    {
        list($start, $stop) = $this->request->params([
            ['start', strtotime(date('Y-m'))],
            ['stop', time()]
        ], true);

        if ($start == $stop) return app('json')->fail('参数有误');
        if ($start > $stop) {
            $middle = $stop;
            $stop = $start;
            $start = $middle;
        }
        $space = bcsub($stop, $start, 0);//间隔时间段
        $front = bcsub($start, $space, 0);//第一个时间段

        $front = date('Y/m/d H:i:s', $front);
        $start = date('Y/m/d H:i:s', $start);
        $stop = date('Y/m/d H:i:s', $stop);
        $frontNumber = $repository->dateOrderNum($front . '-' . date('Y/m/d H:i:s', strtotime($start . '-1 day')), $this->merId);
        $afterNumber = $repository->dateOrderNum($start . '-' . date('Y/m/d H:i:s', strtotime($stop . '-1 day')), $this->merId);
        $chartInfo = $repository->chartTimeNum($start . '-' . date('Y/m/d H:i:s', strtotime($stop . '-1 day')), $this->merId);
        $data['chart'] = $chartInfo;//订单数图表数据
        $data['time'] = $afterNumber;//时间区间订单数
        $increase = $afterNumber - $frontNumber; //同比上个时间区间增长订单数
        $growthRate = abs($increase);
        if ($growthRate == 0) $data['growth_rate'] = 0;
        else if ($frontNumber == 0) $data['growth_rate'] = $growthRate;
        else $data['growth_rate'] = (int)bcmul((string)bcdiv((string)$growthRate, (string)$frontNumber, 2), '100', 0);//时间区间增长率
        $data['increase_time'] = abs($increase); //同比上个时间区间增长营业额
        $data['increase_time_status'] = $increase >= 0 ? 1 : 2; //同比上个时间区间增长营业额增长 1 减少 2

        return app('json')->success($data);
    }
}