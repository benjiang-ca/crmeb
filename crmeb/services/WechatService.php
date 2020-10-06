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


use app\common\repositories\system\config\ConfigValueRepository;
use app\common\repositories\wechat\WechatQrcodeRepository;
use app\common\repositories\wechat\WechatReplyRepository;
use app\common\repositories\wechat\WechatUserRepository;
use crmeb\exceptions\WechatException;
use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Material;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Payment\Order;
use EasyWeChat\Server\BadRequestException;
use EasyWeChat\Support\Collection;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use think\facade\Log;
use think\facade\Route;
use think\Response;

/**
 * Class WechatService
 * @package crmeb\services
 * @author xaboy
 * @day 2020-04-20
 */
class WechatService
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * WechatService constructor.
     * @param $config
     */
    public function __construct(array $config)
    {
        $this->application = new Application($config);
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020-04-24
     */
    public static function getConfig()
    {
        /** @var ConfigValueRepository $make */
        $make = app()->make(ConfigValueRepository::class);
        $wechat = $make->more([
            'wechat_appid', 'wechat_appsecret', 'wechat_token', 'wechat_encodingaeskey', 'wechat_encode'
        ], 0);
        $payment = $make->more(['site_url', 'pay_weixin_mchid', 'pay_weixin_client_cert', 'pay_weixin_client_key', 'pay_weixin_key', 'pay_weixin_open'], 0);
        $config = [
            'app_id' => trim($wechat['wechat_appid']),
            'secret' => trim($wechat['wechat_appsecret']),
            'token' => trim($wechat['wechat_token']),
            'guzzle' => [
                'timeout' => 10.0, // 超时时间（秒）
                'verify' => false
            ],
            'debug' => false,
        ];
        if ($wechat['wechat_encode'] > 0 && $wechat['wechat_encodingaeskey'])
            $config['aes_key'] = trim($wechat['wechat_encodingaeskey']);
        if (isset($payment['pay_weixin_open']) && $payment['pay_weixin_open'] == 1) {
            $config['payment'] = [
                'merchant_id' => trim($payment['pay_weixin_mchid']),
                'key' => trim($payment['pay_weixin_key']),
                'cert_path' => realpath('./public' . $payment['pay_weixin_client_cert']),
                'key_path' => realpath('./public' . $payment['pay_weixin_client_key']),
                'notify_url' => $payment['site_url'] . Route::buildUrl('wechatNotify')->build()
            ];
        }
        return $config;
    }

    /**
     * @return self
     * @author xaboy
     * @day 2020-04-24
     */
    public static function create()
    {
        return new self(self::getConfig());
    }

    /**
     * @return Application
     * @author xaboy
     * @day 2020-04-20
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param \think\Request $request
     * @return Response
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @author xaboy
     * @day 2020-04-26
     */
    public function serve(\think\Request $request)
    {
        $this->serverRequest($request);
        $this->wechatEventHook();
        return response($this->application->server->serve()->getContent());
    }

    protected function serverRequest(\think\Request $request)
    {
        $this->application->server->setRequest(new Request($request->get(), $request->post(), [], [], [], $request->server(), $request->getContent()));
    }

    /**
     * @throws InvalidArgumentException
     * @author xaboy
     * @day 2020-04-20
     */
    public function wechatEventHook()
    {
        $this->application->server->setMessageHandler(function ($message) {
            $openId = $message->FromUserName;
            $userInfo = $this->getUserInfo($openId);
            /** @var WechatUserRepository $wechatUserRepository */
            $wechatUserRepository = app()->make(WechatUserRepository::class);
            $users = $wechatUserRepository->syncUser($openId, $userInfo, true);

            $response = null;
            /** @var WechatReplyRepository $make * */
            $make = app()->make(WechatReplyRepository::class);
            event('WechatMessage', $message);
            switch ($message->MsgType) {
                case 'event':
                    switch (strtolower($message->Event)) {
                        case 'subscribe':
                            $response = $make->reply('subscribe');
                            if (isset($message->EventKey) && $message->EventKey) {
                                /** @var WechatQrcodeRepository $qr */
                                $qr = app()->make(WechatQrcodeRepository::class);
                                if ($qrInfo = $qr->ticketByQrcode($message->Ticket)) {
                                    $qrInfo->incTicket();
                                    if (strtolower($qrInfo['third_type']) == 'spread' && $users) {
                                        $spreadUid = $qrInfo['third_id'];
                                        if ($users[1]['uid'] == $spreadUid)
                                            return '自己不能推荐自己';
                                        else if ($users[1]['spread_uid'])
                                            return '已有推荐人!';
                                        try {
                                            $users[1]->setSpread($spreadUid);
                                        } catch (Exception $e) {
                                            return '绑定推荐人失败';
                                        }
                                    }

                                }
                            }
                            event('WechatEventSubscribe', $message);
                            break;
                        case 'unsubscribe':
                            $wechatUserRepository->unsubscribe($openId);
                            event('WechatEventUnsubscribe', $message);
                            break;
                        case 'scan':
                            $response = $make->reply('subscribe');
                            /** @var WechatQrcodeRepository $qr */
                            $qr = app()->make(WechatQrcodeRepository::class);
                            if ($message->EventKey && ($qrInfo = $qr->ticketByQrcode($message->Ticket))) {
                                $qrInfo->incTicket();
                                if (strtolower($qrInfo['third_type']) == 'spread' && $users) {
                                    $spreadUid = $qrInfo['third_id'];
                                    if ($users[1]['uid'] == $spreadUid)
                                        return '自己不能推荐自己';
                                    else if ($users[1]['spread_uid'])
                                        return '已有推荐人!';
                                    try {
                                        $users[1]->setSpread($spreadUid);
                                    } catch (Exception $e) {
                                        return '绑定推荐人失败';
                                    }
                                }
                            }
                            event('WechatEventScan', $message);
                            break;
                        case 'location':
                            event('wechatEventLocation', $message);
                            break;
                        case 'click':
                            $response = $make->reply($message->EventKey);
                            break;
                        case 'view':
                            event('wechatEventView', $message);
                            break;
                    }
                    break;
                case 'text':
                    $response = $make->reply($message->Content);
                    event('wechatMessageText', $message);
                    break;
                case 'image':
                    event('wechatMessageImage', $message);
                    break;
                case 'voice':
                    event('wechatMessageVoice', $message);
                    break;
                case 'video':
                    event('wechatMessageVideo', $message);
                    break;
                case 'location':
                    event('wechatMessageLocation', $message);
                    break;
                case 'link':
                    event('wechatMessageLink', $message);
                    break;
                // ... 其它消息
                default:
                    event('WechatMessageOther', $message);
                    break;
            }

            return $response ?? false;
        });
    }

    /**
     * @param $value
     * @return Collection
     * @author xaboy
     * @day 2020-04-20
     */
    public function qrcodeForever($value)
    {
        return $this->application->qrcode->forever($value);
    }

    /**
     * @param $value
     * @param int $expireSeconds
     * @return Collection
     * @author xaboy
     * @day 2020-04-20
     */
    public function qrcodeTemp($value, $expireSeconds = 2592000)
    {
        return $this->application->qrcode->temporary($value, $expireSeconds);
    }

    /**
     * @param string $openid
     * @param string $templateId
     * @param array $data
     * @param null $url
     * @param null $defaultColor
     * @return mixed
     * @author xaboy
     * @day 2020-04-20
     */
    public function sendTemplate(string $openid, string $templateId, array $data, $url = null, $defaultColor = null)
    {
        $notice = $this->application->notice->to($openid)->template($templateId)->andData($data);
        if ($url !== null) $notice->url($url);
        if ($defaultColor !== null) $notice->defaultColor($defaultColor);
        return $notice->send();
    }

    /**
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return Order
     * @author xaboy
     * @day 2020-04-20
     */
    protected function paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $total_fee = bcmul($total_fee, 100, 0);
        $order = array_merge(compact('out_trade_no', 'total_fee', 'attach', 'body', 'detail', 'trade_type'), $options);
        if (!is_null($openid)) $order['openid'] = $openid;
        if ($order['detail'] == '') unset($order['detail']);
        return new Order($order);
    }

    /**
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return Collection
     * @author xaboy
     * @day 2020-04-20
     */
    public function paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $order = $this->paymentOrder($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options);
        $result = $this->application->payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            return $result;
        } else {
            if ($result->return_code == 'FAIL') {
                throw new WechatException('微信支付错误返回：' . $result->return_msg);
            } else if (isset($result->err_code)) {
                throw new WechatException('微信支付错误返回：' . $result->err_code_des);
            } else {
                throw new WechatException('没有获取微信支付的预支付ID，请重新发起支付!');
            }
        }
    }

    /**
     * @param $openid
     * @param $out_trade_no
     * @param $total_fee
     * @param $attach
     * @param $body
     * @param string $detail
     * @param string $trade_type
     * @param array $options
     * @return array|string
     * @author xaboy
     * @day 2020-04-20
     */
    public function jsPay($openid, $out_trade_no, $total_fee, $attach, $body, $detail = '', $trade_type = 'JSAPI', $options = [])
    {
        $paymentPrepare = $this->paymentPrepare($openid, $out_trade_no, $total_fee, $attach, $body, $detail, $trade_type, $options);
        return $this->application->payment->configForJSSDKPayment($paymentPrepare->prepay_id);
    }

    /**
     * @param $orderNo
     * @param $refundNo
     * @param $totalFee
     * @param null $refundFee
     * @param null $opUserId
     * @param string $refundReason
     * @param string $type
     * @param string $refundAccount
     * @return Collection
     * @author xaboy
     * @day 2020-04-20
     */
    public function refund($orderNo, $refundNo, $totalFee, $refundFee = null, $opUserId = null, $refundReason = '', $type = 'out_trade_no', $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS')
    {
        $totalFee = floatval($totalFee);
        $refundFee = floatval($refundFee);
        return $this->application->payment->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $type, $refundAccount, $refundReason);
    }

    /**
     * @param $orderNo
     * @param array $opt
     * @author xaboy
     * @day 2020-04-20
     */
    public function payOrderRefund($orderNo, array $opt)
    {
        if (!isset($opt['pay_price'])) throw new WechatException('缺少pay_price');
        $totalFee = floatval(bcmul($opt['pay_price'], 100, 0));
        $refundFee = isset($opt['refund_price']) ? floatval(bcmul($opt['refund_price'], 100, 0)) : null;
        $refundReason = isset($opt['desc']) ? $opt['desc'] : '';
        $refundNo = isset($opt['refund_id']) ? $opt['refund_id'] : $orderNo;
        $opUserId = isset($opt['op_user_id']) ? $opt['op_user_id'] : null;
        $type = isset($opt['type']) ? $opt['type'] : 'out_trade_no';
        /*仅针对老资金流商户使用
        REFUND_SOURCE_UNSETTLED_FUNDS---未结算资金退款（默认使用未结算资金退款）
        REFUND_SOURCE_RECHARGE_FUNDS---可用余额退款*/
        $refundAccount = isset($opt['refund_account']) ? $opt['refund_account'] : 'REFUND_SOURCE_UNSETTLED_FUNDS';
        try {
            $res = ($this->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, $refundReason, $type, $refundAccount));
            if ($res->return_code == 'FAIL') throw new WechatException('退款失败:' . $res->return_msg);
            if (isset($res->err_code)) throw new WechatException('退款失败:' . $res->err_code_des);
        } catch (Exception $e) {
            throw new WechatException($e->getMessage());
        }
    }

    /**
     * array (
     *    'appid' => '****',
     *    'attach' => 'user_recharge',
     *    'bank_type' => 'COMM_CREDIT',
     *    'cash_fee' => '1',
     *    'fee_type' => 'CNY',
     *    'is_subscribe' => 'Y',
     *    'mch_id' => ''*****'',
     *    'nonce_str' => '5ee9dac1bc302',
     *    'openid' => '*****',
     *    'out_trade_no' => ''*****'',
     *    'result_code' => 'SUCCESS',
     *    'return_code' => 'SUCCESS',
     *    'sign' => '51'*****'',
     *    'time_end' => '20200617165651',
     *    'total_fee' => '1',
     *    'trade_type' => 'JSAPI',
     *    'transaction_id' => ''*****'',
     * )
     *
     * @throws FaultException
     * @author xaboy
     * @day 2020-04-20
     */
    public function handleNotify()
    {
        $this->application->payment = new PaymentService($this->application->merchant);
        //TODO 微信支付
        return $this->application->payment->handleNotify(function ($notify, $successful) {
            Log::info('微信支付成功回调' . var_export($notify, 1));
            if (!$successful) return false;
            try {
                event('pay_success_' . $notify['attach'], ['order_sn' => $notify['out_trade_no'], 'data' => $notify]);
            } catch (\Exception $e) {
                Log::info('微信支付回调失败:' . $e->getMessage());
                return false;
            }
            return true;
        });
    }

    /**
     * @param string $url
     * @return array|string
     * @author xaboy
     * @day 2020-04-20
     */
    public function jsSdk($url)
    {
        $apiList = ['editAddress', 'openAddress', 'updateTimelineShareData', 'updateAppMessageShareData', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone', 'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice', 'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation', 'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem', 'closeWindow', 'scanQRCode', 'chooseWXPay', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard'];
        $jsService = $this->application->js;
        $jsService->setUrl($url);
        try {
            return $jsService->config($apiList, false, false, false);
        } catch (Exception $e) {
            Log::info('微信参数获取失败' . $e->getMessage());
            return [];
        }

    }

    /**
     * 回复文本消息
     * @param string $content 文本内容
     * @return Text
     * @author xaboy
     * @day 2020-04-20
     */
    public static function textMessage($content)
    {
        return new Text(compact('content'));
    }

    /**
     * 回复图片消息
     * @param string $media_id 媒体资源 ID
     * @return Image
     * @author xaboy
     * @day 2020-04-20
     */
    public static function imageMessage($media_id)
    {
        return new Image(compact('media_id'));
    }

    /**
     * 回复视频消息
     * @param string $media_id 媒体资源 ID
     * @param string $title 标题
     * @param string $description 描述
     * @param null $thumb_media_id 封面资源 ID
     * @return Video
     * @author xaboy
     * @day 2020-04-20
     */
    public static function videoMessage($media_id, $title = '', $description = '...', $thumb_media_id = null)
    {
        return new Video(compact('media_id', 'title', 'description', 'thumb_media_id'));
    }

    /**
     * 回复声音消息
     * @param string $media_id 媒体资源 ID
     * @return Voice
     * @author xaboy
     * @day 2020-04-20
     */
    public static function voiceMessage($media_id)
    {
        return new Voice(compact('media_id'));
    }

    /**
     * 回复图文消息
     * @param string|array $title 标题
     * @param string $description 描述
     * @param string $url URL
     * @param string $image 图片链接
     * @return News|array<News>
     * @author xaboy
     * @day 2020-04-20
     */
    public static function newsMessage($title, $description = '...', $url = '', $image = '')
    {
        if (is_array($title)) {
            if (isset($title[0]) && is_array($title[0])) {
                $newsList = [];
                foreach ($title as $news) {
                    $newsList[] = self::newsMessage($news);
                }
                return $newsList;
            } else {
                $data = $title;
            }
        } else {
            $data = compact('title', 'description', 'url', 'image');
        }
        return new News($data);
    }

    /**
     * 回复文章消息
     * @param string|array $title 标题
     * @param string $thumb_media_id 图文消息的封面图片素材id（必须是永久 media_ID）
     * @param string $source_url 图文消息的原文地址，即点击“阅读原文”后的URL
     * @param string $content 图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     * @param string $author 作者
     * @param string $digest 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     * @param int $show_cover_pic 是否显示封面，0为false，即不显示，1为true，即显示
     * @param int $need_open_comment 是否打开评论，0不打开，1打开
     * @param int $only_fans_can_comment 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
     * @return Article
     * @author xaboy
     * @day 2020-04-20
     */
    public static function articleMessage($title, $thumb_media_id, $source_url, $content = '', $author = '', $digest = '', $show_cover_pic = 0, $need_open_comment = 0, $only_fans_can_comment = 1)
    {
        $data = is_array($title) ? $title : compact('title', 'thumb_media_id', 'source_url', 'content', 'author', 'digest', 'show_cover_pic', 'need_open_comment', 'only_fans_can_comment');
        return new Article($data);
    }

    /**
     * 回复素材消息
     * @param string $type [mpnews、 mpvideo、voice、image]
     * @param string $media_id 素材 ID
     * @return Material
     * @author xaboy
     * @day 2020-04-20
     */
    public static function materialMessage($type, $media_id)
    {
        return new Material($type, $media_id);
    }

    /**
     * @param $to
     * @param $message
     * @return bool
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @author xaboy
     * @day 2020-04-20
     */
    public function staffTo($to, $message)
    {
        $staff = $this->application->staff;
        $staff = is_callable($message) ? $staff->message($message()) : $staff->message($message);
        $res = $staff->to($to)->send();
        return $res;
    }

    /**
     * @param $openid
     * @return array
     * @author xaboy
     * @day 2020-04-20
     */
    public function getUserInfo($openid)
    {
        $userService = $this->application->user;
        $userInfo = is_array($openid) ? $userService->batchGet($openid) : $userService->get($openid);
        return $userInfo->toArray();
    }


    /**
     * 模板消息接口
     * @return \EasyWeChat\Notice\Notice
     */
    public function noticeService()
    {
        return $this->application->notice;
    }
}