<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-08
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;

use crmeb\interfaces\JobInterface;
use think\facade\Log;
use think\queue\Job;
use crmeb\services\WechatTemplateService;
use app\common\repositories\user\UserRepository;
use crmeb\services\WechatTemplateMessageService;

class SendTemplateMessageJob implements JobInterface
{

    public function fire($job, $data)
    {
        $make = app()->make(WechatTemplateMessageService::class);
        try{
            $make->sendTemplate($data);
        }catch (\Exception $e){
            Log::info('公众号消息模板:' . $e->getMessage());
        }
        try{
            $make->subscribeSendTemplate($data);
        }catch (\Exception $e){
            Log::info('小程序消息模板:' . $e->getMessage());
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
