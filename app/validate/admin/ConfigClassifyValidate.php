<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-25
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class ConfigClassifyValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'pid|上级分类' => 'require|integer',
        'classify_name|配置分类名称' => 'require|max:20',
        'classify_key|配置分类key' => 'require|max:20',
        'info|配置分类说明' => 'max:30',
        'status|显示状态' => 'require|integer|in:0,1',
        'sort|排序' => 'require|integer',
        'icon|图标' => 'max:15'
    ];
}