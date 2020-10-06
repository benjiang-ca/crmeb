<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class GroupDataValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'status|显示状态' => 'require|in:0,1',
        'sort|排序' => 'require|integer'
    ];
}