<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\merchant;


use app\common\repositories\user\UserRepository;
use crmeb\basic\BaseController;
use app\common\repositories\store\order\StoreOrderProductRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\user\UserRelationRepository;
use app\common\repositories\user\UserVisitRepository;
use think\App;
use think\facade\Db;

/**
 * Class Common
 * @package app\controller\merchant
 * @author xaboy
 * @day 2020/6/25
 */
class Common extends BaseController
{
    /**
     * @var int|null
     */
    protected $merId;

    /**
     * Common constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->merId = $this->request->merId() ?: null;
    }

    /**
     * @param null $merId
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function main($merId = null)
    {
        $today = $this->mainGroup('today', $merId ?? $this->merId);
        $yesterday = $this->mainGroup('yesterday', $merId ?? $this->merId);
        $lastWeek = $this->mainGroup(date('Y-m-d', strtotime('- 7day')), $merId ?? $this->merId);
        $lastWeekRate = [];
        foreach ($lastWeek as $k => $item) {
            if ($item == $today[$k])
                $lastWeekRate[$k] = 0;
            else if ($item == 0)
                $lastWeekRate[$k] = $today[$k];
            else if ($today[$k] == 0)
                $lastWeekRate[$k] = -$item;
            else
                $lastWeekRate[$k] = bcdiv(bcsub($today[$k], $item, 2), $item, 2);
        }
        $day = date('Y-m-d');
        return $merId ? compact('today', 'yesterday', 'lastWeekRate', 'day') : app('json')->success(compact('today', 'yesterday', 'lastWeekRate', 'day'));
    }

    /**
     * @param $date
     * @param $merId
     * @return array
     * @author xaboy
     * @day 2020/6/25
     */
    public function mainGroup($date, $merId)
    {
        $userVisitRepository = app()->make(UserVisitRepository::class);
        $repository = app()->make(StoreOrderRepository::class);
        $relationRepository = app()->make(UserRelationRepository::class);
        $orderNum = $repository->dayOrderNum($date, $merId);
        $payPrice = $repository->dayOrderPrice($date, $merId);
        $payUser = $repository->dayOrderUserNum($date, $merId);
        $visitNum = $userVisitRepository->dateVisitUserNum($date, $merId);
        $likeStore = $relationRepository->dayLikeStore($date, $merId);
        return compact('orderNum', 'payPrice', 'payUser', 'visitNum', 'likeStore');
    }

    /**
     * @param StoreOrderRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function order(StoreOrderRepository $repository)
    {
        $date = $this->request->param('date') ?: 'lately7';
        $time = getDatesBetweenTwoDays(getStartModelTime($date), date('Y-m-d'));
        $list = $repository->orderGroupNum($date, $this->merId)->toArray();
        $list = array_combine(array_column($list, 'day'), $list);
        $data = [];
        foreach ($time as $item) {
            $data[] = [
                'day' => $item,
                'total' => $list[$item]['total'] ?? 0,
                'user' => $list[$item]['user'] ?? 0,
                'pay_price' => $list[$item]['pay_price'] ?? 0
            ];
        }
        return app('json')->success($data);
    }

    /**
     * @param UserRelationRepository $repository
     * @param StoreOrderRepository $orderRepository
     * @param UserVisitRepository $userVisitRepository
     * @return \think\response\Json
     * @author xaboy
     * @day 2020/9/24
     */
    public function user(StoreOrderRepository $orderRepository, UserVisitRepository $userVisitRepository)
    {
        $date = $this->request->param('date', 'today') ?: 'today';
        $visitUser = $userVisitRepository->dateVisitUserNum($date, $this->merId);
        $orderUser = $orderRepository->orderUserNum($date, null, $this->merId);
        $orderPrice = $orderRepository->orderPrice($date, null, $this->merId);
        $payOrderUser = $orderRepository->orderUserNum($date, 1, $this->merId);
        $payOrderPrice = $orderRepository->orderPrice($date, 1, $this->merId);
        $userRate = $payOrderUser ? bcdiv($payOrderPrice, $payOrderUser, 2) : 0;
        $orderRate = $visitUser ? bcdiv($orderUser, $visitUser, 2) : 0;
        $payOrderRate = $orderUser ? bcdiv($payOrderUser, $orderUser, 2) : 0;
        return app('json')->success(compact('visitUser', 'orderUser', 'orderPrice', 'payOrderUser', 'payOrderPrice', 'payOrderRate', 'userRate', 'orderRate'));
    }

    /**
     * @param StoreOrderRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function userRate(StoreOrderRepository $repository, UserRepository $userRepository)
    {
        $date = $this->request->param('date') ?: 'today';
        $uids = $repository->orderUserGroup($date, 1, $this->merId)->toArray();
        $userPayCount = $userRepository->idsByPayCount(array_column($uids, 'uid'));
        $user = count($uids);
        $oldUser = 0;
        $totalPrice = 0;
        $oldTotalPrice = 0;
        foreach ($uids as $uid) {
            $totalPrice = bcadd($uid['pay_price'], $totalPrice, 2);
            if (($userPayCount[$uid['uid']] ?? 0) > $uid['total']) {
                $oldUser++;
                $oldTotalPrice = bcadd($uid['pay_price'], $oldTotalPrice, 2);
            }
        }
        $newTotalPrice = bcsub($totalPrice, $oldTotalPrice, 2);
        $newUser = $user - $oldUser;
        return app('json')->success(compact('newTotalPrice', 'newUser', 'oldTotalPrice', 'oldUser', 'totalPrice', 'user'));
    }

    /**
     * @param StoreOrderProductRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function product(StoreOrderProductRepository $repository)
    {
        $date = $this->request->param('date', 'today') ?: 'today';
        return app('json')->success($repository->orderProductGroup($date, $this->merId));
    }

    public function productVisit(UserVisitRepository $repository)
    {
        $date = $this->request->param('date', 'today') ?: 'today';
        return app('json')->success($repository->dateVisitProductNum($date, $this->merId));
    }

    /**
     * @param ProductRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function productCart(ProductRepository $repository)
    {
        $date = $this->request->param('date', 'today') ?: 'today';
        return app('json')->success($repository->cartProductGroup($date, $this->merId));
    }
}