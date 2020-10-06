<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\broadcast;


use app\common\model\BaseModel;
use app\common\model\system\merchant\Merchant;

/**
 * Class BroadcastRoom
 * @package app\common\model\store\broadcast
 * @author xaboy
 * @day 2020/7/29
 */
class BroadcastRoom extends BaseModel
{

    /**
     * @return string|null
     * @author xaboy
     * @day 2020/7/29
     */
    public static function tablePk(): ?string
    {
        return 'broadcast_room_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/7/29
     */
    public static function tableName(): string
    {
        return 'broadcast_room';
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'mer_id', 'mer_id');
    }

    public function broadcast()
    {
        return $this->hasMany(BroadcastRoomGoods::class);
    }
}