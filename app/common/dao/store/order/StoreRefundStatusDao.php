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
use app\common\model\store\order\StoreRefundStatus;

class StoreRefundStatusDao extends BaseDao
{

    protected function getModel(): string
    {
        return StoreRefundStatus::class;
    }

    public function search($id)
    {
        return $query = StoreRefundStatus::getDB()->where('refund_order_id', $id);
    }
}
