<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\merchant;


use think\Validate;

class StoreServiceValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'uid|客服' => 'require|array',
        'uid.src|客服' => 'require',
        'uid.id|客服' => 'require|integer',
        'nickname|客服名称' => 'require|max:12',
        'avatar|客服头像' => 'max:250',
        'status|状态' => 'require|in:0,1',
        'is_verify|核销状态' => 'require|in:0,1',
        'notify|订单通知状态' => 'require|in:0,1',
        'phone|短信通知电话' => 'requireIf:notify,1|mobile',
        'sort|排序' => 'require|integer',
        'customer|展示统计管理状态' => 'require|in:0,1',
    ];
}