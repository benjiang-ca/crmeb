<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-18
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\validate\admin;

use think\Validate;

class TemplateMessageValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'tempkey|模板编号' => 'require',
        'name|模板名' => 'require',
        'tempid|模板ID' => 'require',
        'content|回复内容' => 'require',
        'status|状态' => 'require',
    ];
}
