<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/16
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;


use EasyWeChat\Payment\Notify;
use EasyWeChat\Payment\Payment;
use Symfony\Component\HttpFoundation\Request;

class PaymentService extends Payment
{
    public function getNotify()
    {
        $request = \request();
        $request = new Request($request->get(), $request->post(), [], [], [], $request->server(), $request->getContent());
        return new Notify($this->merchant, $request);
    }
}