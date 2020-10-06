<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\common\dao\store\product;

use app\common\dao\BaseDao;
use app\common\model\store\product\ProductCopy as model;

class ProductCopyDao extends BaseDao
{
    protected function getModel(): string
    {
        return model::class;
    }

    public function search(array $where)
    {
        return $this->getModel()::getDB()
            ->when(isset($where['mer_id']) && $where['mer_id'] !== '',function($query)use($where){
                $query->where('mer_id',$where['mer_id']);
            })
            ->when(isset($where['type']) && $where['type'] !== '',function($query)use($where){
                $query->where('type',$where['type']);
            })
            ->order('create_time DESC');
    }

}
