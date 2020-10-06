<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/11
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\order;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\order\StoreOrderStatus;

/**
 * Class StoreOrderStatusDao
 * @package app\common\dao\store\order
 * @author xaboy
 * @day 2020/6/12
 */
class StoreOrderStatusDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/12
     */
    protected function getModel(): string
    {
        return StoreOrderStatus::class;
    }

    /**
     * @param $id
     * @return mixed
     * @author xaboy
     * @day 2020/6/12
     */
    public function search($id)
    {
        return $query = ($this->getModel()::getDB())->where('order_id', $id);
    }

    public function getTimeoutDeliveryOrder($end)
    {
        return StoreOrderStatus::getDB()->alias('A')->leftJoin('StoreOrder B', 'A.order_id = B.order_id')
            ->whereIn('A.change_type', ['delivery_0', 'delivery_1', 'delivery_2'])
            ->where('A.change_time', '<', $end)->where('B.order_type', 0)->where('B.paid', 1)->where('B.status', 1)
            ->column('A.order_id');
    }
}
