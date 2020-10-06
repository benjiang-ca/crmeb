<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/8
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\order;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\order\StoreOrderProduct;
use think\facade\Db;
use think\model\Relation;

/**
 * Class StoreOrderProductDao
 * @package app\common\dao\store\order
 * @author xaboy
 * @day 2020/6/10
 */
class StoreOrderProductDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/10
     */
    protected function getModel(): string
    {
        return StoreOrderProduct::class;
    }

    /**
     * @param $id
     * @param $uid
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function userOrderProduct($id, $uid)
    {
        return StoreOrderProduct::getDB()->where('uid', $uid)->where('order_product_id', $id)->with(['orderInfo' => function (Relation $query) {
            $query->field('order_id,mer_id')->where('status', 2);
        }])->find();
    }

    /**
     * @param $orderId
     * @return int
     * @author xaboy
     * @day 2020/6/12
     */
    public function noReplyProductCount($orderId)
    {
        return StoreOrderProduct::getDB()->where('order_id', $orderId)->where('is_reply', 0)
            ->count();
    }

    /**
     * @param array $ids
     * @param $uid
     * @param null $orderId
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/12
     */
    public function userRefundProducts(array $ids, $uid, $orderId = null)
    {
        return StoreOrderProduct::getDB()->whereIn('order_product_id', $ids)->when($orderId, function ($query, $orderId) {
            return $query->where('order_id', $orderId);
        })->where('uid', $uid)->where('refund_num', '>', 0)->select();
    }

    public function orderProductGroup($date, $merId = null, $limit = 7)
    {
        return StoreOrderProduct::getDB()->alias('A')->leftJoin('StoreOrder B', 'A.order_id = B.order_id')
            ->field(Db::raw('sum(A.product_num) as total,A.product_id,cart_info'))
            ->withAttr('cart_info', function ($val) {
                return json_decode($val, true);
            })->when($date, function ($query, $date) {
                getModelTime($query, $date, 'B.pay_time');
            })->when($merId, function ($query, $merId) {
                $query->where('B.mer_id', $merId);
            })->where('B.paid', 1)->group('A.product_id')->limit($limit)->order('total DESC')->select();
    }

    public function dateProductNum($date)
    {
        return StoreOrderProduct::getDB()->alias('A')->leftJoin('StoreOrder B', 'A.order_id = B.order_id')->when($date, function ($query, $date) {
            getModelTime($query, $date, 'B.pay_time');
        })->where('B.paid',1)->sum('A.product_num');
    }
}