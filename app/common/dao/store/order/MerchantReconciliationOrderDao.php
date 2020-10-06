<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-15
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\dao\store\order;

use app\common\dao\BaseDao;
use app\common\model\store\order\MerchantReconciliationOrder as model;

class MerchantReconciliationOrderDao extends BaseDao
{
   public function getModel(): string
   {
       return model::class;
   }


   public function search($where)
   {
       return ($this->getModel()::getDB())->when(isset($where['reconciliation_id']) && $where['reconciliation_id'] !== '',function ($query)use ($where){
        $query->where('reconciliation_id',$where['reconciliation_id']);
       })->when(isset($where['type']) && $where['type'] !== '',function ($query)use ($where){
           $query->where('type',$where['type']);
       });
   }
}
