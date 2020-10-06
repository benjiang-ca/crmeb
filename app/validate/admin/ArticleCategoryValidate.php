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

class ArticleCategoryValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'pid|选择分类' => 'require|integer',
        'title|分类名称' => 'require|max:12',
        'info|分类简介' => 'max:255',
        'status|状态' => 'require|in:0,1',
        'image|分类图片' => 'max:128',
        'sort|排序' => 'require|integer'
    ];
}