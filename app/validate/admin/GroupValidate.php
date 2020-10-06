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

class GroupValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'group_name|数据组名称' => 'require|max:32',
        'group_info|数据组说明' => 'max:128',
        'group_key|数据组key' => 'require|max:32',
        'fields|数据组字段' => 'require|array',
        'sort|排序' => 'require|integer',
        'user_type|后台类型' => 'require|in:0,1'
    ];
}