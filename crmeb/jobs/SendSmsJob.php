<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use crmeb\interfaces\JobInterface;
use crmeb\services\YunxinSmsService;
use think\facade\Log;

class SendSmsJob implements JobInterface
{

    public function fire($job, $data)
    {
        try {
            YunxinSmsService::sendMessage($data['tempId'], $data['id']);
        } catch (\Exception $e) {
            Log::info('发送短信失败' . var_export($data, 1) . $e->getMessage());
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
