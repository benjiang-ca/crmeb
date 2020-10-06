<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;


use app\Request;
use think\exception\HttpResponseException;
use think\facade\Route;
use think\Response;

class InstallMiddleware extends BaseMiddleware
{

    public function before(Request $request)
    {
        if(!file_exists(__DIR__.'/../../../install/install.lock')){
            throw new HttpResponseException( Response::create('/install.html', 'redirect')->code(302));
        }
    }

    public function after(Response $response)
    {
    }
}
