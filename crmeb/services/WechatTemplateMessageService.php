<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-20
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;

use app\common\repositories\store\order\StoreGroupOrderRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\order\StoreOrderStatusRepository;
use app\common\repositories\store\order\StoreRefundOrderRepository;
use app\common\repositories\user\UserBillRepository;
use app\common\repositories\user\UserExtractRepository;
use app\common\repositories\user\UserRechargeRepository;
use app\common\repositories\wechat\WechatUserRepository;
use crmeb\listens\pay\UserRechargeSuccessListen;
use crmeb\services\template\Template;
use app\common\repositories\user\UserRepository;
use think\facade\Route;

class WechatTemplateMessageService
{
    /**
     * TODO
     * @param array $data
     * @param string|null $link
     * @param string|null $color
     * @return bool
     * @author Qinii
     * @day 2020-06-29
     */
    public  function sendTemplate(array $data)
    {
        $res = $this->templateMessage($data['tempCode'],$data['id']);

        if(!$res || !is_array($res))return true;
        foreach($res as $item){
            if(is_array($item['uid'])){
                foreach ($item['uid'] as $value){
                    $openid = $this->getUserOpenID($value);
                    if ($openid) {
                        $this->send($openid,$item['tempCode'],$item['data'],'wechat',$item['link'],$item['color']);
                    }
                }
            }else{
                $openid = $this->getUserOpenID($item['uid']);
                if (!$openid) return true;
                $this->send($openid,$item['tempCode'],$item['data'],'wechat',$item['link'],$item['color']);
            }
        }
    }

    /**
     * TODO
     * @param $data
     * @param string|null $link
     * @param string|null $color
     * @return bool
     * @author Qinii
     * @day 2020-07-01
     */
    public function subscribeSendTemplate($data)
    {
        $res = $this->subscribeTemplateMessage($data['tempCode'],$data['id']);

        if(!$res || !is_array($res))return true;
        $openid = $this->getUserOpenID($res['uid']);
        if (!$openid) return true;
        $this->send($openid,$res['tempCode'],$res['data'],'subscribe',$res['link'],$res['color']);
    }

    /**
     * TODO
     * @param $uid
     * @return mixed
     * @author Qinii
     * @day 2020-06-29
     */
    public function getUserOpenID($uid)
    {
        $user = app()->make(UserRepository::class)->get($uid);
        return app()->make(WechatUserRepository::class)->idByOpenId((int)$user['wechat_user_id']);
    }


    /**
     * TODO
     * @param $openid
     * @param $tempCode
     * @param $data
     * @param $type
     * @param $link
     * @param $color
     * @return bool|mixed
     * @author Qinii
     * @day 2020-07-01
     */
    public function send($openid,$tempCode,$data,$type,$link,$color)
    {
        try{
            $template = new Template($type);
            $template->to($openid)->color($color);
            if ($link) $template->url($link);
            return $template->send($tempCode, $data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * TODO
     * @param string $tempCode
     * @param $id
     * @return array|bool
     * @author Qinii
     * @day 2020-07-01
     */
    public static function templateMessage(string $tempCode, int $id)
    {
        $bill_make = app()->make(UserBillRepository::class);
        $order_make = app()->make(StoreOrderRepository::class);
        $refund_make = app()->make(StoreRefundOrderRepository::class);
        $order_status_make = app()->make(StoreOrderStatusRepository::class);

        switch ($tempCode)
        {
            case 'ORDER_CREATE': //订单生成通知
                $res = $order_make->selectWhere(['group_order_id' => $id]);
                if(!$res) return false;
                foreach ($res as $item){
                    $order = $order_make->getWith($item['order_id'],'orderProduct');
                    $data[] = [
                        'tempCode' => 'ORDER_CREATE',
                        'uid' =>  explode(',',merchantConfig($item->mer_id,'merchant_admin_ids')),
                        'data' => [
                            'first' => '您有新的生成订单请注意查看',
                            'keyword1' => $item->create_time,
                            'keyword2' =>  '「'.$order['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                            'keyword3' => $item->order_sn,
                            'remark' => '查看详情'
                        ],
                        'link' => null,
                        'color' => null
                    ];
                }
                break;
            case 'ORDER_PAY_SUCCESS': //支付成功
                $group_order = app()->make(StoreGroupOrderRepository::class)->get($id);
                if(!$group_order) return false;
                $data[] = [
                    'tempCode' => 'ORDER_PAY_SUCCESS',
                    'uid' => $group_order->uid,
                    'data' => [
                        'first' => '您的订单已支付',
                        'keyword1' => $group_order->group_order_sn,
                        'keyword2' => $group_order->pay_price,
                        'remark' => '我们会经快发货，请耐心等待'
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/order_list/index?status=1',
                    'color' => null
                ];
                $res = $order_make->selectWhere(['group_order_id' => $id]);
                if(!$res) return false;
                foreach ($res as $item){
                    $data[] = [
                        'tempCode' => 'ORDER_PAY_SUCCESS',
                        'uid' => explode(',',merchantConfig($item->mer_id,'merchant_admin_ids')),
                        'data' => [
                            'first' => '您有新的支付订单请注意查看。',
                            'keyword1' => $item->order_sn,
                            'keyword2' => $item->pay_price,
                            'remark' => '请尽快发货。'
                        ],
                        'link' => null,
                        'color' => null
                    ];
                }
                break;
            case 'ORDER_POSTAGE_SUCCESS'://订单发货提醒(快递)
                $res = $order_make->get($id);
                if(!$res) return false;
                $data[] = [
                    'tempCode' => 'ORDER_POSTAGE_SUCCESS',
                    'uid' =>  $res->uid ,
                    'data' => [
                        'first' => '亲，宝贝已经启程了，好想快点来到你身边',
                        'keyword1' => $res['order_sn'],
                        'keyword2' => $res['delivery_name'],
                        'keyword3' => $res['delivery_id'],
                        'remark' => '请耐心等待收货哦。'
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/order_details/index?order_id='.$id,
                    'color' => null
                ];
                break;
            case 'ORDER_DELIVER_SUCCESS'://订单发货提醒(送货)
                $res = $order_make->getWith($id,'orderProduct');
                if(!$res) return false;
                $data[] = [
                    'tempCode' => 'ORDER_DELIVER_SUCCESS',
                    'uid' =>  $res->uid ,
                    'data' => [
                        'first' => '亲，宝贝已经启程了，好想快点来到你身边',
                        'keyword1' => '「'.$res['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                        'keyword2' => $res['create_time'],
                        'keyword3' => $res['user_address'],
                        'keyword4' => $res['delivery_name'],
                        'keyword5' => $res['delivery_id'],
                        'remark' => '请耐心等待收货哦。'
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/order_details/index?order_id='.$id,
                    'color' => null
                ];
                break;

            case 'ORDER_TAKE_SUCCESS': //订单收货通知
                $res = $order_make->getWith($id,'orderProduct');
                if(!$res) return false;
                $status = $order_status_make->getWhere(['order_id' => $id,'change_type' => 'take']);
                $data[] = [
                    'tempCode' => 'ORDER_TAKE_SUCCESS',
                    'uid' => $res->uid,
                    'data' => [
                        'first' => '亲，宝贝已经签收',
                        'keyword1' => $res['order_sn'],
                        'keyword2' => '已收货',
                        'keyword3' => $status['change_time'],
                        'keyword4' => '「'.$res['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                        'remark' => '请确认。'
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/order_details/index?order_id='.$id,
                    'color' => null
                ];
                break;
            case 'USER_BALANCE_CHANGE'://帐户资金变动提醒
                $res = $bill_make->get($id);
                if(!$res) return false;
                $data[] = [
                    'tempCode' => 'USER_BALANCE_CHANGE',
                    'uid' => $res->uid,
                    'data' => [
                        'first' => '资金变动提醒',
                        'keyword1' => '账户余额变动',
                        'keyword2' => $res['number'],
                        'keyword3' => $res['create_time'],
                        'keyword4' => $res['balance'],
                        'remark' => '请确认'
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/user_money/index',
                    'color' => null
                ];
                break;

            case 'ORDER_REFUND_STATUS'://退款申请通知
                $res = $refund_make->get($id);
                if(!$res) return false;
                $data[] = [
                    'tempCode' => 'ORDER_REFUND_STATUS',
                    'uid' => explode(',',merchantConfig($res->mer_id,'merchant_admin_ids')),
                    'data' => [
                        'first' => '您有新的退款申请',
                        'keyword1' => $res['refund_order_sn'],
                        'keyword2' => $res['refund_price'],
                        'keyword3' => $res['refund_message'],
                        'remark' => '请及时处理'
                    ],
                    'link' => null,
                    'color' => null
                ];
                break;
            case 'ORDER_REFUND_END'://退货确认提醒
                $res = $refund_make->getWith($id,['order']);
                if(!$res) return false;
                $order = $order_make->getWith($res['order_id'],'orderProduct');

                $data[] = [
                    'tempCode' => 'ORDER_REFUND_END',
                    'uid' => $res->uid,
                    'data' => [
                        'first' => '亲，您有一个订单已退款',
                        'keyword1' => $res['refund_order_sn'],
                        'keyword2' => $res['order']['order_sn'],
                        'keyword3' => $res['refund_price'],
                        'keyword4' => '「'.$order['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                        'remark' => '请查看确认'
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/refund/detail?id='.$id,
                    'color' => null
                ];
                break;

            case 'ORDER_REFUND_NOTICE'://退款进度提醒
                $status = [-1=>'审核未通过' ,1 => '商家已同意退货，请尽快将商品退回，并填写快递单号',];
                $res = $refund_make->getWith($id,['order']);
                if(!$res || !in_array($res['status'],[-1,1])) return false;
                $order = $order_make->getWith($res['order_id'],'orderProduct');
                $data[] = [
                    'tempCode' => 'ORDER_REFUND_NOTICE',
                    'uid' => $res->uid,
                    'data' => [
                        'first' => '退款进度提醒',
                        'keyword1' => $res['refund_order_sn'],
                        'keyword2' => $status[$res['status']],
                        'keyword3' => '「'.$order['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                        'keyword4' => $res['refund_price'],
                        'remark' => ''
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/refund/detail?id='.$id,
                    'color' => null
                ];
                break;
            default:
                return false;
                break;
        }
        return $data;
    }

    /**
     * TODO
     * @param string $tempCode
     * @param $id
     * @return array|bool
     * @author Qinii
     * @day 2020-07-01
     */
    public function subscribeTemplateMessage(string $tempCode, $id)
    {
        $user_make = app()->make(UserRechargeRepository::class);
        $order_make = app()->make(StoreOrderRepository::class);
        $refund_make = app()->make(StoreRefundOrderRepository::class);
        $order_group_make = app()->make(StoreGroupOrderRepository::class);
        $extract_make = app()->make(UserExtractRepository::class);
        switch($tempCode)
        {
            case 'ORDER_PAY_SUCCESS': //订单支付成功
                $res = $order_group_make->get($id);
                if(!$res) return false;
                $data = [
                    'tempCode' => 'ORDER_PAY_SUCCESS',
                    'uid' => $res->uid,
                    'data' => [
                        'character_string1' => $res->group_order_sn,
                        'amount2' => $res->pay_price,
                        'date3' => $res->pay_time,
                        'amount5' => $res->total_price,
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/order_list/index?status=1',
                    'color' => null
                ];
                break;
            case 'ORDER_POSTAGE_SUCCESS':  //订单发货提醒(送货)
                $res = $order_make->getWith($id,'orderProduct');
                if(!$res) return false;
                $data = [
                    'tempCode' => 'ORDER_POSTAGE_SUCCESS',
                    'uid' => $res->uid,
                    'data' => [
                        'thing8' => '「'.$res['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                        'character_string1' => $res->order_sn,
                        'name4' => $res->delivery_name,
                        'phone_number10' => $res->delivery_id,
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/order_details/index?order_id='.$id,
                    'color' => null
                ];
                break;
            case 'ORDER_DELIVER_SUCCESS': //订单发货提醒(快递)
                $res = $order_make->getWith($id,'orderProduct');
                if(!$res) return false;
                $data = [
                    'tempCode' => 'ORDER_DELIVER_SUCCESS',
                    'uid' => $res->uid,
                    'data' => [
                        'character_string2' => $res->delivery_id,
                        'thing1' => $res->delivery_name,
                        'time3' => date('Y-m-d H:i:s',time()),
                        'thing5' => '「'.$res['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/order_details/index?order_id='.$id,
                    'color' => null
                ];
                break;
            case 'ORDER_REFUND_NOTICE': //退款通知
                $status = [-1=>'审核未通过' ,1 => '商家已同意退货，请尽快将商品退回',3 => '退款成功'];
                $res = $refund_make->getWith($id,['order']);
                if(!$res || !in_array($res['status'],[-1,1,3])) return false;
                $order = $order_make->getWith($res['order_id'],'orderProduct');
                $data = [
                    'tempCode' => 'ORDER_REFUND_NOTICE',
                    'uid' => $res->uid,
                    'data' => [
                        'thing1' => $status[$res->status],
                        'thing2' => '「'.$order['orderProduct'][0]['cart_info']['product']['store_name'].'」等',
                        'character_string6' => $res->refund_order_sn,
                        'amount3' => $res->refund_price,
                        'thing13' => $res->fail_message ?? '',
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/refund/detail?id='.$id,
                    'color' => null
                ];
                break;
            case 'RECHARGE_SUCCESS': //充值成功
                $res = $user_make->get($id);
                if(!$res) return false;
                $data = [
                    'tempCode' => 'RECHARGE_SUCCESS',
                    'uid' => $res->uid,
                    'data' => [
                        'character_string1' => $res->order_id,
                        'amount3' => $res->price,
                        'amount6' => $res->give_price,
                        'date5' => $res->pay_time,
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/user_money/index',
                    'color' => null
                ];
                break;
            case 'USER_EXTRACT':  //提现结果通知
                $res = $extract_make->get($id);
                if(!$res) return false;
                $data = [
                    'tempCode' => 'USER_EXTRACT',
                    'uid' => $res->uid,
                    'data' => [
                        'thing1' => $res->status == -1 ? '未通过' : '已通过',
                        'amount2' => empty($res->bank_code)?(empty($res->alipay_code)?$res->wechat:$res->alipay_code):$res->bank_code,
                        'thing3' => $res->give_price,
                        'date4' => $res->create_time,
                    ],
                    'link' => rtrim(systemConfig('site_url'),'/').'/pages/users/user_spread_user/index',
                    'color' => null
                ];
                break;
            default:
                return false;
                break;
        }
       return $data;
    }



}
