<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\merchant;


use think\Validate;

class StoreAttrTemplateValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'template_name|模板名称' => 'require|max:32',
        'template_value|规则' => 'require|array|min:1'
    ];
}