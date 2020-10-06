<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\config;


use app\common\model\BaseModel;
use think\model\relation\HasOne;

/**
 * Class SystemConfig
 * @package app\common\model\system\config
 * @author xaboy
 * @day 2020-03-30
 */
class SystemConfig extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'config_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_config';
    }

    /**
     * @return HasOne
     * @author xaboy
     * @day 2020-03-30
     */
    public function classify()
    {
        return $this->hasOne(SystemConfig::class, 'config_classify_id', 'config_classify_id');
    }
}