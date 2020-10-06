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

class RoleValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'role_name|管理名称' => 'require|max:32',
        'rules|权限' => 'require|array|min:1',
        'status|是否启用' => 'require',
    ];
}