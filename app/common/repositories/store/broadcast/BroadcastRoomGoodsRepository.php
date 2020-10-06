<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/31
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\broadcast;


use app\common\dao\store\broadcast\BroadcastRoomGoodsDao;
use app\common\repositories\BaseRepository;

/**
 * Class BroadcastRoomGoodsRepository
 * @package app\common\repositories\store\broadcast
 * @author xaboy
 * @day 2020/7/31
 * @mixin BroadcastRoomGoodsDao
 */
class BroadcastRoomGoodsRepository extends BaseRepository
{
    public function __construct(BroadcastRoomGoodsDao $dao)
    {
        $this->dao = $dao;
    }
}