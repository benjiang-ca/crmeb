<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/2
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api;


use crmeb\basic\BaseController;
use crmeb\services\WechatService;

class Wechat extends BaseController
{
    public function jsConfig()
    {
        return app('json')->success(WechatService::create()->jsSdk($this->request->param('url')?:$this->request->host()));
    }
}