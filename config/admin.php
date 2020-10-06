<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-09
 *
 * Copyright (c) http://crmeb.net
 */

return [
    //token 有效期
    'token_exp' => 3, //三小时
    //token超时多久可自动续期(后台)
    'token_valid_exp' => 15, //15分钟
    //token超时多久可自动续期(用户)
    'user_token_valid_exp' => 60 * 24 * 7, //7天
    //登录验证码有效期
    'captcha_exp' => 30, //30分钟
    'admin_prefix' => 'admin',
    'merchant_prefix' => 'merchant',
    'api_admin_prefix' => 'sys',
    'api_merchant_prefix' => 'mer'
];