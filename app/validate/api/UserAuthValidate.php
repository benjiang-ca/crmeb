<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\api;

use think\Validate;

class UserAuthValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'phone|手机号' => 'require|mobile',
        'pwd|密码' => 'require|min:6',
        'sms_code|短信验证码' => 'require|max:4',
    ];


    public function scenePwdlogin()
    {
        return $this->remove('sms_code','require|max:4');
    }

    public function sceneSmslogin()
    {
        return $this->remove('pwd','require|min:6');
    }

    public function sceneVerify()
    {
        return $this->remove('pwd','require|min:6')
            ->remove('sms_code','require|max:4');
    }
}
