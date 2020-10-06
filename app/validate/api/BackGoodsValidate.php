<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\api;


use think\Validate;

class BackGoodsValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'delivery_type|快递公司' => 'require',
        'delivery_id|快递单号' => 'require',
        'delivery_phone|联系电话' => 'require|mobile',
        'delivery_mark|备注' => 'max:128',
        'delivery_pics|凭证' => 'array|max:9',
    ];
}