<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-13
 *
 * Copyright (c) http://crmeb.net
 */
namespace crmeb\listens;

use app\common\repositories\store\ExcelRepository;
use Swoole\Timer;
use think\facade\Log;
use crmeb\interfaces\ListenerInterface;

class ExcelFileDelListen implements ListenerInterface
{
    public function handle($event): void
    {
        $make = app()->make(ExcelRepository::class);

        Timer::tick(1000 * 60 * 60, function () use ($make) {
            $time = date('Y-m-d H:i:s', strtotime("-" . 3 . " day"));
            $data = $make->getDelByTime($time);
            foreach ($data as $id => $path) {
                try {
                    $make->del($id,$path);
                } catch (\Exception $e) {
                    Log::info('自动删除导出文件失败' . var_export($id,true));
                }
            }
        });
    }
}
