<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-07
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class UserLabelValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'label_name|标签名称' => 'require|max:32'
    ];
}