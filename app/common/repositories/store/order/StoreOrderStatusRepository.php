<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/11
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreOrderStatusDao;
use app\common\repositories\BaseRepository;

/**
 * Class StoreOrderStatusRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/11
 * @mixin StoreOrderStatusDao
 */
class StoreOrderStatusRepository extends BaseRepository
{
    /**
     * StoreOrderStatusRepository constructor.
     * @param StoreOrderStatusDao $dao
     */
    public function __construct(StoreOrderStatusDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $order_id
     * @param $change_type
     * @param $change_message
     * @return \app\common\dao\BaseDao|\think\Model
     * @author xaboy
     * @day 2020/6/11
     */
    public function status($order_id, $change_type, $change_message)
    {
        return $this->dao->create(compact('order_id', 'change_message', 'change_type'));
    }

    public function search($id,$page, $limit)
    {
        $query = $this->dao->search($id);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count','list');
    }
}
