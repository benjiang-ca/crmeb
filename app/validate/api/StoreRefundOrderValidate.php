<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/12
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\api;


use think\Validate;

class StoreRefundOrderValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'type|退款类型' => 'require|in:1,2',
        'refund_type|退款方式' => 'require|in:1,2',
        'num|商品件数' => 'requireIf:type,1|integer|>:0',
        'ids|退款商品' => 'require',
        'refund_message|退款原因' => 'require|max:128',
        'mark|备注' => 'max:128',
        'refund_price|退款金额' => 'require|float',
        'pics|凭证' => 'array|max:9',
    ];
}