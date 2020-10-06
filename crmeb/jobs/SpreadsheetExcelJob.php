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
use crmeb\services\ExcelService;

class SpreadsheetExcelJob implements JobInterface
{

    public function fire($job, $data)
    {
        try{
            app()->make(ExcelService::class)->getAll($data);
        }catch (\Exception $e){
            Log::info('导出文件:'.$data['type'].'; error : ' . $e->getMessage());
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
