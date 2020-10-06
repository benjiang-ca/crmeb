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

class AdminValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'account|账号' => 'require|max:18|min:4',
        'pwd|密码' => 'require|max:18|min:6',
        'phone|联系电话' => 'isPhone',
        'againPassword|确认密码' => 'require|max:18|min:6',
        'real_name|管理员姓名' => 'max:16',
        'roles|权限组' => 'require|array|min',
        'status|启用状态' => 'require|in:0,1',
    ];

    protected function isPhone($val)
    {
        if ($val && !preg_match('/^1[3456789]{1}\d{9}$/', $val))
            return '请输入正确的手机号';
        else
            return true;
    }

    public function isUpdate()
    {
        unset($this->rule['pwd|密码'], $this->rule['againPassword|确认密码']);
        return $this;
    }

    public function isPassword()
    {
        unset($this->rule['account|账号'], $this->rule['real_name|姓名'], $this->rule['roles|权限组'], $this->rule['status|启用状态'], $this->rule['phone|联系电话']);
        return $this;
    }
}
