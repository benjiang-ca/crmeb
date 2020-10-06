<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\sms;


use app\common\model\BaseModel;

class SmsRecord extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'sms_record_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'sms_record';
    }
}