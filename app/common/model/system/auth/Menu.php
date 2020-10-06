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

class Menu extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'menu_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_menu';
    }
}