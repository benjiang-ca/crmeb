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


use app\common\repositories\user\UserRechargeRepository;
use crmeb\interfaces\ListenerInterface;

class UserRechargeSuccessListen implements ListenerInterface
{

    public function handle($data): void
    {
        $orderSn = $data['order_sn'];
        app()->make(UserRechargeRepository::class)->paySuccess($orderSn);
    }
}