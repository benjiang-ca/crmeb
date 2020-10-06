<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/12
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\order;


use app\common\dao\BaseDao;
use app\common\model\store\order\StoreRefundProduct;

class StoreRefundProductDao extends BaseDao
{

    protected function getModel(): string
    {
        return StoreRefundProduct::class;
    }

    public function search(array $where)
    {
        $query = $this->getModel()::getDB()
            ->when(isset($where['order_id']) && $where['order_id'] !== '',function($query)use($where){
                $query->where('order_id',$where['order_id']);
            });

        return $query->order('create_time');
    }
}