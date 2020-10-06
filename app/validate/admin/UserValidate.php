<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-09
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class UserValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'real_name|真实姓名' => 'max:25',
        'phone|手机号' => 'isPhone',
        'birthday|生日' => 'dateFormat:Y-m-d',
        'card_id|身份证' => 'length:18',
        'addres|用户地址' => 'max:64',
        'mark|备注' => 'max:200',
        'group_id|分组' => 'integer',
        'label_id|标签' => 'array',
        'is_promoter|推广人' => 'in:0,1'
    ];

    protected function isPhone($val)
    {
        if (!preg_match('/^1[3456789]{1}\d{9}$/', $val))
            return '请输入正确的手机号';
        else
            return true;
    }
}