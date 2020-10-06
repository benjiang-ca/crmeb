<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/8
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreGroupOrderDao;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\coupon\StoreCouponUserRepository;
use crmeb\jobs\CancelGroupOrderJob;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Queue;
use think\model\Relation;

/**
 * Class StoreGroupOrderRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/8
 * @mixin StoreGroupOrderDao
 */
class StoreGroupOrderRepository extends BaseRepository
{
    /**
     * StoreGroupOrderRepository constructor.
     * @param StoreGroupOrderDao $dao
     */
    public function __construct(StoreGroupOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->search($where);
        $count = $query->count();
        $list = $query->with(['orderList' => function (Relation $query) {
            $query->field('order_id,group_order_id')->with('orderProduct');
        }])->page($page, $limit)->order('create_time DESC')->select();
        return compact('count', 'list');
    }

    /**
     * @param $uid
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function detail($uid, $id)
    {
        return $this->search(['paid' => 0, 'uid' => $uid])->where('group_order_id', $id)->with(['orderList' => function (Relation $query) {
            $query->field('order_id,group_order_id,mer_id,order_sn,pay_price')->with(['merchant' => function ($query) {
                $query->field('mer_id,mer_name');
            }, 'orderProduct']);
        }])->find();
    }

    public function status($uid, $id)
    {
        return $this->search(['uid' => $uid])->where('group_order_id', $id)->append(['give_coupon'])->find();
    }

    /**
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function getCancelDetail($id)
    {
        return $this->search(['paid' => 0, 'is_del' => 1])->where('group_order_id', $id)->with(['orderList.orderProduct'])->find();
    }

    /**
     * @param $id
     * @param null $uid
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function cancel($id, $uid = null)
    {
        $groupOrder = $this->search(['paid' => 0, 'uid' => $uid ?? ''])->where('group_order_id', $id)->with(['orderList'])->find();
        if (!$groupOrder)
            throw new ValidateException('订单不存在');
        if ($groupOrder['paid'] != 0)
            throw new ValidateException('订单状态错误,无法删除');
        //TODO 关闭订单
        Db::transaction(function () use ($groupOrder, $id, $uid) {
            $groupOrder->is_del = 1;
            $orderStatus = [];
            foreach ($groupOrder->orderList as $order) {
                $order->is_del = 1;
                $order->save();
                $orderStatus[] = [
                    'order_id' => $order->order_id,
                    'change_type' => 'cancel',
                    'change_message' => '取消订单' . ($uid ? '' : '[自动]')
                ];
            }
            $groupOrder->save();
            app()->make(StoreOrderStatusRepository::class)->insertAll($orderStatus);
        });
        Queue::push(CancelGroupOrderJob::class, $id);
    }
}