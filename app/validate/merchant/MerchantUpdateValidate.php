<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/25
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\merchant;


use think\Validate;

class MerchantUpdateValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'mer_info|店铺简介' => 'require|max:128',
        'service_phone|服务电话' => 'require|number|length:11',
        'mer_avatar|店铺头像' => 'require|max:128',
        'mer_banner|店铺banner' => 'require|max:128',
        'mer_state|是否开启' => 'require|in:0,1',
    ];
}