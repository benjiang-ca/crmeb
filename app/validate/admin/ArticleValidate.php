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

class ArticleValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'cid|选择分类' => 'require|integer',
        'title|标题' => 'require|max:32',
        'content|内容' => 'require',
        'author|作者' => 'require|max:32',
        'image_input|图片' => 'require|max:128',
        'is_hot|是否热门' => 'require|integer',
        'is_banner|是否为Banner' => 'require|integer'
    ];
}
