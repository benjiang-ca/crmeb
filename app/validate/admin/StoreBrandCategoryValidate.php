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

class StoreBrandCategoryValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'pid|父级分类' => 'require|integer',
        'cate_name|分类名称' => 'require|max:12',
        'is_show|状态' => 'require|in:0,1',
        'sort|排序' => 'require|integer'
    ];
}
