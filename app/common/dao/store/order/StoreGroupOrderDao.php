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
use app\common\model\store\order\StoreGroupOrder;

/**
 * Class StoreGroupOrderDao
 * @package app\common\dao\store\order
 * @author xaboy
 * @day 2020/6/9
 */
class StoreGroupOrderDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/9
     */
    protected function getModel(): string
    {
        return StoreGroupOrder::class;
    }

    /**
     * @param null $uid
     * @return int
     * @author xaboy
     * @day 2020/6/11
     */
    public function orderNumber($uid = null)
    {
        return StoreGroupOrder::when($uid, function ($query, $uid) {
            $query->where('uid', $uid);
        })->where('is_del', 0)->where('paid', 0)->count();
    }

    /**
     * @param array $where
     * @return \think\db\BaseQuery
     * @author xaboy
     * @day 2020/6/9
     */
    public function search(array $where)
    {
        return StoreGroupOrder::getDB()->when(isset($where['paid']) && $where['paid'] !== '', function ($query) use ($where) {
            $query->where('paid', $where['paid']);
        })->when(isset($where['uid']) && $where['uid'] !== '', function ($query) use ($where) {
            $query->where('uid', $where['uid']);
        })->order('create_time DESC')->when(isset($where['is_del']) && $where['is_del'] !== '', function ($query) use ($where) {
            $query->where('is_del', $where['is_del']);
        }, function ($query) {
            $query->where('is_del', 0);
        });
    }

    /**
     * @param $time
     * @param bool $is_remind
     * @return array
     * @author xaboy
     * @day 2020/6/9
     */
    public function getTimeOutIds($time, $is_remind = false)
    {
        return StoreGroupOrder::getDB()->where('is_del', 0)->where('paid', 0)
            ->when($is_remind, function ($query) {
                $query->where('is_remind', 0);
            })->where('create_time', '<=', $time)->column('group_order_id');
    }

    public function isRemind($id)
    {
        return StoreGroupOrder::getDB()->where('group_order_id', $id)->update(['is_remind' => 1]);
    }

    public function totalNowMoney($uid)
    {
        return StoreGroupOrder::getDB()->where('pay_type', 0)->where('uid', $uid)->sum('pay_price') ?: 0;
    }
}
