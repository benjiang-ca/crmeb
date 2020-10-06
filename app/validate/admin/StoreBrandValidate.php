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

class StoreBrandValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'brand_category_id|父级分类' => 'require|integer',
        'brand_name|名称' => 'require|max:32',
        'is_show|状态' => 'require|in:0,1',
        'sort|排序' => 'require|integer',
        'pic|图标'   => 'max:128'
    ];
}
