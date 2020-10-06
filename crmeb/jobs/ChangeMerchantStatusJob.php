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


use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use crmeb\interfaces\JobInterface;
use think\facade\Log;
use think\queue\Job;

class ChangeMerchantStatusJob implements JobInterface
{

    public function fire($job, $merId)
    {
        $merchant = app()->make(MerchantRepository::class)->get($merId);
        if ($merchant) {
            $where = [
                'mer_status' => ($merchant['is_del'] || !$merchant['mer_state'] || !$merchant['status']) ? 0 : 1
            ];
            app()->make(ProductRepository::class)->changeMerchantProduct($merId, $where);
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
