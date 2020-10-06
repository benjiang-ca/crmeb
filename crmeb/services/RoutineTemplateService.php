<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-20
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;


use app\common\repositories\wechat\WechatUserRepository;
use crmeb\services\template\Template;

/**
 * Class WechatTemplateService
 * @package crmeb\services
 * @author xaboy
 * @day 2020-04-20
 */
class RoutineTemplateService
{

    /**
     * 发送模板消息
     * @param string $tempCode
     * @param int $uid 用户uid
     * @param array $data 模板内容
     * @param string $link 跳转链接
     * @return bool
     */
    public function sendTemplate(string $tempCode, $uid, array $data, string $link = '')
    {
        try {
            $openid = app()->make(WechatUserRepository::class)->idByOpenId((int)$uid);
            if (!$openid) return true;
            $template = new Template('subscribe');
            return $template->to($openid)->url($link)->send($tempCode, $data);
        } catch (\Exception $e) {
            return true;
        }
    }
}