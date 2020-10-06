<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\auth;

use app\common\model\BaseModel;

class Role extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'role_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_role';
    }

    public function ruleNames($isArray = false)
    {
        $menusName = Menu::getDB()->whereIn('menu_id', $this->rules)->column('menu_name');
        return $isArray ? $menusName : implode(',', $menusName);
    }


    /**
     * @param $value
     * @return array
     * @author xaboy
     * @day 2020-03-30
     */
    public function getRulesAttr($value)
    {
        return array_map('intval', explode(',', $value));
    }

    /**
     * @param $value
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public function setRulesAttr($value)
    {
        return implode(',', $value);
    }
}
