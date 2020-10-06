<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/30
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class StoreProductReplyValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'product_id|商品' => 'require|array|min:1',
        'nickname|用户昵称' => 'require|max:20',
        'comment|评论' => 'require|max:128',
        'product_score|商品分数' => 'require|integer|max:5',
        'service_score|服务分数' => 'require|integer|max:5',
        'postage_score|物流分数' => 'require|integer|max:5',
        'avatar|用户头像' => 'require',
        'pics|评价图片' => 'require|array|max:6',
    ];
}