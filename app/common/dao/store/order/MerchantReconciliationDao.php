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
use app\common\model\store\order\MerchantReconciliation as model;

class MerchantReconciliationDao extends BaseDao
{
   public function getModel(): string
   {
       return model::class;
   }

   public function search(array $where)
   {
       $query = ($this->getModel()::getDB())
           ->when(isset($where['mer_id']) && $where['mer_id'] != '' ,function($query)use($where){
               $query->where('mer_id',$where['mer_id']);
           })->when(isset($where['status']) && $where['status'] != '' ,function($query)use($where){
               $query->where('status',$where['status']);
           })->when(isset($where['is_accounts']) && $where['is_accounts'] != '' ,function($query)use($where){
               $query->where('is_accounts',$where['is_accounts']);
           })->when(isset($where['date']) && $where['date'] != '' ,function($query)use($where){
               getModelTime($query,$where['date']);
           })->when(isset($where['reconciliation_id']) && $where['reconciliation_id'] != '' ,function($query)use($where){
               $query->where('reconciliation_id',$where['reconciliation_id']);
           });
       return $query->order('create_time DESC,status DESC');
   }

}
