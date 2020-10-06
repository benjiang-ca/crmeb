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

class AttachmentCategoryValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'pid|选择分类' => 'require|integer',
        'attachment_category_name|分类名称' => 'require|max:16',
        'attachment_category_enname|分类目录' => 'require|alphaNum|max:16',
        'sort|排序' => 'require|integer'
    ];
}