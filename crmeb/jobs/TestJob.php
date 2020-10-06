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

class TestJob implements JobInterface
{

    public function fire($job, $data)
    {
        Log::info(var_export($data, 1));
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}