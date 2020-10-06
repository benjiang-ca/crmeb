<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api;


use app\common\repositories\user\UserRepository;
use app\common\repositories\wechat\RoutineQrcodeRepository;
use app\common\repositories\wechat\WechatUserRepository;
use app\validate\api\UserAuthValidate;
use crmeb\basic\BaseController;
use crmeb\services\MiniProgramService;
use crmeb\services\WechatService;
use crmeb\services\YunxinSmsService;
use Exception;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Symfony\Component\HttpFoundation\Request;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;


/**
 * Class Auth
 * @package app\controller\api
 * @author xaboy
 * @day 2020-05-06
 */
class Auth extends BaseController
{

    public function test()
    {
    }

    /**
     * @param UserRepository $repository
     * @return mixed
     * @throws DbException
     * @author xaboy
     * @day 2020/6/1
     */
    public function login(UserRepository $repository)
    {
        $account = $this->request->param('account');
        if (!$account)
            return app('json')->fail('请输入账号');
        $user = $repository->accountByUser($this->request->param('account'));
        if (!$user)
            return app('json')->fail('账号或密码错误');
        if (!password_verify($pwd = (string)$this->request->param('password'), $user['pwd']))
            return app('json')->fail('账号或密码错误');
        $user = $repository->mainUser($user);
        $pid = $this->request->param('spread', 0);
        $repository->bindSpread($user, intval($pid));

        $tokenInfo = $repository->createToken($user);
        $repository->loginAfter($user);

        return app('json')->success($repository->returnToken($user, $tokenInfo));
    }


    /**
     * @return mixed
     * @author xaboy
     * @day 2020/6/1
     */
    public function userInfo()
    {
        $user = $this->request->userInfo()->hidden(['label_id', 'group_id', 'pwd', 'addres', 'card_id', 'last_time', 'last_ip', 'create_time', 'mark', 'status', 'spread_uid', 'spread_time', 'real_name', 'birthday', 'brokerage_price']);
        $user->append(['service', 'total_consume', 'total_collect_product', 'total_collect_store', 'total_coupon', 'total_visit_product', 'total_unread', 'total_recharge']);
        return app('json')->success($user->toArray());
    }

    /**
     * @param UserRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/1
     */
    public function logout(UserRepository $repository)
    {
        $repository->clearToken($this->request->token());
        return app('json')->success('退出登录');
    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-11
     */
    public function auth()
    {
        $request = $this->request;
        $oauth = WechatService::create()->getApplication()->oauth;
        $oauth->setRequest(new Request($request->get(), $request->post(), [], [], [], $request->server(), $request->getContent()));
        try {
            $wechatInfo = $oauth->user()->getOriginal();
        } catch (Exception $e) {
            return app('json')->fail('授权失败[001]', ['message' => $e->getMessage()]);
        }
        if (!isset($wechatInfo['nickname'])) {
            return app('json')->fail('授权失败[002]');
        }
        /** @var WechatUserRepository $make */
        $make = app()->make(WechatUserRepository::class);

        $user = $make->syncUser($wechatInfo['openid'], $wechatInfo);
        if (!$user)
            return app('json')->fail('授权失败[003]');
        /** @var UserRepository $make */
        $userRepository = app()->make(UserRepository::class);
        $user[1] = $userRepository->mainUser($user[1]);

        $pid = $this->request->param('spread', 0);
        $userRepository->bindSpread($user[1], intval($pid));

        $tokenInfo = $userRepository->createToken($user[1]);
        $userRepository->loginAfter($user[1]);

        return app('json')->success($userRepository->returnToken($user[1], $tokenInfo));
    }

    protected function returnToken($user, $tokenInfo)
    {

    }

    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-11
     */
    public function mpAuth()
    {
        list($code, $post_cache_key) = $this->request->params([
            'code',
            'cache_key',
        ], true);
        $session_key = Cache::get('eb_api_code_' . $post_cache_key);
        if (!$code && !$session_key)
            return app('json')->fail('授权失败,参数有误');
        $miniProgramService = MiniProgramService::create();
        if ($code && !$session_key) {
            try {
                $userInfoCong = $miniProgramService->getUserInfo($code);
                $session_key = $userInfoCong['session_key'];
                $cache_key = md5(time() . $code);
                Cache::set('eb_api_code_' . $cache_key, $session_key, 86400);
            } catch (Exception $e) {
                return app('json')->fail('获取session_key失败，请检查您的配置！', ['line' => $e->getLine(), 'message' => $e->getMessage()]);
            }
        }

        $data = $this->request->params([
            ['spread_spid', 0],
            ['spread_code', ''],
            ['iv', ''],
            ['encryptedData', ''],
        ]);

        try {
            //解密获取用户信息
            $userInfo = $miniProgramService->encryptor($session_key, $data['iv'], $data['encryptedData']);
        } catch (Exception $e) {
            if ($e->getCode() == '-41003') return app('json')->fail('获取会话密匙失败');
            throw $e;
        }
        if (!$userInfo || !isset($userInfo['openId'])) return app('json')->fail('openid获取失败');
        if (!isset($userInfo['unionId'])) $userInfo['unionId'] = '';

        /** @var WechatUserRepository $make */
        $make = app()->make(WechatUserRepository::class);
        $user = $make->syncRoutineUser($userInfo['openId'], $userInfo);
        if (!$user)
            return app('json')->fail('授权失败');
        /** @var UserRepository $make */
        $userRepository = app()->make(UserRepository::class);
        $user[1] = $userRepository->mainUser($user[1]);
        //获取是否有扫码进小程序
        if (isset($data['spread_code']) && ($info = app()->make(RoutineQrcodeRepository::class)->getRoutineQrcodeFindType($data['spread_code']))) {
            $data['spread_spid'] = $info['third_id'];
        }
        $userRepository->bindSpread($user[1], intval($data['spread_spid']));
        $tokenInfo = $userRepository->createToken($user[1]);
        $userRepository->loginAfter($user[1]);

        return app('json')->success($userRepository->returnToken($user[1], $tokenInfo));
    }

    public function getCaptcha()
    {
        $codeBuilder = new CaptchaBuilder(null, new PhraseBuilder(4));
        $key = uniqid(microtime(true), true);
        Cache::set('api_captche' . $key, $codeBuilder->getPhrase(), 300);
        $captcha = $codeBuilder->build()->inline();
        $code = $codeBuilder->getPhrase();
        return app('json')->success(compact('key', 'code', 'captcha'));
    }

    protected function checkCaptcha($uni, string $code): bool
    {
        $cacheName = 'api_captche' . $uni;
        if (!Cache::has($cacheName)) return false;
        $key = Cache::get($cacheName);
        $code = mb_strtolower($code, 'UTF-8');
        $res = password_verify($code, $key);
        if ($res) Cache::delete($cacheName);
        return $res;
    }

    public function verify(UserAuthValidate $validate)
    {
        $data = $this->request->params(['phone', 'code', 'key',['type','login']]);
        $validate->sceneVerify()->check($data);

        $sms_num_key = 'api.auth.num.' . $data['phone'];
        $num = Cache::get($sms_num_key) ? Cache::get($sms_num_key) : 0;
        if ($num > 2) {
            if (!$data['code'])
                return app('json')->make(402, '请输入验证码');
            if (!$this->checkCaptcha($data['key'], $data['code']))
                return app('json')->fail('验证码输入有误');
        }
        $sms =  (YunxinSmsService::create());
//        if(!env('APP_DEBUG', false)){
            try {
                $sms_code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
                $sms_time = systemConfig('sms_time') ? systemConfig('sms_time') : 30 ;
                $sms->send($data['phone'], 'VERIFICATION_CODE', ['code' => $sms_code,'time' => $sms_time]);
            } catch (Exception $e) {
                return app('json')->fail($e->getMessage());
            }
//        }else{
//            $sms_code =  1234;
//            $sms_time = 5;
//        }
        $sms_key = $sms->sendSmsKey($data['phone'],$data['type']);
        Cache::set($sms_key, $sms_code,$sms_time * 60);
        Cache::set($sms_num_key, $num + 1, 300);
        //'短信发送成功'
        return app('json')->success('短信发送成功');
    }


    public function smsLogin(UserAuthValidate $validate, UserRepository $repository)
    {
        $data = $this->request->params(['phone', 'sms_code', 'spread']);
        $validate->sceneSmslogin()->check($data);
        if (!(YunxinSmsService::create())->checkSmsCode($data['phone'], $data['sms_code'],'login'))
            return app('json')->fail('验证码不正确');
        $user = $repository->accountByUser($data['phone']);
        if (!$user) $user = $repository->registr($data['phone'], null);
        $user = $repository->mainUser($user);
        $repository->bindSpread($user, intval($data['spread']));

        $tokenInfo = $repository->createToken($user);
        $repository->loginAfter($user);

        return app('json')->success($repository->returnToken($user, $tokenInfo));
    }
}
