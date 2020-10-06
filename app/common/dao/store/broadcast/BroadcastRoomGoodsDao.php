<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/31
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\broadcast;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\broadcast\BroadcastRoomGoods;
use app\common\repositories\store\order\StoreCartRepository;

class BroadcastRoomGoodsDao extends BaseDao
{

    protected function getModel(): string
    {
        return BroadcastRoomGoods::class;
    }

    public function clear($id)
    {
        return BroadcastRoomGoods::getDB()->where('broadcast_room_id', $id)->delete();
    }

    public function goodsId($id)
    {
        return BroadcastRoomGoods::getDB()->where('broadcast_room_id', $id)->column('broadcast_goods_id');
    }

    public function getGoodsList($roomId, $page, $limit)
    {
        $query = BroadcastRoomGoods::getDB()->where('broadcast_room_id', $roomId);
        $count = $query->count();
        $list = $query->page($page, $limit)->with('goods.product')->select()->toArray();
        $list = array_column($list, 'goods');
        $ids = array_column($list, 'broadcast_goods_id');
        if (count($ids)) {
            $sourcePayInfo = app()->make(StoreCartRepository::class)->getSourcePayInfo(1, $ids);
            $data = [];
            foreach ($sourcePayInfo as $item) {
                $data[$item['source_id']] = $item;
            }
            foreach ($list as $k => $goods) {
                $list[$k]['pay_num'] = $data[$goods['broadcast_goods_id']]['pay_num'] ?? 0;
                $list[$k]['pay_price'] = $data[$goods['broadcast_goods_id']]['pay_price'] ?? 0;
            }
        }
        return compact('list', 'count');
    }
}