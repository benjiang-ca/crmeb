<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-15
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\exceptions;


use think\exception\HttpResponseException;

class UploadFailException extends HttpResponseException
{

    public function __construct($message = '附件上传失败')
    {
        parent::__construct(app('json')->fail($message));
    }
}