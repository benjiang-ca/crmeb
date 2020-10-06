<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\user;


use app\common\dao\BaseDao;
use app\common\dao\user\UserDao;
use app\common\model\user\User;
use app\common\model\user\UserGroup;
use app\common\model\wechat\WechatUser;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\coupon\StoreCouponRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\system\attachment\AttachmentRepository;
use app\common\repositories\wechat\RoutineQrcodeRepository;
use crmeb\exceptions\AuthException;
use crmeb\jobs\AutoUserPosterJob;
use crmeb\jobs\SendNewPeopleCouponJob;
use crmeb\services\JwtTokenService;
use crmeb\services\QrcodeService;
use crmeb\services\UploadService;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use think\exception\ValidateException;
use think\facade\Queue;
use think\model\Relation;
use function GuzzleHttp\Psr7\str;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Route;
use think\Model;

/**
 * Class UserRepository
 * @package app\common\repositories\user
 * @author xaboy
 * @day 2020-04-28
 * @mixin UserDao
 */
class UserRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     * @param UserDao $dao
     */
    public function __construct(UserDao $dao)
    {
        $this->dao = $dao;
    }

    public function promoter($uid)
    {
        return $this->dao->update($uid, ['is_promoter' => 1, 'promoter_time' => date('Y-m-d H:i:s')]);
    }

    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-09
     */
    public function userForm($id)
    {
        $user = $this->dao->get($id);
        $user['uid'] = (string)$user['uid'];
        return Elm::createForm(Route::buildUrl('systemUserUpdate', compact('id'))->build(), [
            Elm::input('uid', '会员 ID', '')->disabled(true)->required(true),
            Elm::input('real_name', '真实姓名'),
            Elm::input('phone', '手机号'),
            Elm::date('birthday', '生日'),
            Elm::input('card_id', '身份证'),
            Elm::input('addres', '用户地址'),
            Elm::textarea('mark', '备注'),
            Elm::select('group_id', '会员分组')->options(function () {
                $data = app()->make(UserGroupRepository::class)->allOptions();
                $options = [];
                foreach ($data as $value => $label) {
                    $options[] = compact('value', 'label');
                }
                return $options;
            }),
            Elm::selectMultiple('label_id', '会员标签')->options(function () {
                $data = app()->make(UserLabelRepository::class)->allOptions();
                $options = [];
                foreach ($data as $value => $label) {
                    $value = (string)$value;
                    $options[] = compact('value', 'label');
                }
                return $options;
            }),
            Elm::radio('is_promoter', '推广员', 1)->options([
                ['value' => 0, 'label' => '关闭'],
                ['value' => 1, 'label' => '开启'],
            ])->required()
        ])->setTitle('编辑')->formData($user->toArray());
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-07
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where)->with(['spread' => function ($query) {
            $query->field('uid,nickname,spread_uid');
        }, 'group']);
        $make = app()->make(UserLabelRepository::class);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->select()->each(function ($item) use ($make) {
            return $item->label = count($item['label_id']) ? $make->labels($item['label_id']) : [];
        });

        return compact('count', 'list');
    }

    public function merList(string $keyword, $page, $limit)
    {
        $query = $this->dao->searchMerUser($keyword);
        $count = $query->count($this->dao->getPk());
        $list = $query->page($page, $limit)->setOption('field', [])->field('uid,nickname,avatar,user_type,sex')->select();
        return compact('count', 'list');
    }

    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-05-07
     */
    public function changeGroupForm($id)
    {
        $isArray = is_array($id);
        if (!$isArray)
            $user = $this->dao->get($id);

        /** @var UserGroupRepository $make */
        $data = app()->make(UserGroupRepository::class)->allOptions();
        return Elm::createForm(Route::buildUrl($isArray ? 'systemUserBatchChangeGroup' : 'systemUserChangeGroup', $isArray ? [] : compact('id'))->build(), [
            Elm::hidden('ids', $isArray ? $id : [$id]),
            Elm::select('group_id', '用户分组', $isArray ? '' : $user->group_id)->options(function () use ($data) {
                $options = [['label' => '不设置', 'value' => '0']];
                foreach ($data as $value => $label) {
                    $options[] = compact('value', 'label');
                }
                return $options;
            })
        ])->setTitle('修改用户分组');
    }

    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-05-07
     */
    public function changeLabelForm($id)
    {
        $isArray = is_array($id);
        if (!$isArray)
            $user = $this->dao->get($id);

        /** @var UserLabelRepository $make */
        $data = app()->make(UserLabelRepository::class)->allOptions();
        return Elm::createForm(Route::buildUrl($isArray ? 'systemUserBatchChangeLabel' : 'systemUserChangeLabel', $isArray ? [] : compact('id'))->build(), [
            Elm::hidden('ids', $isArray ? $id : [$id]),
            Elm::selectMultiple('label_id', '用户标签', $isArray ? [] : $user->label_id)->options(function () use ($data) {
                $options = [];
                foreach ($data as $value => $label) {
                    $value = (string)$value;
                    $options[] = compact('value', 'label');
                }
                return $options;
            })
        ])->setTitle('修改用户标签');
    }

    /**
     * @param $id
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-05-07
     */
    public function changeNowMoneyForm($id)
    {
        return Elm::createForm(Route::buildUrl('systemUserChangeNowMoney', compact('id'))->build(), [
            Elm::radio('type', '修改余额', 1)->options([
                ['label' => '增加', 'value' => 1],
                ['label' => '减少', 'value' => 0],
            ])->required(),
            Elm::number('now_money', '金额')->required()->min(0)
        ])->setTitle('修改用户余额');
    }

    /**
     * @param $id
     * @param $adminId
     * @param $type
     * @param $nowMoney
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-07
     */
    public function changeNowMoney($id, $adminId, $type, $nowMoney)
    {
        $user = $this->dao->get($id);
        Db::transaction(function () use ($id, $adminId, $user, $type, $nowMoney) {
            $balance = $type == 1 ? bcadd($user->now_money, $nowMoney, 2) : bcsub($user->now_money, $nowMoney, 2);
            $user->save(['now_money' => $balance]);
            /** @var UserBillRepository $make */
            $make = app()->make(UserBillRepository::class);
            if ($type == 1) {
                $make->incBill($id, 'now_money', 'sys_inc_money', [
                    'link_id' => $adminId,
                    'status' => 1,
                    'title' => '系统增加余额',
                    'number' => $nowMoney,
                    'mark' => '系统增加了' . floatval($nowMoney) . '余额',
                    'balance' => $balance
                ]);
            } else {
                $make->decBill($id, 'now_money', 'sys_dec_money', [
                    'link_id' => $adminId,
                    'status' => 1,
                    'title' => '系统减少余额',
                    'number' => $nowMoney,
                    'mark' => '系统减少了' . floatval($nowMoney) . '余额',
                    'balance' => $balance
                ]);
            }
        });


    }

    /**
     * @param $password
     * @return false|string|null
     * @author xaboy
     * @day 2020/6/22
     */
    public function encodePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param WechatUser $wechatUser
     * @return BaseDao|array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-28
     */
    public function syncWechatUser(WechatUser $wechatUser, $userType = 'wechat')
    {
        $user = $this->dao->wechatUserIdBytUser($wechatUser->wechat_user_id);
        $request = request();

        if ($user) {
            $user->save([
                'nickname' => $wechatUser['nickname'] ?: '',
                'avatar' => $wechatUser['headimgurl'] ?: '',
                'sex' => $wechatUser['sex'] ?: 0,
                'last_time' => date('Y-m-d H:i:s'),
                'last_ip' => $request->ip(),
            ]);
        } else
            $user = $this->create($userType, [
                'account' => 'wx' . $wechatUser->wechat_user_id . time(),
                'wechat_user_id' => $wechatUser->wechat_user_id,
                'pwd' => $this->encodePassword(123456),
                'nickname' => $wechatUser['nickname'] ?: '',
                'avatar' => $wechatUser['headimgurl'] ?: '',
                'sex' => $wechatUser['sex'] ?: 0,
                'spread_uid' => 0,
                'is_promoter' => 0,
                'last_time' => date('Y-m-d H:i:s'),
                'last_ip' => $request->ip()
            ]);

        return $user;
    }

    /**
     * @param string $type
     * @param array $userInfo
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020-04-28
     */
    public function create(string $type, array $userInfo)
    {
        $userInfo['user_type'] = $type;
        $user = $this->dao->create($userInfo);
        try {
            Queue::push(SendNewPeopleCouponJob::class, $user->uid);
        } catch (\Exception $e) {
        }
        return $user;
    }

    /**
     * @param User $user
     * @return array
     * @author xaboy
     * @day 2020-04-29
     */
    public function createToken(User $user)
    {
        $service = new JwtTokenService();
        $token = $service->createToken($user->uid, 'user');
        $this->cacheToken($token['token'], $token['out']);
        return $token;
    }

    /**
     * //TODO 登录成功后
     * @param User $user
     * @author xaboy
     * @day 2020/6/22
     */
    public function loginAfter(User $user)
    {
        $user->last_time = date('Y-m-d H:i:s', time());
        $user->last_ip = request()->ip();
        $user->save();
    }


    /**
     * @param string $token
     * @param int $exp
     * @author xaboy
     * @day 2020-04-29
     */
    public function cacheToken(string $token, int $exp)
    {
        Cache::set('user_' . $token, time() + $exp, $exp);
    }


    /**
     * @param string $token
     * @author xaboy
     * @day 2020-04-29
     */
    public function checkToken(string $token)
    {
        $has = Cache::has('user_' . $token);
        if (!$has)
            throw new AuthException('无效的token');
        $lastTime = Cache::get('user_' . $token);
        if (($lastTime + (intval(Config::get('admin.user_token_valid_exp', 15))) * 60) > time())
            throw new AuthException('token 已过期');
    }


    /**
     * @param string $token
     * @author xaboy
     * @day 2020-04-29
     */
    public function updateToken(string $token)
    {
        Cache::set('user_' . $token, time(), intval(Config::get('admin.user_token_valid_exp', 15)) * 60);
    }


    /**
     * @param string $token
     * @author xaboy
     * @day 2020-04-29
     */
    public function clearToken(string $token)
    {
        Cache::delete('user_' . $token);
    }

    /**
     * @param string $key
     * @param string $code
     * @author xaboy
     * @day 2020/6/1
     */
    public function checkCode(string $key, string $code)
    {
        $_code = Cache::get('am_captcha' . $key);
        if (!$_code) {
            throw new ValidateException('验证码过期');
        }

        if (strtolower($_code) != strtolower($code)) {
            throw new ValidateException('验证码错误');
        }

        //删除code
        Cache::delete('am_captcha' . $key);
    }


    /**
     * @param string $code
     * @return string
     * @author xaboy
     * @day 2020/6/1
     */
    public function createLoginKey(string $code)
    {
        $key = uniqid(microtime(true), true);
        Cache::set('am_captcha' . $key, $code, Config::get('admin.captcha_exp', 5) * 60);
        return $key;
    }

    public function registr(string $phone, ?string $pwd, $user_type = 'h5')
    {
        $pwd = $pwd ? $this->encodePassword($pwd) : $this->encodePassword($this->dao->defaultPwd());
        $data = [
            'account' => $phone,
            'pwd' => $pwd,
            'nickname' => substr($phone, 0, 3) . '****' . substr($phone, 7, 4),
            'avatar' => '',
            'phone' => $phone,
            'last_ip' => app('request')->ip()
        ];
        return $this->create($user_type, $data);
    }

    public function routineSpreadImage(User $user)
    {
        //小程序
        $name = md5('urt' . $user['uid'] . $user['is_promoter'] . date('Ymd')) . '.jpg';
        $attachmentRepository = app()->make(AttachmentRepository::class);
        $imageInfo = $attachmentRepository->getWhere(['attachment_name' => $name]);
        $spreadBanner = systemGroupData('spread_banner');
        if (!count($spreadBanner)) return [];
        $siteName = systemConfig('site_name');
        $siteUrl = systemConfig('site_url');
        $uploadType = (int)systemConfig('upload_type') ?: 1;
        //检测远程文件是否存在
        if (isset($imageInfo['attachment_src']) && strstr($imageInfo['attachment_src'], 'http') !== false && curl_file_exist($imageInfo['attachment_src']) === false) {
            $imageInfo->delete();
            $imageInfo = null;
        }
        if (!$imageInfo) {
            $routineQrcodeRepository = app()->make(RoutineQrcodeRepository::class);
            $res = $routineQrcodeRepository->getShareCode($user['uid'], 'spread', '', '');
            if (!$res) throw new ValidateException('二维码生成失败');
            $upload = UploadService::create($uploadType);
            $uploadRes = $upload->to('routine/spread/code')->validate()->stream($res['res'], $name);
            if ($uploadRes === false) {
                throw new ValidateException($upload->getError());
            }
            $imageInfo = $upload->getUploadInfo();
            $imageInfo['image_type'] = $uploadType;
            $imageInfo['dir'] = path_to_url($imageInfo['dir']);
            if (strpos($imageInfo['dir'], 'http') === false) $imageInfo['dir'] = request()->domain() . $imageInfo['dir'];
            $attachmentRepository->create($uploadType, -1, $user->uid, [
                'attachment_category_id' => 0,
                'attachment_name' => $imageInfo['name'],
                'attachment_src' => $imageInfo['dir']
            ]);
            $routineQrcodeRepository->setRoutineQrcodeFind($res['id'], ['status' => 1, 'url_time' => date('Y-m-d H:i:s'), 'qrcode_url' => $imageInfo['dir']]);
            $urlCode = $imageInfo['dir'];
        } else $urlCode = $imageInfo['attachment_src'];
        $siteUrlHttps = set_http_type($siteUrl, request()->isSsl() ? 0 : 1);
        $filelink = [
            'Bold' => 'public/font/Alibaba-PuHuiTi-Regular.otf',
            'Normal' => 'public/font/Alibaba-PuHuiTi-Regular.otf',
        ];
        if (!file_exists($filelink['Bold'])) throw new ValidateException('缺少字体文件Bold');
        if (!file_exists($filelink['Normal'])) throw new ValidateException('缺少字体文件Normal');
        $resRoutine = true;
        foreach ($spreadBanner as $key => &$item) {
            $posterInfo = '海报生成失败:(';
            $config = array(
                'image' => array(
                    array(
                        'url' => $urlCode,     //二维码资源
                        'stream' => 0,
                        'left' => 114,
                        'top' => 790,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 120,
                        'height' => 120,
                        'opacity' => 100
                    )
                ),
                'text' => array(
                    array(
                        'text' => $user['nickname'],
                        'left' => 250,
                        'top' => 840,
                        'fontPath' => $filelink['Bold'],     //字体文件
                        'fontSize' => 16,             //字号
                        'fontColor' => '40,40,40',       //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '邀请您加入' . $siteName,
                        'left' => 250,
                        'top' => 880,
                        'fontPath' => $filelink['Normal'],     //字体文件
                        'fontSize' => 16,             //字号
                        'fontColor' => '40,40,40',       //字体颜色
                        'angle' => 0,
                    )
                ),
                'background' => $item['pic']
            );
            $resRoutine = $resRoutine && $posterInfo = setSharePoster($config, 'routine/spread/poster');
            if (!is_array($posterInfo)) throw new ValidateException($posterInfo);
            $posterInfo['dir'] = path_to_url($posterInfo['dir']);
            if (strpos($posterInfo['dir'], 'http') === false) $posterInfo['dir'] = setHttpType(request()->domain() . $posterInfo['dir']);
            if ($resRoutine) {
                $attachmentRepository->create($uploadType, -1, $user->uid, [
                    'attachment_category_id' => 0,
                    'attachment_name' => $posterInfo['name'],
                    'attachment_src' => $posterInfo['dir']
                ]);
                $item['poster'] = $posterInfo['dir'];
            }
        }
        return $spreadBanner;
    }

    public function wxSpreadImage(User $user)
    {
        $name = md5('uwx' . $user['uid'] . $user['is_promoter'] . date('Ymd')) . '.jpg';
        $spreadBanner = systemGroupData('spread_banner');
        if (!count($spreadBanner)) return [];
        $siteName = systemConfig('site_name');
        $attachmentRepository = app()->make(AttachmentRepository::class);
        $imageInfo = $attachmentRepository->getWhere(['attachment_name' => $name]);
        $siteUrl = systemConfig('site_url');
        $uploadType = (int)systemConfig('upload_type') ?: 1;
        $resWap = true;
        //检测远程文件是否存在
        if (isset($imageInfo['attachment_src']) && strstr($imageInfo['attachment_src'], 'http') !== false && curl_file_exist($imageInfo['attachment_src']) === false) {
            $imageInfo->delete();
            $imageInfo = null;
        }
        if (!$imageInfo) {
            $codeUrl = set_http_type($siteUrl . '?spread=' . $user['uid'], request()->isSsl() ? 0 : 1);//二维码链接
            $imageInfo = app()->make(QrcodeService::class)->getQRCodePath($codeUrl, $name);
            if (is_string($imageInfo)) throw new ValidateException('二维码生成失败');
            $imageInfo['dir'] = path_to_url($imageInfo['dir']);
            if (strpos($imageInfo['dir'], 'http') === false) $imageInfo['dir'] = request()->domain() . $imageInfo['dir'];

            $attachmentRepository->create(systemConfig('upload_type') ?: 1, -1, $user->uid, [
                'attachment_category_id' => 0,
                'attachment_name' => $imageInfo['name'],
                'attachment_src' => $imageInfo['dir']
            ]);
            $urlCode = $imageInfo['dir'];
        } else $urlCode = $imageInfo['attachment_src'];
        $siteUrl = set_http_type($siteUrl, request()->isSsl() ? 0 : 1);
        $filelink = [
            'Bold' => 'public/font/Alibaba-PuHuiTi-Regular.otf',
            'Normal' => 'public/font/Alibaba-PuHuiTi-Regular.otf',
        ];
        if (!file_exists($filelink['Bold'])) throw new ValidateException('缺少字体文件Bold');
        if (!file_exists($filelink['Normal'])) throw new ValidateException('缺少字体文件Normal');
        foreach ($spreadBanner as $key => &$item) {
            $posterInfo = '海报生成失败:(';
            $config = array(
                'image' => array(
                    array(
                        'url' => $urlCode,     //二维码资源
                        'stream' => 0,
                        'left' => 114,
                        'top' => 790,
                        'right' => 0,
                        'bottom' => 0,
                        'width' => 120,
                        'height' => 120,
                        'opacity' => 100
                    )
                ),
                'text' => array(
                    array(
                        'text' => $user['nickname'],
                        'left' => 250,
                        'top' => 840,
                        'fontPath' => $filelink['Bold'],     //字体文件
                        'fontSize' => 16,             //字号
                        'fontColor' => '40,40,40',       //字体颜色
                        'angle' => 0,
                    ),
                    array(
                        'text' => '邀请您加入' . $siteName,
                        'left' => 250,
                        'top' => 880,
                        'fontPath' => $filelink['Normal'],     //字体文件
                        'fontSize' => 16,             //字号
                        'fontColor' => '40,40,40',       //字体颜色
                        'angle' => 0,
                    )
                ),
                'background' => $item['pic']
            );
            $resWap = $resWap && $posterInfo = setSharePoster($config, 'wap/spread/poster');
            if (!is_array($posterInfo)) throw new ValidateException('海报生成失败');
            $posterInfo['dir'] = path_to_url($posterInfo['dir']);
            if (strpos($posterInfo['dir'], 'http') === false) $posterInfo['dir'] = request()->domain() . $posterInfo['dir'];

            $attachmentRepository->create($uploadType, -1, $user->uid, [
                'attachment_category_id' => 0,
                'attachment_name' => $posterInfo['name'],
                'attachment_src' => $posterInfo['dir']
            ]);
            if ($resWap) {
                if ($posterInfo['image_type'] == 1)
                    $item['wap_poster'] = rtrim($siteUrl, '/') . $posterInfo['thumb_path'];
                else
                    $item['wap_poster'] = set_http_type($posterInfo['thumb_path'], 1);
            }
        }
        return $spreadBanner;
    }

    public function getUsername($uid)
    {
        return User::getDB()->where('uid', $uid)->value('nickname');
    }

    /**
     * @param $uid
     * @param $inc
     * @param string $type
     * @author xaboy
     * @day 2020/6/22
     */
    public function incBrokerage($uid, $inc, $type = '+')
    {
        $weekKey = 'b_top_' . date('Y-m');
        $moneyKey = 'b_top_' . monday();
        //TODO 佣金周榜
        $brokerage = Cache::zscore($weekKey, $uid);
        $brokerage = $type == '+' ? bcadd($brokerage, $inc, 2) : bcsub($brokerage, $inc, 2);
        Cache::zadd($weekKey, $brokerage, $uid);

        //TODO 佣金月榜
        $brokerage = Cache::zscore($moneyKey, $uid);
        $brokerage = $type == '+' ? bcadd($brokerage, $inc, 2) : bcsub($brokerage, $inc, 2);
        Cache::zadd($moneyKey, $brokerage, $uid);
    }

    public function brokerageWeekTop($page, $limit)
    {
        $key = 'b_top_' . monday();
        return $this->topList($key, $page, $limit);
    }

    public function brokerageMonthTop($page, $limit)
    {
        $key = 'b_top_' . date('Y-m');
        return $this->topList($key, $page, $limit);
    }

    /**
     * //TODO 绑定上下级关系
     * @param User $user
     * @param int $spreadUid
     * @throws DbException
     * @author xaboy
     * @day 2020/6/22
     */
    public function bindSpread(User $user, int $spreadUid)
    {
        if ($spreadUid && !$user->spread_uid && $user->uid != $spreadUid) {
            Db::transaction(function () use ($spreadUid, $user) {
                $user->spread_uid = $spreadUid;
                $user->spread_time = date('Y-m-d H:i:s');
                $this->dao->update($spreadUid, [
                    'spread_count' => Db::raw('spread_count +1')
                ]);
                $user->save();
                //TODO 推广人月榜
                Cache::zincrby('s_top_' . date('Y-m'), 1, $spreadUid);
                //TODO 推广人周榜
                Cache::zincrby('s_top_' . monday(), 1, $spreadUid);
            });
        }
    }

    public function topList($key, $page, $limit)
    {
        $res = Cache::zrevrange($key, ($page - 1) * $limit, $limit, true);
        $ids = array_keys($res);
        $index = array_flip($ids);
        $count = Cache::zcard($key);
        $list = count($ids) ? $this->dao->users($ids, 'uid,avatar,nickname')->toArray() : [];
        foreach ($list as $k => $v) {
            $list[$k]['count'] = $res[$v['uid']] ?? 0;
        }
        $sort = array_column($list, 'count');
        array_multisort($sort, SORT_DESC, $list);
        return compact('count', 'list');
    }

    public function spreadWeekTop($page, $limit)
    {
        $key = 's_top_' . monday();
        return $this->topList($key, $page, $limit);
    }

    public function spreadMonthTop($page, $limit)
    {
        $key = 's_top_' . date('Y-m');
        return $this->topList($key, $page, $limit);
    }

    /**
     * @param $uid
     * @param $nickname
     * @param $sort
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/22
     */
    public function getOneLevelList($uid, $nickname, $sort, $page, $limit)
    {
        $query = $this->search(['spread_uid' => $uid, 'nickname' => $nickname, 'sort' => $sort]);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('uid,avatar,nickname,pay_count,pay_price,spread_count,spread_time')->page($page, $limit)->select();
        return compact('list', 'count');
    }

    /**
     * @param $uid
     * @param $nickname
     * @param $sort
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/22
     */
    public function getTwoLevelList($uid, $nickname, $sort, $page, $limit)
    {
        $ids = $this->dao->getSubIds($uid);
        if (count($ids)) {
            $query = $this->search(['spread_uids' => $ids, 'nickname' => $nickname, 'sort' => $sort]);
            $count = $query->count();
            $list = $query->setOption('field', [])->field('uid,avatar,nickname,pay_count,pay_price,spread_count,spread_time')->page($page, $limit)->select();
        } else {
            $list = [];
            $count = 0;
        }
        return compact('list', 'count');
    }

    public function getLevelList($uid, array $where, $page, $limit)
    {
        if (!$where['level']) {
            $ids = $this->dao->getSubIds($uid);
            $ids[] = $uid;
            $where['spread_uids'] = $ids;
        } else if ($where['level'] == 2) {
            $ids = $this->dao->getSubIds($uid);
            if (!count($ids)) return ['list' => [], 'count' => 0];
            $where['spread_uids'] = $ids;
        } else {
            $where['spread_uid'] = $uid;
        }
        $query = $this->search($where);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('uid,avatar,nickname,is_promoter,pay_count,pay_price,spread_count,create_time')->page($page, $limit)->select();
        return compact('list', 'count');
    }


    /**
     * @param $uid
     * @param $page
     * @param $limit
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/26
     */
    public function subOrder($uid, $page, $limit, array $where = [])
    {
        if (isset($where['level'])) {
            if (!$where['level']) {
                $ids = $this->dao->getSubIds($uid);
                $subIds = $ids ? $this->dao->getSubAllIds($ids) : [];
            } else if ($where['level'] == 2) {
                $ids = $this->dao->getSubIds($uid);
                $subIds = $ids ? $this->dao->getSubAllIds($ids) : [];
                $ids = [];
            } else {
                $ids = $this->dao->getSubIds($uid);
                $subIds = [];
            }
        } else {
            $ids = $this->dao->getSubIds($uid);
            $subIds = $ids ? $this->dao->getSubAllIds($ids) : [];
        }
        $all = array_merge($ids, $subIds);
        if (!count($all)) return ['count' => 0, 'list' => []];
        $query = app()->make(StoreOrderRepository::class)->usersOrderQuery($where, $all);
        $count = $query->count();
        $list = $query->page($page, $limit)->field('uid,order_sn,pay_time,extension_one,extension_two')->with(['user' => function ($query) {
            $query->field('avatar,nickname,uid');
        }])->select()->toArray();
        foreach ($list as $k => $item) {
            $list[$k]['brokerage'] = in_array($item['uid'], $ids) ? $item['extension_one'] : $item['extension_two'];
            unset($list[$k]['extension_one'], $list[$k]['extension_two']);
        }
        return compact('count', 'list');
    }

    /**
     * @param User $user
     * @return User
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/7/2
     */
    public function mainUser(User $user): User
    {
        if (!$user->main_uid || $user->uid == $user->main_uid) return $user;
        $switchUser = $this->dao->get($user->main_uid);
        if (!$switchUser) return $user;
        if ($user->wechat_user_id && !$switchUser->wechat_user_id) {
            $switchUser->wechat_user_id = $user->wechat_user_id;
            $switchUser->save();
        }
        return $switchUser;
    }

    public function switchUser(User $user, $uid)
    {
        if ($user->uid == $uid || !$this->dao->existsWhere(['uid' => $uid, 'phone' => $user->phone]))
            throw new ValidateException('操作失败');
        $this->dao->update($user->uid, ['main_uid' => $uid]);
        $switchUser = $this->dao->get($uid);
        return $switchUser;
    }

    public function returnToken($user, $tokenInfo)
    {
        $user = $user->hidden(['label_id', 'group_id', 'main_uid', 'pwd', 'addres', 'card_id', 'last_time', 'last_ip', 'create_time', 'mark', 'status', 'spread_uid', 'spread_time', 'real_name', 'birthday', 'brokerage_price'])->toArray();
        return [
            'token' => $tokenInfo['token'],
            'exp' => $tokenInfo['out'],
            'user' => $user
        ];
    }

    public function switchBrokerage(User $user, $brokerage)
    {
        $user->now_money = bcadd($user->now_money, $brokerage, 2);
        $user->brokerage_price = bcsub($user->brokerage_price, $brokerage, 2);
        Db::transaction(function () use ($brokerage, $user) {
            $user->save();
            app()->make(UserBillRepository::class)->incBill($user->uid, 'now_money', 'brokerage', [
                'link_id' => 0,
                'status' => 1,
                'title' => '佣金转入余额',
                'number' => $brokerage,
                'mark' => '成功转入余额' . floatval($brokerage) . '元',
                'balance' => $user->now_money
            ]);
            app()->make(UserBillRepository::class)->decBill($user->uid, 'brokerage', 'now_money', [
                'link_id' => 0,
                'status' => 1,
                'title' => '佣金转入余额',
                'number' => $brokerage,
                'mark' => '成功转入余额' . floatval($brokerage) . '元',
                'balance' => $user->brokerage_price
            ]);
        });
    }
}
