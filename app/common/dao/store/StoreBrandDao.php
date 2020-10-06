<?php
/**
 *
 * User: Qinii
 * Date: 2020-04-26
 * Time: 11:14
 */
namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\StoreBrand as model;
use crmeb\traits\CategoresDao;

class StoreBrandDao extends BaseDao
{

    use CategoresDao;

    protected function getModel(): string
    {
        return model::class;
    }


    public function getAll()
    {
        $query = $this->getModel()::hasWhere('brandCategory',function($query){
                $query->where('is_show',1);
            });
        $query->where('StoreBrand.is_show',1);
        return $query->order('StoreBrand.sort DESC')->select();
    }


    public function merFieldExists($field, $value, $except = null)
    {
        return ($this->getModel())::getDB()
                ->when($except, function ($query, $except) use ($field) {
                    $query->where($field, '<>', $except);
                })
                ->where($field, $value)->count() > 0;
    }

    public function search(array $where)
    {
        $query = $this->getModel()::getDB()->order('sort DESC');
        if(isset($where['brand_category_id']) && $where['brand_category_id'])
            $query->where('brand_category_id',$where['brand_category_id']);
        if(isset($where['brand_name']) && $where['brand_name'])
            $query->where('brand_name','like','%'.$where['brand_name'].'%');
        if((isset($where['ids']) && $where['ids']))
            $query->where($this->getPk(),'in',$where['ids']);
        return $query;

    }

}
