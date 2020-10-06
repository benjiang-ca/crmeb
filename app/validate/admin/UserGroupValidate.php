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

class UserGroupValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'group_name|分组名称' => 'require|max:32'
    ];
}