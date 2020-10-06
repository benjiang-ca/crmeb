<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-10
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\exceptions;


use think\exception\HttpResponseException;

class AuthException extends HttpResponseException
{
    public function __construct($message, $code = 40000)
    {
        parent::__construct(app('json')->make($code, $message));
    }
}