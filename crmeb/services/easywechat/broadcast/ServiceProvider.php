<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/28
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services\easywechat\broadcast;


use EasyWeChat\MiniProgram\AccessToken;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['miniBroadcast'] = function ($pimple) {
            return new Client($pimple['mini_program.access_token'],$pimple['config']['mini_program']);
        };
    }
}