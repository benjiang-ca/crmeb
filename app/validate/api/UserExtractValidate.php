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

class UserExtractValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'extract_type|收款方式' => 'require',
        'extract_price|提现金额' => 'require|gt:0',
        'real_name|姓名' => 'requireIf:extract_type,0',
        'bank_code|银行卡号' => 'requireIf:extract_type,0',
        'alipay_code|支付宝账户' => 'requireIf:extract_type,2',
        'wechat|微信号' => 'requireIf:extract_type,1',
    ];

}
