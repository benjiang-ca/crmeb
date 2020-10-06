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

class UserAddressValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'real_name|收货人姓名' => 'require',
        'phone|收货人电话' => 'require|mobile',
        'province|收货人所在省' => 'require',
        'city|收货人所在市' => 'require',
        'district|收货人所在区' => 'require',
        'detail|收货人详细地址' => 'require',
    ];
}
