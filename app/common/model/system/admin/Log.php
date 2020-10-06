<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-15
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\admin;


use app\common\model\BaseModel;

class Log extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'log_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'system_log';
    }
}