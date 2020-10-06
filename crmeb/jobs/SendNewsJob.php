<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-11
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\user\UserRepository;
use app\common\repositories\wechat\WechatUserRepository;
use crmeb\interfaces\JobInterface;
use crmeb\services\WechatService;
use think\queue\Job;

class SendNewsJob implements JobInterface
{

    public function fire($job, $data)
    {
        $wechatUserRepository = app()->make(WechatUserRepository::class);
        [$id, $news] = $data;
        $wechatUid = app()->make(UserRepository::class)->uidByWechatUserId(intval($id));

        if (!$wechatUid || !($openId = $wechatUserRepository->idByOpenId((int)$wechatUid))) {
            $job->delete();
            return;
        }
        try {
            WechatService::create()->staffTo($openId, WechatService::newsMessage($news));
        } catch (\Exception $e) {
            $job->failed($e);
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}