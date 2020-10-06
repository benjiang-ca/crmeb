<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/6
 * @Time: 15:11
 */
namespace app\common\dao\store\shipping;

use app\common\dao\BaseDao;
use app\common\model\store\shipping\City as model;

class CityDao  extends BaseDao
{
    protected function getModel(): string
    {
        return model::class;
    }

    public function getAll(array $where)
    {
        return ($this->getModel()::getDB())->where($where)
            ->order('city_id ASC')->field('city_id,name,merger_name,parent_id,level')->select();
    }
}
