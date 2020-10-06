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
use app\common\model\store\shipping\ShippingTemplate as model;

class ShippingTemplateDao  extends BaseDao
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
     * @Date: 2020/5/7
     * @param int $merId
     * @param array $where
     * @return mixed
     */
    public function search(int $merId,array $where)
    {
        $query = ($this->getModel()::getDB())->where('mer_id',$merId)->order('sort desc');
        if(isset($where['name']) && !empty($where['name']))
            $query->where('name','like','%'.$where['name'].'%');
        if(isset($where['type']) && !empty($where['type']))
            $query->where('type',$where['type']);
        return $query->order('sort DESC,create_time DESC');
    }

    /**
     * 查询是否存在
     * @Author:Qinii
     * @Date: 2020/5/7
     * @param int $merId
     * @param $field
     * @param $value
     * @param null $except
     * @return bool
     */
    public function merFieldExists(int $merId, $field, $value, $except = null)
    {
       return  ($this->getModel())::getDB()->when($except, function ($query, $except) use ($field) {
                $query->where($field, '<>', $except);
            })->where('mer_id', $merId)->where($field, $value)->count() > 0;
    }

    /**
     * 关联删除
     * @Author:Qinii
     * @Date: 2020/5/7
     * @param int $id
     * @return int|void
     */
    public function delete(int $id)
    {
        $result = $this->getModel()::with(['free','region','undelives'])->find($id);
        $result->together(['free','region','undelives'])->delete();
    }

    /**
     * 批量删除
     * @Author:Qinii
     * @Date: 2020/5/8
     * @param int $id
     * @return mixed
     */
    public function batchRemove(int $id)
    {
        return ($this->getModel())::getDB()->where($this->getPk(),'in',$id)->delete();
    }

    public function getList($merId)
    {
        return ($this->getModel())::getDB()->where('mer_id',$merId)->field('shipping_template_id,name')->order('sort DESC,create_time DESC')->select();
    }
}
