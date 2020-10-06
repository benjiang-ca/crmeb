<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-18
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\wechat;

use app\common\model\BaseModel;

class TemplateMessage extends BaseModel
{

    public static function tablePk(): string
    {
        return 'template_id';
    }


    public static function tableName(): string
    {
        return 'template_message';
    }

}
