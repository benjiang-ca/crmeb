<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system;


use app\common\model\BaseModel;

/**
 * Class Cache
 * @package app\common\model\system
 * @author xaboy
 * @day 2020-04-24
 */
class Cache extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'key';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'cache';
    }

    /**
     * @param $val
     * @return false|string
     * @author xaboy
     * @day 2020-04-24
     */
    public function setResultAttr($val)
    {
        return json_encode($val);
    }

    /**
     * @param string $val
     * @return mixed
     * @author xaboy
     * @day 2020-04-24
     */
    public function getResultAttr($val)
    {
        return json_decode($val, true);
    }
}