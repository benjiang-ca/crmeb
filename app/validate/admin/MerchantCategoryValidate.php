<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class MerchantCategoryValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'category_name|分类名称' => 'require|max:32',
        'commission_rate|手续费' => 'require|float|>:0|<=:100'
    ];
}
