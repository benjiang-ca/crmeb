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
use app\common\repositories\store\product\ProductRepository;

class CheckProductExtensionJob implements JobInterface
{

    public function fire($job, $data)
    {
        try{
        app()->make(ProductRepository::class)->checkProductByExtension();
        $job->delete();
        }catch (\Exception $exception){
            Log::info(var_export($exception, 1));
        }
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
