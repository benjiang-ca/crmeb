<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/31
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens;


use app\common\repositories\store\broadcast\BroadcastGoodsRepository;
use app\common\repositories\store\broadcast\BroadcastRoomRepository;
use crmeb\interfaces\ListenerInterface;
use Swoole\Timer;
use think\facade\Log;

class SyncBroadcastStatusListen implements ListenerInterface
{

    public function handle($event): void
    {
        $broadcastRoomRepository = app()->make(BroadcastRoomRepository::class);
        $broadcastGoodsRepository = app()->make(BroadcastGoodsRepository::class);
        Timer::tick(1000 * 60 * 5, function () use ($broadcastGoodsRepository) {
            try {
                $broadcastGoodsRepository->syncGoodStatus();
            } catch (\Exception $e) {
                Log::error('同步直播商品:' . $e->getMessage());
            }
        });

        Timer::tick(1000 * 60 * 5, function () use ($broadcastRoomRepository) {
            try {
                $broadcastRoomRepository->syncRoomStatus();
            } catch (\Exception $e) {
                Log::error('同步直播间:' . $e->getMessage());
            }
        });
    }
}