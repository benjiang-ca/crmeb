<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class StoreSeckillValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'start_time|开始时间' => 'require|number|between:0,24',
        'end_time|结束时间'   => 'require|number|between:0,24|>:start_time',
        'status|状态'        => 'require|in:0,1',
    ];
}
