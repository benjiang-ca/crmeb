<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-07
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\user;


use app\common\dao\BaseDao;
use app\common\model\user\UserAddress as model;

class UserAddressDao extends BaseDao
{


    /**
     * @return string
     * @author Qinii
     */
    protected function getModel(): string
    {
        return model::class;
    }


    public function userFieldExists($field, $value,$uid): bool
    {
        return (($this->getModel()::getDB())->where('uid',$uid)->where($field,$value)->count()) > 0;
    }

    public function changeDefault(int $uid)
    {
        return ($this->getModel()::getDB())->where('uid',$uid)->update(['is_default' => 0]);
    }

    public function getAll(int $uid)
    {
        return (($this->getModel()::getDB())->where('uid',$uid));
    }
}
