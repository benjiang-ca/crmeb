<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\config;


use app\common\model\BaseModel;

/**
 * Class SystemConfigValue
 * @package app\common\model\system\config
 * @author xaboy
 * @day 2020-03-30
 */
class SystemConfigValue extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'config_value_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_config_value';
    }

    /**
     * @param $value
     * @return mixed
     * @author xaboy
     * @day 2020-03-30
     */
    public function getValueAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     * @return false|string
     * @author xaboy
     * @day 2020-03-30
     */
    public function setValueAttr($value)
    {
        return json_encode($value);
    }
}