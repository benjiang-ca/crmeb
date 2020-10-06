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
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * Class SystemConfigClassify
 * @package app\common\model\system\config
 * @author xaboy
 * @day 2020-03-30
 */
class SystemConfigClassify extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'config_classify_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_config_classify';
    }


    /**
     * @return HasOne
     * @author xaboy
     * @day 2020-03-30
     */
    public function parent()
    {
        return $this->hasOne(self::class, 'config_classify_id', 'pid');
    }

    /**
     * @return HasMany
     * @author xaboy
     * @day 2020-03-30
     */
    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'config_classify_id');
    }

    /**
     * @return HasMany
     * @author xaboy
     * @day 2020-03-30
     */
    public function config()
    {
        return $this->hasMany(SystemConfig::class, 'classify_id', 'config_classify_id');
    }
}