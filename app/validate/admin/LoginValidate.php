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

class LoginValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'account|账号' => 'require|min:4|max:32',
        'password|密码' => 'require|min:6|max:16',
        'code|验证码' => 'require|length:4',
        'key|key' => 'require|max:64',
    ];
}
