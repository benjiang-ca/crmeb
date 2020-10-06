<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/17
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\listens\pay;


use app\common\repositories\store\order\StoreGroupOrderRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use crmeb\interfaces\ListenerInterface;

class OrderPaySuccessListen implements ListenerInterface
{

    public function handle($data): void
    {
        $orderSn = $data['order_sn'];
        $groupOrder = app()->make(StoreGroupOrderRepository::class)->getWhere(['group_order_sn' => $orderSn]);
        if (!$groupOrder || $groupOrder->paid == 1) return;
        app()->make(StoreOrderRepository::class)->paySuccess($groupOrder);
    }
}