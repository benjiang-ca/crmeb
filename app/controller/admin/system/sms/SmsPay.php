<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\system\sms;


use crmeb\basic\BaseController;
use crmeb\services\YunxinSmsService;
use think\App;

/**
 * Class SmsPay
 * @package app\controller\admin\system\sms
 * @author xaboy
 * @day 2020-05-18
 */
class SmsPay extends BaseController
{
    /**
     * @var YunxinSmsService
     */
    protected $service;

    /**
     * Sms constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = YunxinSmsService::create();
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-05-18
     */
    public function number()
    {
        $countInfo = $this->service->count();
        if ($countInfo['status'] == 400) return app('json')->fail($countInfo['msg']);
        $info['account'] = $this->service->account();
        $info['number'] = $countInfo['data']['number'];
        $info['send_total'] = $countInfo['data']['send_total'];
        return app('json')->success($info);
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-05-18
     */
    public function price()
    {
        [$page, $limit] = $this->getPage();
        $mealInfo = $this->service->meal($page, $limit);
        if ($mealInfo['status'] == 400) return app('json')->fail($mealInfo['msg']);
        return app('json')->success($mealInfo['data']);
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-05-18
     */
    public function pay()
    {
        list($payType, $mealId, $price) = $this->request->params([
            ['payType', 'weixin'],
            ['mealId', 0],
            ['price', 0],
        ], true);
        $payInfo = $this->service->pay($payType, $mealId, $price, $this->request->adminId());
        if ($payInfo['status'] == 400) return app('json')->fail($payInfo['msg']);
        return app('json')->success($payInfo['data']);
    }

    /**
     * @author xaboy
     * @day 2020-05-18
     */
    public function notice()
    {
        //TODO 短信支付成功回调
    }
}