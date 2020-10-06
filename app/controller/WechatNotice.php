<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller;


use crmeb\basic\BaseController;
use crmeb\services\WechatService;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Server\BadRequestException;
use think\Response;

/**
 * Class WechatNotice
 * @package app\controller
 * @author xaboy
 * @day 2020-04-26
 */
class WechatNotice extends BaseController
{
    /**
     * @return Response
     * @throws InvalidArgumentException
     * @throws BadRequestException
     * @author xaboy
     * @day 2020-04-26
     */
    public function serve()
    {
        ob_clean();
        return WechatService::create()->serve($this->request);
    }
}