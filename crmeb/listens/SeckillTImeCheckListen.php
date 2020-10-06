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

use Swoole\Timer;
use think\facade\Log;
use crmeb\interfaces\ListenerInterface;
use app\common\repositories\store\StoreSeckillActiveRepository;

class SeckillTImeCheckListen implements ListenerInterface
{
    public function handle($event): void
    {
        $make = app()->make(StoreSeckillActiveRepository::class);
        Timer::tick(1000 * 60, function () use ($make) {
            try {
                $make->valActiveStatus();
            } catch (\Exception $e) {
                Log::info('自动检测秒杀结束失败' . date('Y-m-d H:i:s', time()));
            }
        });
    }
}
