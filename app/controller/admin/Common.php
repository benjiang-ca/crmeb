<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin;


use crmeb\basic\BaseController;
use app\common\repositories\store\order\StoreOrderProductRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\system\config\ConfigClassifyRepository;
use app\common\repositories\system\config\ConfigRepository;
use app\common\repositories\system\config\ConfigValueRepository;
use app\common\repositories\system\merchant\MerchantCategoryRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use app\common\repositories\user\UserRelationRepository;
use app\common\repositories\user\UserRepository;
use app\common\repositories\user\UserVisitRepository;
use crmeb\services\HttpService;
use think\App;
use think\facade\Db;

/**
 * Class Common
 * @package app\controller\admin
 * @author xaboy
 * @day 2020/6/25
 */
class Common extends BaseController
{

    /**
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function main()
    {
        $today = $this->mainGroup('today');
        $yesterday = $this->mainGroup('yesterday');
        $lastWeek = $this->mainGroup(date('Y-m-d', strtotime('- 7day')));
        $lastWeekRate = [];
        foreach ($lastWeek as $k => $item) {
            $lastWeekRate[$k] = $this->getRate($item, $today[$k]);
        }
        return app('json')->success(compact('today', 'yesterday', 'lastWeekRate'));
    }

    /**
     * @param $date
     * @return array
     * @author xaboy
     * @day 2020/6/25
     */
    protected function mainGroup($date)
    {
        $userRepository = app()->make(UserRepository::class);
        $storeOrderRepository = app()->make(StoreOrderRepository::class);
        $merchantRepository = app()->make(MerchantRepository::class);
        $userVisitRepository = app()->make(UserVisitRepository::class);
        $payPrice = $storeOrderRepository->dayOrderPrice($date);
        $userNum = $userRepository->newUserNum($date);
        $storeNum = $merchantRepository->dateMerchantNum($date);
        $visitUserNum = $userVisitRepository->dateVisitUserNum($date);
        $visitNum = $userVisitRepository->dateVisitNum($date);

        return compact('payPrice', 'userNum', 'storeNum', 'visitUserNum', 'visitNum');
    }

    /**
     * @param StoreOrderRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function order(StoreOrderRepository $repository)
    {
        $today = $repository->dayOrderPriceGroup('today')->toArray();
        $yesterday = $repository->dayOrderPriceGroup('yesterday')->toArray();
        $today = array_combine(array_column($today, 'time'), array_column($today, 'price'));
        $yesterday = array_combine(array_column($yesterday, 'time'), array_column($yesterday, 'price'));
        $time = getTimes();
        $order = [];
        foreach ($time as $item) {
            $order[] = [
                'time' => $item,
                'today' => $today[$item] ?? 0,
                'yesterday' => $yesterday[$item] ?? 0,
            ];
        }
        $todayPrice = $repository->dayOrderPrice('today');
        $yesterdayPrice = $repository->dayOrderPrice('yesterday');
        return app('json')->success(compact('order', 'todayPrice', 'yesterdayPrice'));
    }

    /**
     * @param StoreOrderRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function orderNum(StoreOrderRepository $repository)
    {
        $orderNum = $repository->dayOrderNum('today');
        $yesterdayNum = $repository->dayOrderNum('yesterday');
        $today = $repository->dayOrderNumGroup('today')->toArray();
        $today = array_combine(array_column($today, 'time'), array_column($today, 'total'));
        $monthOrderNum = $repository->dayOrderNum(date('Y/m/d', strtotime('first day of')) . ' 00:00:00' . '-' . date('Y/m/d H:i:s'));
        $beforeOrderNum = $repository->dayOrderNum(date('Y/m/01 00:00:00', strtotime('last Month')) . '-' . date('Y/m/d 00:00:00', strtotime('-1 day', strtotime('first day of'))));
        $monthRate = $this->getRate($beforeOrderNum, $monthOrderNum);
        $orderRate = $this->getRate($yesterdayNum, $orderNum);
        $time = getTimes();
        $data = [];
        foreach ($time as $item) {
            $data[] = [
                'total' => $today[$item] ?? 0,
                'time' => $item
            ];
        }
        $today = $data;
        return app('json')->success(compact('orderNum', 'today', 'monthOrderNum', 'monthRate', 'orderRate'));
    }

    /**
     * @param StoreOrderRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function orderUser(StoreOrderRepository $repository)
    {
        $orderNum = $repository->dayOrderUserNum('today');
        $yesterdayNum = $repository->dayOrderUserNum('yesterday');
        $today = $repository->dayOrderUserGroup('today')->toArray();
        $today = array_combine(array_column($today, 'time'), array_column($today, 'total'));
        $monthOrderNum = $repository->dayOrderUserNum(date('Y/m/d', strtotime('first day of')) . ' 00:00:00' . '-' . date('Y/m/d H:i:s'));
        $beforeOrderNum = $repository->dayOrderUserNum(gmdate('Y/m/01 00:00:00', strtotime('last Month')) . '-' . date('Y/m/d 00:00:00', strtotime('-1 day', strtotime('first day of'))));
        $monthRate = $this->getRate($beforeOrderNum, $monthOrderNum);
        $orderRate = $this->getRate($yesterdayNum, $orderNum);
        $time = getTimes();
        $data = [];
        foreach ($time as $item) {
            $data[] = [
                'total' => $today[$item] ?? 0,
                'time' => $item
            ];
        }
        $today = $data;
        return app('json')->success(compact('orderNum', 'today', 'monthOrderNum', 'monthRate', 'orderRate'));
    }

    /**
     * @param StoreOrderProductRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function merchantStock(StoreOrderProductRepository $repository)
    {
        $date = $this->request->param('date') ?: 'lately7';
        $total = $repository->dateProductNum($date);
        $list = $repository->orderProductGroup($date)->toArray();
        foreach ($list as &$item) {
            $item['rate'] = bcdiv($item['total'], $total, 2);
        }
        return app('json')->success(compact('list', 'total'));
    }

    /**
     * @param UserVisitRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function merchantVisit(UserVisitRepository $repository)
    {
        $date = $this->request->param('date') ?: 'lately7';
        $total = $repository->dateVisitMerchantTotal($date);
        $list = $repository->dateVisitMerchantNum($date)->toArray();
        foreach ($list as &$item) {
            $item['rate'] = bcdiv($item['total'], $total, 2);
        }
        return app('json')->success(compact('list', 'total'));
    }

    /**
     * @param StoreOrderRepository $repository
     * @param MerchantCategoryRepository $merchantCategoryRepository
     * @return mixed
     * @author xaboy
     * @day 2020/6/25
     */
    public function merchantRate(StoreOrderRepository $repository, MerchantCategoryRepository $merchantCategoryRepository)
    {
        $date = $this->request->param('date') ?: 'lately7';
        $total = $repository->dateOrderPrice($date);
        $list = $merchantCategoryRepository->dateMerchantPriceGroup($date)->toArray();
        $rate = 1;
        $pay_price = $total;
        foreach ($list as &$item) {
            $item['rate'] = bcdiv($item['pay_price'], $total, 2);
            $rate = bcsub($rate, $item['rate'], 2);
            $pay_price = bcsub($pay_price, $item['pay_price'], 2);
        }
        if ($rate > 0 && count($list)) {
            $list[] = [
                'pay_price' => $pay_price,
                'category_name' => '其他类',
                'rate' => $rate
            ];
        }
        return app('json')->success(compact('list', 'total'));
    }

    public function userData(UserRepository $repository, UserVisitRepository $visitRepository)
    {
        $date = $this->request->param('date') ?: 'lately7';
        $newUserList = $repository->userNumGroup($date)->toArray();
        $newUserList = array_combine(array_column($newUserList, 'time'), array_column($newUserList, 'new'));
        $visitList = $visitRepository->dateVisitNumGroup($date)->toArray();
        $visitList = array_combine(array_column($visitList, 'time'), array_column($visitList, 'total'));
        $base = $repository->beforeUserNum(getStartModelTime($date));
        $time = getDatesBetweenTwoDays(getStartModelTime($date), date('Y-m-d'));
        $userList = [];
        $before = $base;
        foreach ($time as $item) {
            $new = $newUserList[$item] ?? 0;
            $before += $new;
            $userList[] = [
                'total' => $before,
                'new' => $new,
                'visit' => $visitList[$item] ?? 0,
                'day' => $item
            ];
        }
        return app('json')->success($userList);
    }

    /**
     * @param $last
     * @param $today
     * @return int|string|null
     * @author xaboy
     * @day 2020/6/25
     */
    protected function getRate($last, $today)
    {
        if ($last == $today)
            return 0;
        else if ($last == 0)
            return $today;
        else if ($today == 0)
            return -$last;
        else
            return bcdiv(bcsub($today, $last, 2), $last, 2);
    }

    /**
     * @return mixed
     */
    public function check_auth()
    {
        return $this->checkAuthDecrypt();
    }

    /**
     * @return mixed
     */
    public function auth()
    {
        return $this->getAuth();
    }

    /**
     * 申请授权
     * @return mixed
     */
    public function auth_apply()
    {
        $data = $this->request->params([
            ['company_name', ''],
            ['domain_name', ''],
            ['order_id', ''],
            ['phone', ''],
            ['label', 10],
            ['captcha', ''],
        ]);
        if (!$data['company_name']) {
            return app('json')->fail('请填写公司名称');
        }
        if (!$data['domain_name']) {
            return app('json')->fail('请填写授权域名');
        }
        if (!$data['phone']) {
            return app('json')->fail('请填写手机号码');
        }
        if (!$data['order_id']) {
            return app('json')->fail('请填写订单id');
        }
        if (!$data['captcha']) {
            return app('json')->fail('请填写验证码');
        }
        $res = HttpService::postRequest('http://authorize.crmeb.net/api/auth_apply', $data);
        if ($res === false) {
            return app('json')->fail('申请失败,服务器没有响应!');
        }
        $res = json_decode($res, true);
        if (isset($res['status'])) {
            if ($res['status'] == 400) {
                return app('json')->fail($res['msg'] ?? "申请失败");
            } else {
                return app('json')->success($res['msg'] ?? '申请成功', $res);
            }
        }
        return app('json')->fail("申请授权失败!");
    }

    public function uploadConfig(ConfigRepository $repository)
    {
        return app('json')->success(formToData($repository->uploadForm()));
    }

    public function saveUploadConfig(ConfigRepository $repository)
    {
        $formData = $this->request->post();
        if (!count($formData)) return app('json')->fail('保存失败');
        $repository->saveUpload($formData);

        return app('json')->success('保存成功');
    }

    public function loginConfig()
    {
        $login_logo = systemConfig('sys_login_logo');
        $menu_logo = systemConfig('sys_menu_logo');
        $menu_slogo = systemConfig('sys_menu_slogo');
        $login_title = systemConfig('sys_login_title');
        $login_banner = systemGroupData('sys_login_banner');
        return app('json')->success(compact('login_banner', 'login_logo', 'login_title', 'menu_slogo', 'menu_logo'));
    }
}