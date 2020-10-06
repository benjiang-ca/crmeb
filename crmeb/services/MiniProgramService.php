<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-11
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;


use crmeb\services\easywechat\broadcast\Client;
use crmeb\services\easywechat\broadcast\ServiceProvider;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Material\Temporary;
use EasyWeChat\MiniProgram\MiniProgram;
use EasyWeChat\Payment\Order;
use EasyWeChat\Payment\Payment;
use EasyWeChat\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use think\exception\ValidateException;
use think\facade\Log;
use think\facade\Route;

/**
 * Class MiniProgramService
 * @package crmeb\services
 * @author xaboy
 * @day 2020-05-11
 */
class MiniProgramService
{
    /**
     * @var MiniProgram
     */
    protected $service;

    /**
     * MiniProgramService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->service = new Application($config);
        $this->service->register(new ServiceProvider());
    }

    /**
     * @return Client
     * @author xaboy
     * @day 2020/7/29
     */
    public function miniBroadcast()
    {
        return $this->service->miniBroadcast;
    }

    /**
     * @return array[]
     * @author xaboy
     * @day 2020/6/18
     */
    public static function getConfig()
    {
        $wechat = systemConfig(['site_url', 'routine_appId', 'routine_appsecret']);
        $payment = systemConfig(['pay_routine_mchid', 'pay_routine_key', 'pay_routine_client_cert', 'pay_routine_client_key', 'pay_weixin_open']);
        return [
            'mini_program' => [
                'app_id' => $wechat['routine_appId'],
                'secret' => $wechat['routine_appsecret'],
                'token' => '',
                'aes_key' => '',
            ],
            'payment' => [
                'app_id' => $wechat['routine_appId'],
                'merchant_id' => trim($payment['pay_routine_mchid']),
                'key' => trim($payment['pay_routine_key']),
                'cert_path' => realpath('./public' . $payment['pay_routine_client_cert']),
                'key_path' => realpath('./public' . $payment['pay_routine_client_key']),
                'notify_url' => $wechat['site_url'] . Route::buildUrl('routineNotify')->build()
            ]
        ];
    }


    /**
     * @return MiniProgramService
     * @author xaboy
     * @day 2020/6/2
     */
    public static function create()
    {
        return new self(self::getConfig());
    }

    /**
     * 支付
     * @return Payment
     */
    public function paymentService()
    {
        return $this->service->payment;
    }

    /**
     * 小程序接口
     * @return MiniProgram
     */
    public function miniProgram()
    {
        return $this->service->mini_program;
    }

    /**
     * @return \EasyWeChat\Material\Material|mixed
     * @author xaboy
     * @day 2020/7/29
     */
    public function material()
    {
        return $this->service->mini_program->material_temporary;
    }

    /**
     * @param $sessionKey
     * @param $iv
     * @param $encryptData
     * @return mixed
     * @author xaboy
     * @day 2020/6/18
     */
    public function encryptor($sessionKey, $iv, $encryptData)
    {
        return $this->miniProgram()->encryptor->decryptData($sessionKey, $iv, $encryptData);
    }

    /**
     * 上传临时素材接口
     * @return Temporary
     */
    public function materialTemporaryService()
    {
        return $this->miniProgram()->material_temporary;
    }

    /**
     * 客服消息接口
     */
    public function staffService()
    {
        return $this->miniProgram()->staff;
    }

    /**
     * @param $code
     * @return mixed
     * @author xaboy
     * @day 2020/6/18
     */
    public function getUserInfo($code)
    {
        $userInfo = $this->miniProgram()->sns->getSessionKey($code);
        return $userInfo;
    }

    /**
     * @return \EasyWeChat\MiniProgram\QRCode\QRCode
     * @author xaboy
     * @day 2020/6/18
     */
    public function qrcodeService()
    {
        return $this->miniProgram()->qrcode;
    }

    /**
     * 生成支付订单对象
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return Order
     */
    protected function paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $total_fee = bcmul($total_fee, 100, 0);
        $order = array_merge(compact('openid', 'out_trade_no', 'total_fee', 'attach', 'body', 'detail', 'trade_type'), $options);
        if ($order['detail'] == '') unset($order['detail']);
        return new Order($order);
    }

    /**
     * 获得下单ID
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return mixed
     */
    public function paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $order = $this->paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options);
        $result = $this->paymentService()->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            return $result->prepay_id;
        } else {
            if ($result->return_code == 'FAIL') {
                throw new ValidateException('微信支付错误返回：' . $result->return_msg);
            } else if (isset($result->err_code)) {
                throw new ValidateException('微信支付错误返回：' . $result->err_code_des);
            } else {
                throw new ValidateException('没有获取微信支付的预支付ID，请重新发起支付!');
            }
        }
    }

    /**
     * 获得jsSdk支付参数
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return array|string
     */
    public function jsPay($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        return $this->paymentService()->configForJSSDKPayment($this->paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options));
    }

    /**
     * 使用商户订单号退款
     * @param $orderNo
     * @param $refundNo
     * @param $totalFee
     * @param null $refundFee
     * @param null $opUserId
     * @param string $refundReason
     * @param string $type
     * @param string $refundAccount
     * @return Collection|ResponseInterface
     */
    public function refund($orderNo, $refundNo, $totalFee, $refundFee = null, $opUserId = null, $refundReason = '', $type = 'out_trade_no', $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS')
    {
        $totalFee = floatval($totalFee);
        $refundFee = floatval($refundFee);
        return $this->paymentService()->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $type, $refundAccount, $refundReason);
    }

    /**
     * 发送订阅消息
     * @param string $touser 接收者（用户）的 openid
     * @param string $templateId 所需下发的订阅模板id
     * @param array $data 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
     * @param string $link 击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
     * @return \EasyWeChat\Support\Collection|null
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function sendSubscribeTemlate(string $touser, string $templateId, array $data, string $link = '')
    {
        return $this->miniprogram()->now_notice->to($touser)->template($templateId)->andData($data)->withUrl($link)->send();
    }


    /**
     * @param $orderNo
     * @param array $opt
     * @return bool
     * @author xaboy
     * @day 2020/6/18
     */
    public function payOrderRefund($orderNo, array $opt)
    {
        if (!isset($opt['pay_price'])) throw new ValidateException('缺少pay_price');
        $totalFee = floatval(bcmul($opt['pay_price'], 100, 0));
        $refundFee = isset($opt['refund_price']) ? floatval(bcmul($opt['refund_price'], 100, 0)) : null;
        $refundReason = isset($opt['desc']) ? $opt['desc'] : '';
        $refundNo = isset($opt['refund_id']) ? $opt['refund_id'] : $orderNo;
        $opUserId = isset($opt['op_user_id']) ? $opt['op_user_id'] : null;
        $type = isset($opt['type']) ? $opt['type'] : 'out_trade_no';
        $refundAccount = isset($opt['refund_account']) ? $opt['refund_account'] : 'REFUND_SOURCE_UNSETTLED_FUNDS';
        try {
            $res = ($this->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $refundReason, $type, $refundAccount));
            if ($res->return_code == 'FAIL') throw new ValidateException('退款失败:' . $res->return_msg);
            if (isset($res->err_code)) throw new ValidateException('退款失败:' . $res->err_code_des);
        } catch (\Exception $e) {
            throw new ValidateException($e->getMessage());
        }
        return true;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     * @author xaboy
     * @day 2020/6/18
     */
    public function handleNotify()
    {
        $this->service->payment = new PaymentService($this->service->merchant);
        return $this->service->payment->handleNotify(function ($notify, $successful) {
            Log::info('小程序支付回调' . var_export($notify, 1));
            if (!$successful) return;
            try {
                event('pay_success_' . $notify['attach'], ['order_sn' => $notify['out_trade_no'], 'data' => $notify]);
            } catch (\Exception $e) {
                Log::info('小程序支付回调失败:' . $e->getMessage());
                return false;
            }
            return true;
        });
    }
}
