<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;

use app\common\repositories\system\admin\AdminRepository;
use app\Request;
use crmeb\exceptions\AuthException;
use crmeb\services\JwtTokenService;
use Firebase\JWT\ExpiredException;
use think\exception\ValidateException;
use think\facade\Route;
use think\Response;
use Throwable;

class AdminTokenMiddleware extends BaseMiddleware
{

    /**
     * @param Request $request
     * @throws Throwable
     * @author xaboy
     * @day 2020-04-10
     */
    public function before(Request $request)
    {
        $force = $this->getArg(0, true);
        try {
            $token = trim($request->header('X-Token'));
            if(!$token) $token = trim($request->param('token',''));
            if (strpos($token, 'Bearer') === 0)
                $token = trim(substr($token, 6));
            if (!$token)
                throw new ValidateException('请登录');

            /**
             * @var AdminRepository $repository
             */
            $repository = app()->make(AdminRepository::class);
            $service = new JwtTokenService();
            try {
                $payload = $service->parseToken($token);
            } catch (ExpiredException $e) {
                $repository->checkToken($token);
                $payload = $service->decode($token);
            } catch (Throwable $e) {//Token 过期
                throw new AuthException('token 已过期');
            }
            if ('admin' != $payload->jti[1])
                throw new AuthException('无效的 token');

            $admin = $repository->get($payload->jti[0]);
            if (!$admin)
                throw new AuthException('账号不存在');
            if (!$admin['status'])
                throw new AuthException('账号已被禁用');

        } catch (Throwable $e) {
            if ($force)
                throw  $e;
            $request->macro('isLogin', function () {
                return false;
            });
            $request->macros(['tokenInfo', 'adminId', 'adminInfo', 'token'], function () {
                throw new AuthException('请登录');
            });
            return;
        }
        $repository->updateToken($token);

        $request->macro('isLogin', function () {
            return true;
        });
        $request->macro('tokenInfo', function () use (&$payload) {
            return $payload;
        });
        $request->macro('token', function () use (&$token) {
            return $token;
        });
        $request->macro('adminId', function () use (&$admin) {
            return $admin->admin_id;
        });
        $request->macro('adminInfo', function () use (&$admin) {
            return $admin;
        });
    }

    public function after(Response $response)
    {
        // TODO: Implement after() method.
    }
}