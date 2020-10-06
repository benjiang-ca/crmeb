<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\api;

use think\Validate;

class StoreCartValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'product_id|商品ID' => 'require',
        'product_attr_unique|SKU' => 'require',
        'cart_num|购买数量' => 'require|number',
    ];
}