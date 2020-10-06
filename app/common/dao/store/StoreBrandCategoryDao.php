<?php
/**
 *
 * User: Qinii
 * Date: 2020-04-26
 * Time: 11:14
 */
namespace app\common\dao\store;

use app\common\dao\BaseDao;
use app\common\model\store\StoreBrandCategory as model;
use crmeb\traits\CategoresDao;

class StoreBrandCategoryDao extends BaseDao
{

    use CategoresDao;

    protected function getModel(): string
    {
        return model::class;
    }
    public function getMaxLevel()
    {
        return 2;
    }

    public function getAll($mer_id = 0,$status = null)
    {
        return $this->getModel()::getDB()->when(($status !== null),function($query)use($status){
            $query->where($this->getStatus(),$status);
        })->order('sort DESC')->select();
    }

    public function merFieldExists(int $merId, $field, $value, $except = null)
    {
        return ($this->getModel())::getDB()
                ->when($except, function ($query, $except) use ($field) {
                    $query->where($field, '<>', $except);
                })
                ->where($field, $value)->count() > 0;
    }

    public function getAllByField( $field, $value, $except = null)
    {
        return ($this->getModel())::getDB()
                ->when($except, function ($query, $except) use ($field) {
                    $query->where($field, '<>', $except);
                })
                ->where($field, $value);
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020/7/22
     */
    public function options()
    {
        return model::getDB()->where('is_show', 1)->column('pid,cate_name', 'store_brand_category_id');
    }

}
