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

class FeedbackValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'type|类型' => 'require',
        'content|详情' => 'require|>:10',
        'images|图片' => 'array|max:6',
        'realname|姓名' => 'require|>:1',
        'contact|联系方式' => 'require|checkContact'
    ];

    protected function checkContact($val)
    {
        if ($this->regex($val, 'mobile') || $this->filter($val, 'email'))
            return true;
        else
            return '请输入正确的联系方式';
    }
}
