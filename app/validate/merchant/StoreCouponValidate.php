<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\merchant;


use think\Validate;

class StoreCouponValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'title|优惠券名称' => 'require|max:32',
        'coupon_price|优惠券面值' => 'require|>:0',
        'use_min_price|最低消费金额' => 'require|float',
        'coupon_type|有效期类型' => 'require|in:0,1',
        'coupon_time|有效期限' => 'requireIf:coupon_type,0|integer|>:0',
        'use_start_time|有效期限' => 'requireIf:coupon_type,1|array|>:2',
        'sort|排序' => 'require|integer',
        'status|状态' => 'require|in:0,1',
        'type|优惠券类型' => 'require|in:0,1',
        'product_id|商品' => 'requireIf:type,1|array|>:0',
        'send_type|类型' => 'require|in:0,1,2,3',
        'full_reduction|满赠金额' => 'requireIf:send_type,1|float|>=:0',
        'is_limited|是否限量' => 'require|in:0,1',
        'is_timeout|是否限时' => 'require|in:0,1',
        'range_date|领取时间' => 'requireIf:is_timeout,1|array|length:2',
        'total_count|发布数量' => 'requireIf:is_limited,1|integer|>:0',
    ];

}