<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/4
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api\store\order;


use app\common\repositories\store\order\StoreOrderRepository;
use crmeb\basic\BaseController;
use think\App;
use think\exception\HttpResponseException;

class StoreOrderVerify extends BaseController
{
    protected $user;

    protected $merId;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $user = $this->request->userInfo();
        if (isset($user->service->is_verify) && $user->service->is_verify == 1) {
            $this->merId = $user->service->mer_id;
            $this->user = $user;
        } else {
            throw new HttpResponseException(app('json')->fail('没有权限'));
        }
    }

    public function lst(StoreOrderRepository $repository)
    {
        [$page, $limit] = $this->getPage();
        return app('json')->success($repository->getList([
            'service_id' => $this->user->service_id,
            'mer_id' => $this->merId
        ], $page, $limit));
    }

    public function detail($id, StoreOrderRepository $repository)
    {
        $order = $repository->codeByDetail($id);
        if (!$order) return app('json')->fail('订单不存在');
        if ($order->mer_id != $this->merId)
            return app('json')->fail('没有权限查询该订单');
        return app('json')->success($order->toArray());
    }

    public function verify($id, StoreOrderRepository $repository)
    {
        $repository->verifyOrder($id, $this->merId, $this->user->service->service_id);
        return app('json')->success('订单核销成功');
    }
}