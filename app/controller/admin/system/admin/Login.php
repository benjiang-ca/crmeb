<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller\admin\system\admin;


use crmeb\basic\BaseController;
use app\common\repositories\system\admin\AdminRepository;
use app\validate\admin\LoginValidate;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class Login extends BaseController
{
    protected $repository;

    public function __construct(App $app, AdminRepository $repository)
    {

        parent::__construct($app);
        $this->repository = $repository;
    }

    /**
     * @param LoginValidate $validate
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-10
     */
    public function login(LoginValidate $validate)
    {
        $data = $this->request->params(['account', 'password', 'code', 'key']);
        $validate->check($data);
        $this->repository->checkCode($data['key'], $data['code']);
        $adminInfo = $this->repository->login($data['account'], $data['password']);
        $tokenInfo = $this->repository->createToken($adminInfo);
        $admin = $adminInfo->toArray();
        unset($admin['pwd']);
        $data = [
            'token' => $tokenInfo['token'],
            'exp' => $tokenInfo['out'],
            'admin' => $admin
        ];

        return app('json')->success($data);
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-04-10
     */
    public function logout()
    {
        if ($this->request->isLogin())
            $this->repository->clearToken($this->request->token());
        return app('json')->success('退出登录');
    }

    /**
     * @return mixed
     * @author xaboy
     * @day 2020-04-09
     */
    public function getCaptcha()
    {
        $codeBuilder = new CaptchaBuilder(null, new PhraseBuilder(4));
        $key = $this->repository->createLoginKey($codeBuilder->getPhrase());
        $captcha = $codeBuilder->build()->inline();
        return app('json')->success(compact('key', 'captcha'));
    }

}
