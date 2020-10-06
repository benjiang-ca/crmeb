<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-16
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\merchant;


use app\common\model\BaseModel;
use app\common\model\system\auth\Role;

class MerchantAdmin extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'merchant_admin_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'merchant_admin';
    }

    /**
     * @param $value
     * @return array
     * @author xaboy
     * @day 2020-03-30
     */
    public function getRolesAttr($value)
    {
        return array_map('intval', explode(',', $value));
    }

    /**
     * @param $value
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public function setRolesAttr($value)
    {
        return implode(',', $value);
    }

    /**
     * @param bool $isArray
     * @return array|string
     * @author xaboy
     * @day 2020-04-18
     */
    public function roleNames($isArray = false)
    {
        $roleNames = Role::getDB()->whereIn('role_id', $this->roles)->column('role_name');
        return $isArray ? $roleNames : implode(',', $roleNames);
    }
}