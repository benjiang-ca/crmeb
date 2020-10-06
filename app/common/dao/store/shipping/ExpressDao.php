<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/13
 */
namespace app\common\dao\store\shipping;

use app\common\dao\BaseDao;
use app\common\model\store\shipping\Express as model;

class ExpressDao  extends BaseDao
{
    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @return string
     */
    protected function getModel(): string
    {
        return model::class;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param $field
     * @param $value
     * @param null $except
     * @return bool
     */
    public function merFieldExists($field, $value, $except = null, $id = null, $isUser = null)
    {
        return ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
                $query->where($field, '<>', $except);
            })->when($id, function ($query) use ($id) {
                $query->where($this->getPk(), '<>', $id);
            })->when($isUser, function ($query) {
                $query->where('is_show', 1);
            })->where($field, $value)->count() > 0;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param array $where
     * @return mixed
     */
    public function search(array $where)
    {
        $query = ($this->getModel()::getDB())
            ->when(isset($where['name']) && $where['name'],function($query) use ($where){
                $query->where('name|code','like','%'.$where['name'].'%');
            })->where(isset($where['code']) && $where['code'],function($query)use($where){
                $query->where('code',$where['name']);
            });
        return $query->order('sort DESC');
    }
}