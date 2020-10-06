<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\merchant;


use think\Validate;

class MerchantTakeValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'mer_take_status|开启门店自提' => 'require|in:0,1',
        'mer_take_name|自提点名称' => 'require',
        'mer_take_phone|自提点手机号' => 'require|mobile',
        'mer_take_address|自提点地址' => 'require',
        'mer_take_location|店铺经纬度' => 'require|array|length:2',
        'mer_take_day|自提点营业日期' => 'array|max:7',
        'mer_take_time|自提点营业时间' => 'array|length:2',
    ];
}