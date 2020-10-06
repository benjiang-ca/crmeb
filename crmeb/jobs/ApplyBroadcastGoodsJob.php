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


use app\common\repositories\store\broadcast\BroadcastGoodsRepository;
use crmeb\interfaces\JobInterface;
use crmeb\services\YunxinSmsService;
use think\facade\Log;

class ApplyBroadcastGoodsJob implements JobInterface
{

    public function fire($job, $goodsId)
    {
        $broadcastRoomGoodsRepository = app()->make(BroadcastGoodsRepository::class);
        $goods = $broadcastRoomGoodsRepository->get($goodsId);
        if ($goods) {
            try {
                $res = $broadcastRoomGoodsRepository->wxCreate($goods);
            } catch (\Exception $e) {
                $goods->error_msg = $e->getMessage();
                $goods->status = -1;
            }
            if (isset($res)) {
                $goods->goods_id = $res->goodsId;
                $goods->audit_id = $res->auditId;
                $goods->status = 1;
            }
            $goods->save();
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
