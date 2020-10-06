<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\admin;


//附件
use app\common\dao\system\admin\AdminDao;
use app\common\model\system\admin\Admin;
use app\common\repositories\BaseRepository;
use app\common\repositories\system\auth\RoleRepository;
use crmeb\exceptions\AuthException;
use crmeb\services\JwtTokenService;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Route;
use think\Model;


/**
 * Class BaseRepository
 * @package common\repositories
 * @mixin AdminDao
 */
class AdminRepository extends BaseRepository
{
    public function __construct(AdminDao $dao)
    {
        /**
         * @var AdminDao
         */
        $this->dao = $dao;
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
     * @day 2020-04-09
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->page($page, $limit)->hidden(['pwd', 'is_del', 'update_time'])->select();

        foreach ($list as $k => $role) {
            $list[$k]['rule_name'] = $role->roleNames();
        }
        return compact('list', 'count');
    }

    public function passwordEncode($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * 更新
     * @param int $id id
     * @param array $data 数组
     * @return int
     * @throws DbException
     * @author 张先生
     * @date 2020-03-26
     */
    public function update(int $id, array $data)
    {
        if (isset($data['roles']))
            $data['roles'] = implode(',', $data['roles']);
        return $this->dao->update($id, $data);
    }

    /**
     * @param int $id
     * @param $isSelf
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-04-20
     */
    public function passwordForm(int $id, $isSelf = false)
    {
        $form = Elm::createForm(Route::buildUrl($isSelf ? 'systemAdminEditPassword' : 'systemAdminPassword', $isSelf ? [] : compact('id'))->build(), [
            $rules[] = Elm::password('pwd', '密码')->required(),
            $rules[] = Elm::password('againPassword', '确认密码')->required(),
        ]);
        return $form->setTitle('修改密码');

    }

    /**
     * @param array $formData
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-04-20
     */
    public function editForm(array $formData)
    {
        $form = Elm::createForm(Route::buildUrl('systemAdminEdit')->build());
        $form->setRule([
            Elm::input('real_name', '管理员姓名')->required(),
            Elm::input('phone', '联系电话')
        ]);

        return $form->setTitle('修改信息')->formData($formData);
    }

    /**
     * @param int|null $id
     * @param array $formData
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020-04-08
     */
    public function form(?int $id = null, array $formData = []): Form
    {
        $form = Elm::createForm(is_null($id) ? Route::buildUrl('systemAdminCreate')->build() : Route::buildUrl('systemAdminUpdate', ['id' => $id])->build());

        $rules = [
            Elm::select('roles', '身份', [])->options(function () {
                $data = app()->make(RoleRepository::class)->getAllOptions(0);
                $options = [];

                foreach ($data as $value => $label) {
                    $options[] = compact('value', 'label');
                }
                return $options;
            })->multiple(true),
            Elm::input('real_name', '管理员姓名'),
            Elm::input('account', '账号')->required(),
            Elm::input('phone', ' 联系电话'),
        ];
        if (!$id) {
            $rules[] = Elm::password('pwd', '密码')->required();
            $rules[] = Elm::password('againPassword', '确认密码')->required();
        }
        $rules[] = Elm::switches('status', '是否开启', 1)->inactiveValue(0)->activeValue(1)->inactiveText('关闭')->activeText('开启');
        $form->setRule($rules);
        return $form->setTitle(is_null($id) ? '添加管理员' : '编辑管理员')->formData($formData);
    }

    /**
     * @param int $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-09
     */
    public function updateForm(int $id)
    {
        return $this->form($id, $this->dao->get($id)->toArray());
    }


    /**
     * @param string $account
     * @param string $password
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-10
     */
    public function login(string $account, string $password)
    {
        $adminInfo = $this->dao->accountByAdmin($account);
        if (!$adminInfo)
            throw new ValidateException('账号不存在');
        if ($adminInfo['status'] != 1)
            throw new ValidateException('账号已关闭');
        if (!password_verify($password, $adminInfo->pwd))
            throw new ValidateException('账号或密码错误');

        $adminInfo->last_time = date('Y-m-d H:i:s');
        $adminInfo->last_ip = app('request')->ip();
        $adminInfo->login_count++;
        $adminInfo->save();

        return $adminInfo;
    }

    /**
     * @param string $token
     * @param int $exp
     * @author xaboy
     * @day 2020-04-10
     */
    public function cacheToken(string $token, int $exp)
    {
        Cache::set('admin_' . $token, time() + $exp, $exp);
    }

    public function checkToken(string $token)
    {
        $has = Cache::has('admin_' . $token);
        if (!$has)
            throw new AuthException('无效的token');
        $lastTime = Cache::get('admin_' . $token);
        if (($lastTime + (intval(Config::get('admin.token_valid_exp', 15))) * 60) > time())
            throw new AuthException('token 已过期');
    }

    public function updateToken(string $token)
    {
        Cache::set('admin_' . $token, time(), intval(Config::get('admin.token_valid_exp', 15)) * 60);
    }

    public function clearToken(string $token)
    {
        Cache::delete('admin_' . $token);
    }

    /**
     * @param Admin $admin
     * @return array
     * @author xaboy
     * @day 2020-04-09
     */
    public function createToken(Admin $admin)
    {
        $service = new JwtTokenService();
        $token = $service->createToken($admin->admin_id, 'admin');
        $this->cacheToken($token['token'], $token['out']);
        return $token;
    }

    /**
     * 检测验证码
     * @param string $key key
     * @param string $code 验证码
     * @author 张先生
     * @date 2020-03-26
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
     * @day 2020-04-09
     */
    public function createLoginKey(string $code)
    {
        $key = uniqid(microtime(true), true);
        Cache::set('am_captcha' . $key, $code, Config::get('admin.captcha_exp', 5) * 60);
        return $key;
    }
}