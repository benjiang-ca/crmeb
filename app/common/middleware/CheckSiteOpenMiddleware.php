<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/23
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;


use app\Request;
use crmeb\interfaces\MiddlewareInterface;
use think\Response;

class CheckSiteOpenMiddleware implements MiddlewareInterface
{

    public function handle(Request $request, \Closure $next): Response
    {
        if (systemConfig('site_open') === '0') {
            return app('json')->make(501, '站点已关闭');
        }
        return $next($request);
    }
}