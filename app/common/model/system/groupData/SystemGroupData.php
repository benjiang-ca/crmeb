<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\groupData;


use app\common\model\BaseModel;

/**
 * Class SystemGroupData
 * @package app\common\model\system\groupData
 * @author xaboy
 * @day 2020-03-30
 */
class SystemGroupData extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'group_data_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_group_data';
    }

    public static function getValueAttr($val)
    {
        return json_decode($val, true);
    }

    public static function setValueAttr($val)
    {
        return json_encode($val);
    }
}