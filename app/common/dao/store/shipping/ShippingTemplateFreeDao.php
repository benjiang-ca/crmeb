<?php
/**
 * @package crmeb_merchant
 * Author: Qinii
 * Date: 2020/5/6
 * Time: 14:21
 */
namespace app\common\dao\store\shipping;

use think\facade\Db;
use app\common\dao\BaseDao;
use app\common\model\store\shipping\ShippingTemplateFree as model;

class ShippingTemplateFreeDao  extends BaseDao
{
    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @return string
     */
    protected function getModel(): string
    {
        return model::class;
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param $field
     * @param $value
     * @param null $except
     * @return bool
     */
    public function merFieldExists($field, $value, $except = null)
    {
        return ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
                $query->where($field, '<>', $except);
            })->where($field, $value)->count() > 0;
    }

    /**
     * 批量删除
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param array $id
     * @param array $temp_id
     */
    public function batchRemove(array $id,array $temp_id)
    {
        if($id)
            ($this->getModel())::getDB()->where($this->getPk(),'in',$id)->delete();
        if($temp_id)
            ($this->getModel())::getDB()->where('temp_id','in',$temp_id)->delete();
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param array $data
     * @return mixed
     */
    public function insertAll(array $data)
    {
        return ($this->getModel()::getDB())->insertAll($data);
    }
}