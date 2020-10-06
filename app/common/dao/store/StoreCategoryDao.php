<?php
/**
 *
 * User: Qinii
 * Date: 2020-04-26
 * Time: 11:14
 */
namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\StoreCategory as model;
use crmeb\traits\CategoresDao;

class StoreCategoryDao extends BaseDao
{

    use CategoresDao;

    protected function getModel(): string
    {
        return model::class;
    }


    public function fieldExistsList(?int $merId,$field,$value,$except = null)
    {
        return ($this->getModel()::getDB())->when($except ,function($query)use($field,$except){
            $query->where($field,'<>',$except);
        })->when(($merId !== null) ,function($query)use($merId){
            $query->where('mer_id',$merId);
        })->where($field,$value);

    }

    public function getTwoLevel($merId = 0)
    {
        $pid = model::getDB()->where('pid', 0)->where('mer_id', $merId)->column('store_category_id');
        return model::getDB()->whereIn('pid', $pid)->where('is_show', 1)->where('mer_id', $merId)->order('sort DESC')->column('store_category_id,cate_name,pid');
    }

    public function children($pid, $merId = 0)
    {
        return model::getDB()->where('pid', $pid)->where('mer_id', $merId)->where('is_show', 1)->order('sort DESC')->column('store_category_id,cate_name,pic');
    }

    public function allChildren($id)
    {
        $path = model::getDB()->where('store_category_id', $id)->where('mer_id', 0)->value('path');
        return model::getDB()->whereLike('path', "$path%")->where('mer_id', 0)->order('sort DESC')->column('store_category_id');
    }

    public function getMaxLevel($merId = null)
    {
        if($merId) return 2;
        return 3;
    }
}
