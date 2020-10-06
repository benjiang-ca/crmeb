<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/2
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api\user;


use crmeb\basic\BaseController;
use app\common\repositories\system\groupData\GroupDataRepository;
use app\common\repositories\user\UserRechargeRepository;
use app\common\repositories\user\UserRepository;
use app\common\repositories\wechat\WechatUserRepository;
use crmeb\services\WechatService;
use think\App;

class UserRecharge extends BaseController
{
    protected $repository;

    public function __construct(App $app, UserRechargeRepository $repository)
    {
        parent::__construct($app);
        $this->repository = $repository;
    }

    public function brokerage(UserRepository $userRepository)
    {
        $brokerage = (float)$this->request->param('brokerage');
        if ($brokerage <= 0)
            return app('json')->fail('请输入正确的充值金额!');
        $user = $this->request->userInfo();
        if ($user->brokerage_price < $brokerage)
            return app('json')->fail('剩余可用佣金不足' . $brokerage);
        $config = systemConfig(['recharge_switch', 'balance_func_status']);
        if (!$config['recharge_switch'] || !$config['balance_func_status'])
            return app('json')->fail('余额充值功能已关闭');
        $userRepository->switchBrokerage($user, $brokerage);
        return app('json')->success('转换成功');
    }

    public function recharge(GroupDataRepository $groupDataRepository)
    {
        [$type, $price, $rechargeId] = $this->request->params(['type', 'price', 'recharge_id'], true);
        if (!in_array($type, ['wechat', 'routine', 'h5']))
            return app('json')->fail('请选择正确的支付方式!');
        $wechatUserId = $this->request->userInfo()['wechat_user_id'];
        if (!$wechatUserId && in_array($type, ['wechat', 'routine']))
            return app('json')->fail('请关联微信' . ($type == 'wechat' ? '公众号' : '小程序') . '!');
        $config = systemConfig(['store_user_min_recharge', 'recharge_switch', 'balance_func_status']);
        if (!$config['recharge_switch'] || !$config['balance_func_status'])
            return app('json')->fail('余额充值功能已关闭');
        if ($rechargeId) {
            if (!intval($rechargeId))
                return app('json')->fail('请选择充值金额!');
            $rule = $groupDataRepository->merGet(intval($rechargeId), 0);
            if (!$rule || !isset($rule['price']) || !isset($rule['give']))
                return app('json')->fail('您选择的充值方式已下架!');
            $give = floatval($rule['give']);
            $price = floatval($rule['price']);
            if ($price <= 0)
                return app('json')->fail('请选择正确的充值金额!');
        } else {
            $price = floatval($price);
            if ($price <= 0)
                return app('json')->fail('请输入正确的充值金额!');
            if ($price < $config['store_user_min_recharge'])
                return app('json')->fail('最低充值' . floatval($config['store_user_min_recharge']));
            $give = 0;
        }
        $recharge = $this->repository->create($this->request->uid(), $price, $give, $type);
        $userRepository = app()->make(WechatUserRepository::class);
        if ($type == 'wechat') {
            $openId = $userRepository->idByOpenId($wechatUserId);
            if (!$openId)
                return app('json')->fail('请关联微信公众号!');
            $data = $this->repository->wxPay($openId, $recharge);
        } else if ($type == 'h5') {
            $data = $this->repository->wxH5Pay($recharge);
        } else {
            $openId = $userRepository->idByRoutineId($wechatUserId);
            if (!$openId)
                return app('json')->fail('请关联微信小程序!');
            $data = $this->repository->jsPay($openId, $recharge);
        }

        return app('json')->success(compact('type', 'data'));
    }
}
