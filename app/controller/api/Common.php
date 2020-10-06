<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\api;


use app\common\repositories\system\CacheRepository;
use crmeb\basic\BaseController;
use app\common\repositories\store\shipping\ExpressRepository;
use app\common\repositories\store\StoreCategoryRepository;
use app\common\repositories\system\groupData\GroupDataRepository;
use app\common\repositories\user\UserVisitRepository;
use app\common\repositories\wechat\TemplateMessageRepository;
use crmeb\services\MiniProgramService;
use crmeb\services\UploadService;
use crmeb\services\WechatService;
use Exception;
use think\facade\Config;
use think\facade\Log;
use think\Response;
use think\response\Html;

/**
 * Class Common
 * @package app\controller\api
 * @author xaboy
 * @day 2020/5/28
 */
class Common extends BaseController
{
    /**
     * @return mixed
     * @author xaboy
     * @day 2020/5/28
     */
    public function hotKeyword()
    {
        $keyword = systemGroupData('hot_keyword');
        return app('json')->success($keyword);
    }

    public function express(ExpressRepository $repository)
    {
        return app('json')->success($repository->options());
    }

    public function menus()
    {
        return app('json')->success(['banner' => systemGroupData('my_banner'), 'menu' => systemGroupData('my_menus')]);
    }

    public function refundMessage()
    {
        return app('json')->success(explode("\n", systemConfig('refund_message')));
    }

    public function config()
    {
        $config = systemConfig(['hide_mer_status', 'mer_intention_open', 'share_info', 'share_title', 'share_pic', 'store_user_min_recharge', 'recharge_switch', 'balance_func_status', 'yue_pay_status', 'site_logo', 'site_name']);
        $make = app()->make(TemplateMessageRepository::class);
        $sys_intention_agree = app()->make(CacheRepository::class)->getResult('sys_intention_agree');
        if (!$sys_intention_agree) {
            $sys_intention_agree = systemConfig('sys_intention_agree');
        }
        $config['sys_intention_agree'] = $sys_intention_agree;
        $config['tempid'] = $make->getSubscribe();
        return app('json')->success($config);
    }

    /**
     * @param GroupDataRepository $repository
     * @return mixed
     * @author xaboy
     * @day 2020/6/3
     */
    public function userRechargeQuota(GroupDataRepository $repository)
    {
        $recharge_quota = $repository->groupDataId('user_recharge_quota', 0);
        $recharge_attention = explode("\n", systemConfig('recharge_attention'));
        return app('json')->success(compact('recharge_quota', 'recharge_attention'));
    }

    /**
     * @param $field
     * @return mixed
     * @author xaboy
     * @day 2020/5/28
     */
    public function uploadImage($field)
    {
        $file = $this->request->file($field);
        if (!$file)
            return app('json')->fail('请上传图片');
        $file = is_array($file) ? $file[0] : $file;
        validate(["$field|图片" => [
            'fileSize' => 2097152,
            'fileExt' => 'jpg,jpeg,png,bmp,gif',
            'fileMime' => 'image/jpeg,image/png,image/gif',
        ]])->check([$field => $file]);

        $upload = UploadService::create();
        $info = $upload->to('def')->move($field);
        if ($info === false) {
            return app('json')->fail($upload->getError());
        }
        $res = $upload->getUploadInfo();
        $res['dir'] = path_to_url($res['dir']);
        if (strpos($res['dir'], 'http') === false) $res['dir'] = $this->request->domain() . $res['dir'];
        return app('json')->success(['path' => $res['dir']]);
    }

    /**
     * @return Response
     * @author xaboy
     * @day 2020/6/3
     */
    public function wechatNotify()
    {
        try {
            return response(WechatService::create()->handleNotify()->getContent());
        } catch (Exception $e) {
            Log::info('支付回调失败:' . var_export([$e->getMessage(), $e->getFile() . ':' . $e->getLine()], true));
        }
    }

    public function routineNotify()
    {
        try {
            return response(MiniProgramService::create()->handleNotify()->getContent());
        } catch (Exception $e) {
            Log::info('支付回调失败:' . var_export([$e->getMessage(), $e->getFile() . ':' . $e->getLine()], true));
        }
        Log::info('小程序支付成功回调');
    }

    /**
     * 获取图片base64
     * @return mixed
     */
    public function get_image_base64()
    {
        list($imageUrl, $codeUrl) = $this->request->params([
            ['image', ''],
            ['code', ''],
        ], true);
        try {
            $codeTmp = $code = $codeUrl ? image_to_base64($codeUrl) : '';
            if (!$codeTmp) {
                $putCodeUrl = put_image($codeUrl);
                $code = $putCodeUrl ? image_to_base64('./runtime/temp' . $putCodeUrl) : '';
                $code && unlink('./runtime/temp' . $putCodeUrl);
            }

            $imageTmp = $image = $imageUrl ? image_to_base64($imageUrl) : '';
            if (!$imageTmp) {
                $putImageUrl = put_image($imageUrl);
                $image = $putImageUrl ? image_to_base64('./runtime/temp' . $putImageUrl) : '';
                $image && unlink('./runtime/temp' . $putImageUrl);
            }
            return app('json')->success(compact('code', 'image'));
        } catch (Exception $e) {
            return app('json')->fail($e->getMessage());
        }
    }

    public function home()
    {
        $banner = systemGroupData('home_banner', 1, 10);
        $menu = systemGroupData('home_menu');
        $hot = systemGroupData('home_hot', 1, 4);
        $ad = systemConfig(['home_ad_pic', 'home_ad_url']);
        $category = app()->make(StoreCategoryRepository::class)->getTwoLevel();
        return app('json')->success(compact('banner', 'menu', 'hot', 'ad', 'category'));
    }

    public function visit()
    {
        if (!$this->request->isLogin()) return app('json')->success();
        [$page, $type] = $this->request->params(['page', 'type'], true);
        $uid = $this->request->uid();
        if (!$page || !$uid) return app('json')->fail();
        $userVisitRepository = app()->make(UserVisitRepository::class);
        $type == 'routine' ? $userVisitRepository->visitSmallProgram($uid, $page) : $userVisitRepository->visitPage($uid, $page);
        return app('json')->success();
    }

    public function hotBanner($type)
    {
        if (!in_array($type, ['new', 'hot', 'best', 'good']))
            $data = [];
        else
            $data = systemGroupData($type . '_home_banner');
        return app('json')->success($data);
    }
}
