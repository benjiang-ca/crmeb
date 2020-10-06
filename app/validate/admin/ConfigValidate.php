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

class ConfigValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'config_classify_id|配置分类' => 'require|integer',
        'config_name|配置名称' => 'require|max:64',
        'config_key|配置key' => 'require|max:64',
        'config_type|配置类型' => 'require|max:15',
        'config_rule|配置规则' => 'max:250',
        'required|必填状态' => 'require|in:0,1',
        'info|配置说明' => 'max:128',
        'sort|排序' => 'require|integer',
        'status|状态' => 'require|in:0,1',
        'user_type|后台类型' => 'require|in:0,1'
    ];
}