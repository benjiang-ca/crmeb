<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-18
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;


use app\common\dao\system\sms\SmsRecordDao;
use app\common\repositories\store\broadcast\BroadcastRoomRepository;
use app\common\repositories\store\order\StoreGroupOrderRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\order\StoreRefundOrderRepository;
use app\common\repositories\store\service\StoreServiceRepository;
use app\common\repositories\system\config\ConfigValueRepository;
use crmeb\exceptions\SmsException;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Route;

/**
 * Class YunxinSmsService
 * @package crmeb\services
 * @author xaboy
 * @day 2020-05-18
 */
class YunxinSmsService
{
    /**
     * api
     */
    const API = 'https://sms.crmeb.net/api/';
   // const API = 'http://plat.crmeb.net/api/';

    /**
     * @var array
     */
    protected $config;

    /**
     * YunxinSmsService constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        if (isset($this->config['sms_token'])) {
            $this->config['sms_token'] = $this->getToken();
        }
    }

    public static function sendMessage($tempId, $id)
    {
        if ($tempId == 'DELIVER_GOODS_CODE') {
            if (!systemConfig('sms_fahuo_status')) return;
            $order = app()->make(StoreOrderRepository::class)->get($id);
            if (!$order || !$order->user_phone) return;
            $nickname = $order->user->nickname;
            $store_name = $order->orderProduct[0]['product']['store_name'] . count($order->orderProduct) ? '等' : '';
            $order_id = $order->order_sn;
            self::create()->send($order->user_phone, $tempId, compact('nickname', 'store_name', 'order_id'));
        } else if ($tempId == 'TAKE_DELIVERY_CODE') {
            if (!systemConfig('sms_take_status')) return;
            $order = app()->make(StoreOrderRepository::class)->get($id);
            if (!$order || !$order->user_phone) return;
            $order_id = $order->order_sn;
            $store_name = $order->orderProduct[0]['product']['store_name'] . count($order->orderProduct) ? '等' : '';
            self::create()->send($order->user_phone, $tempId, compact('store_name', 'order_id'));
        } else if ($tempId == 'PAY_SUCCESS_CODE') {
            if (!systemConfig('sms_pay_status')) return;
            $order = app()->make(StoreGroupOrderRepository::class)->get($id);
            if (!$order || !$order->user_phone) return;
            $pay_price = $order->pay_price;
            $order_id = $order->group_order_sn;
            self::create()->send($order->user_phone, $tempId, compact('pay_price', 'order_id'));
        } else if ($tempId == 'PRICE_REVISION_CODE') {
            if (!systemConfig('sms_revision_status')) return;
            $order = app()->make(StoreOrderRepository::class)->get($id);
            if (!$order || !$order->user_phone) return;
            $pay_price = $order->pay_price;
            $order_id = $order->order_sn;
            self::create()->send($order->user_phone, $tempId, compact('pay_price', 'order_id'));
        } else if ($tempId == 'ORDER_PAY_FALSE') {
            if (!systemConfig('sms_pay_false_status')) return;
            $order = app()->make(StoreGroupOrderRepository::class)->get($id);
            if (!$order || !$order->user_phone) return;
            $order_id = $order->group_order_sn;
            self::create()->send($order->user_phone, $tempId, compact('order_id'));
        } else if ($tempId == 'REFUND_FAIL_CODE') {
            if (!systemConfig('sms_refund_fail_status')) return;
            $order = app()->make(StoreRefundOrderRepository::class)->get($id);
            if (!$order || !$order->order->user_phone) return;
            $order_id = $order->refund_order_sn;
            $store_name = $order->refundProduct[0]->product['product']['store_name'] . count($order->refundProduct) ? '等' : '';
            self::create()->send($order->order->user_phone, $tempId, compact('order_id', 'store_name'));
        } else if ($tempId == 'REFUND_SUCCESS_CODE') {
            if (!systemConfig('sms_refund_success_status')) return;
            $order = app()->make(StoreRefundOrderRepository::class)->get($id);
            if (!$order || $order->order->user_phone) return;
            $order_id = $order->refund_order_sn;
            $store_name = $order->refundProduct[0]->product['product']['store_name'] . count($order->refundProduct) ? '等' : '';
            self::create()->send($order->order->user_phone, $tempId, compact('order_id', 'store_name'));
        } else if ($tempId == 'REFUND_CONFORM_CODE') {
            if (!systemConfig('sms_refund_confirm_status')) return;
            $order = app()->make(StoreRefundOrderRepository::class)->get($id);
            if (!$order || !$order->order->user_phone) return;
            $order_id = $order->order_sn;
            $store_name = $order->refundProduct[0]->product['product']['store_name'] . count($order->refundProduct) ? '等' : '';
            self::create()->send($order->order->user_phone, $tempId, compact('order_id', 'store_name'));
        } else if ($tempId == 'ADMIN_PAY_SUCCESS_CODE') {
            if (!systemConfig('sms_admin_pay_status')) return;
            $order = app()->make(StoreGroupOrderRepository::class)->get($id);
            if (!$order) return;
            foreach ($order->orderList as $_order) {
                self::sendMerMessage($_order->mer_id, $tempId, [
                    'order_id' => $_order->order_sn
                ]);
            }
        } else if ($tempId == 'ADMIN_RETURN_GOODS_CODE') {
            if (!systemConfig('sms_admin_return_status')) return;
            $order = app()->make(StoreRefundOrderRepository::class)->get($id);
            if (!$order) return;
            self::sendMerMessage($order->mer_id, $tempId, [
                'order_id' => $order->refund_order_sn
            ]);
        } else if ($tempId == 'ADMIN_TAKE_DELIVERY_CODE') {
            if (!systemConfig('sms_admin_take_status')) return;
            $order = app()->make(StoreOrderRepository::class)->get($id);
            if (!$order) return;
            self::sendMerMessage($order->mer_id, $tempId, [
                'order_id' => $order->order_sn
            ]);
        } else if ($tempId == 'ADMIN_DELIVERY_CODE') {
            if (!systemConfig('sms_admin_postage_status')) return;
            $order = app()->make(StoreOrderRepository::class)->get($id);
            if (!$order) return;
            self::sendMerMessage($order->mer_id, $tempId, [
                'order_id' => $order->order_sn
            ]);
        } else if ($tempId == 'BROADCAST_ROOM_CODE') {
            if (!systemConfig('sms_broadcast_room_status')) return;
            $room = app()->make(BroadcastRoomRepository::class)->get($id);
            if (!$room) return;
            self::create()->send($room->phone, $tempId, [
                'wechat' => $room->anchor_wechat
            ]);
        }
    }

    public static function sendMerMessage($merId, string $tempId, array $data)
    {
        $noticeServiceInfo = app()->make(StoreServiceRepository::class)->getNoticeServiceInfo($merId);
        $yunxinSmsService = self::create();
        foreach ($noticeServiceInfo as $service) {
            if (!$service['phone']) continue;
            $data['nickname'] = $service['nickname'];
            $yunxinSmsService->send($service['phone'], $tempId, $data);
        }
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-05-18
     */
    protected function getToken()
    {
        return md5($this->config['sms_account'] . $this->config['sms_token']);
    }

    /**
     * @author xaboy
     * @day 2020-05-18
     */
    public function checkConfig()
    {
        if (!isset($this->config['sms_account']) || !$this->config['sms_account']) {
            throw new ValidateException('请登录短信账户');
        }
        if (!isset($this->config['sms_token']) || !$this->config['sms_token']) {
            throw new ValidateException('请登录短信账户');
        }
    }

    /**
     * 发送注册验证码
     * @param $phone
     * @return mixed
     */
    public function captcha($phone)
    {
        return json_decode(HttpService::getRequest(self::API . 'sms/captcha', compact('phone')), true);
    }

    /**
     * 短信注册
     * @param $account
     * @param $password
     * @param $url
     * @param $phone
     * @param $code
     * @param $sign
     * @return mixed
     */
    public function register($account, $password, $url, $phone, $code, $sign)
    {
        return $this->registerData(compact('account', 'password', 'url', 'phone', 'code', 'sign'));
    }

    /**
     * @param array $data
     * @return mixed
     * @author xaboy
     * @day 2020-05-18
     */
    public function registerData(array $data)
    {
        return json_decode(HttpService::postRequest(self::API . 'sms/register', $data), true);
    }

    /**
     * 公共短信模板列表
     * @param array $data
     * @return mixed
     */
    public function publictemp(array $data = [])
    {
        $this->checkConfig();
        $data['account'] = $this->config['sms_account'];
        $data['token'] = $this->config['sms_token'];
        $data['source'] = 'crmeb_merchant';
        return json_decode(HttpService::postRequest(self::API . 'sms/publictemp', $data), true);
    }

    /**
     * 公共短信模板添加
     * @param $id
     * @param $tempId
     * @return mixed
     */
    public function use($id, $tempId)
    {
        $this->checkConfig();
        $data = [
            'account' => $this->config['sms_account'],
            'token' => $this->config['sms_token'],
            'id' => $id,
            'tempId' => $tempId,
        ];

        return json_decode(HttpService::postRequest(self::API . 'sms/use', $data), true);
    }

    /**
     * @param string $templateId
     * @return mixed
     * @author xaboy
     * @day 2020-05-18
     */
    public function getTemplateCode(string $templateId)
    {
        return Config::get('sms.template_id.' . $templateId);
    }

    /**
     * 发送短信
     * @param string $phone
     * @param string $templateId
     * @param array $data
     * @return bool|string
     * @throws SmsException
     */
    public function send(string $phone, string $templateId, array $data = [])
    {
        if (!$phone) {
            throw new SmsException('Mobile number cannot be empty');
        }

        $this->checkConfig();

        $formData['uid'] = $this->config['sms_account'];
        $formData['token'] = $this->config['sms_token'];
        $formData['mobile'] = $phone;
        $formData['template'] = $this->getTemplateCode($templateId);
        if (is_null($formData['template']))
            throw new SmsException('Missing template number');

        $formData['param'] = json_encode($data);
        $resource = json_decode(HttpService::postRequest(self::API . 'sms/send', $formData), true);
        if ($resource['status'] === 400) {
            throw new SmsException($resource['msg']);
        }else{
            app()->make(SmsRecordDao::class)->create([
                'uid' => $formData['uid'],
                'phone' => $phone,
                'content' => $resource['data']['content'],
                'template' => $resource['data']['template'],
                'record_id' => $resource['data']['id']
            ]);
        }
        return $resource;
    }

    /**
     * 账号信息
     * @return mixed
     */
    public function count()
    {
        $this->checkConfig();
        return json_decode(HttpService::postRequest(self::API . 'sms/userinfo', [
            'account' => $this->config['sms_account'],
            'token' => $this->config['sms_token']
        ]), true);
    }

    /**
     * 支付套餐
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function meal($page, $limit)
    {
        return json_decode(HttpService::getRequest(self::API . 'sms/meal', [
            'page' => $page,
            'limit' => $limit
        ]), true);
    }

    /**
     * 支付码
     * @param $payType
     * @param $mealId
     * @param $price
     * @param $attach
     * @param $notify
     * @return mixed
     */
    public function pay($payType, $mealId, $price, $attach, $notify = null)
    {
        $this->checkConfig();
        $data['uid'] = $this->config['sms_account'];
        $data['token'] = $this->config['sms_token'];
        $data['payType'] = $payType;
        $data['mealId'] = $mealId;
        $data['notify'] = $notify ?? Route::buildUrl('SmsNotify')->build();
        $data['price'] = $price;
        $data['attach'] = $attach;
        return json_decode(HttpService::postRequest(self::API . 'sms/mealpay', $data), true);
    }

    /**
     * 申请模板消息
     * @param $title
     * @param $content
     * @param $type
     * @return mixed
     */
    public function apply($title, $content, $type)
    {
        $this->checkConfig();
        $data['account'] = $this->config['sms_account'];
        $data['token'] = $this->config['sms_token'];
        $data['title'] = $title;
        $data['content'] = $content;
        $data['type'] = $type;
        return json_decode(HttpService::postRequest(self::API . 'sms/apply', $data), true);
    }

    /**
     * 短信模板列表
     * @param $data
     * @return mixed
     */
    public function template(array $data)
    {
        $this->checkConfig();
        return json_decode(HttpService::postRequest(self::API . 'sms/template', $data + [
                'account' => $this->config['sms_account'], 'token' => $this->config['sms_token']
            ]), true);
    }

    /**
     * 获取短息记录状态
     * @param $record_id
     * @return mixed
     */
    public function getStatus(array $record_id)
    {
        return json_decode(HttpService::postRequest(self::API . 'sms/status', [
            'record_id' => json_encode($record_id)
        ]), true);
    }

    /**
     * @return YunxinSmsService
     * @author xaboy
     * @day 2020-05-18
     */
    public static function create()
    {
        /** @var ConfigValueRepository $make */
        $make = app()->make(ConfigValueRepository::class);
        $config = $make->more(['sms_account', 'sms_token'], 0);

        return new static($config);
    }

    /**
     * @param string $sms_account
     * @param string $sms_token
     * @return $this
     * @author xaboy
     * @day 2020-05-18
     */
    public function setConfig(string $sms_account, string $sms_token)
    {
        $this->config = compact('sms_token', 'sms_account');
        $this->config['sms_token'] = $this->getToken();
        return $this;
    }

    /**
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-05-18
     */
    public function form()
    {
        return Elm::createForm(Route::buildUrl('smsCreate')->build(), [
            Elm::input('title', '模板名称'),
            Elm::input('content', '模板内容')->type('textarea'),
            Elm::radio('type', '模板类型', 1)->options([['label' => '验证码', 'value' => 1], ['label' => '通知', 'value' => 2], ['label' => '推广', 'value' => 3]])
        ])->setTitle('申请短信模板');
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-05-18
     */
    public function account()
    {
        $this->checkConfig();
        return $this->config['sms_account'];
    }

    /**
     * @Author:Qinii
     * @Date: 2020/9/19
     * @param $data
     * @return mixed
     */
    public function smsChange($data)
    {
        $this->checkConfig();
        $data['account'] = $this->config['sms_account'];
        return json_decode(HttpService::postRequest(self::API . 'sms/modify',$data),true);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/9/19
     * @param $phone
     * @param $code
     * @param $type
     * @return bool
     */
    public function checkSmsCode($phone,$code,$type)
    {
        $sms_key = $this->sendSmsKey($phone,$type);
        if (!$cache_code = Cache::get($sms_key)) return false;
        if ($code != $cache_code) return false;
        Cache::delete($sms_key);
        return true;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/9/19
     * @param $phone
     * @param string $type
     * @return string
     */
   public function sendSmsKey($phone,$type = 'login')
   {
       switch ($type)
       {
           case 'login': //登录
               return 'api_login_'.$phone;
               break;
           case 'binding': //绑定手机号
               return 'api_binding_'.$phone;
               break;
           case 'intention': //申请入住
               return 'merchant_intention_'.$phone;
               break;
           default:
               return 'crmeb_'.$phone;
               break;
       }
   }
}
