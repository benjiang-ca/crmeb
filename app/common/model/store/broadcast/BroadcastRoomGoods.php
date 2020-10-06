<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/31
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\broadcast;


use app\common\model\BaseModel;

class BroadcastRoomGoods extends BaseModel
{

    public static function tablePk(): ?string
    {
        return null;
    }

    public static function tableName(): string
    {
        return 'broadcast_room_goods';
    }

    public function goods()
    {
        return $this->hasOne(BroadcastGoods::class, 'broadcast_goods_id', 'broadcast_goods_id');
    }

    public function room()
    {
        return $this->hasOne(BroadcastRoom::class, 'broadcast_room_id', 'broadcast_room_id');
    }
}