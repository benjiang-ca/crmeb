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

class AttachmentValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'attachment_category_id|选择分类' => 'require|integer',
        'attachment_name|附件名称' => 'require|max:255',
        'attachment_src|分类目录' => 'require|max:255',
    ];
}