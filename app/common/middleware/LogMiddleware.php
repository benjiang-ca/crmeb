<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-15
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;


use app\common\repositories\system\admin\AdminLogRepository;
use app\Request;
use crmeb\services\SwooleTaskService;
use think\Response;

class LogMiddleware extends BaseMiddleware
{

    public function before(Request $request)
    {
        // TODO: Implement before() method.
    }


    public function after(Response $response)
    {
        if ($this->request->method() == 'GET') return;
        SwooleTaskService::log($this->request->merId(), AdminLogRepository::parse($this->request));
    }
}