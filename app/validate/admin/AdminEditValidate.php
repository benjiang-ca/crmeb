<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class AdminEditValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'real_name|管理员姓名' => 'require|max:16',
        'phone|手机号' => 'max:12',
    ];

}