<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/10
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\api;


use think\Validate;

class ProductReplyValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'comment|评论' => 'require|max:128',
        'product_score|商品分数' => 'require|integer|max:5',
        'service_score|服务分数' => 'require|integer|max:5',
        'postage_score|物流分数' => 'require|integer|max:5',
        'pics|评价图片' => 'array|max:6',
    ];

    protected $message = [
        'pics.max' => '评价最多上传6张图片'
    ];
}