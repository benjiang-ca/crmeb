<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-10
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\middleware;


use app\common\repositories\system\auth\MenuRepository;
use app\common\repositories\system\auth\RoleRepository;
use app\Request;
use think\exception\ValidateException;
use think\Response;

class MerchantAuthMiddleware extends BaseMiddleware
{

    public function before(Request $request)
    {
        $admin = $request->adminInfo();

        /** @var RoleRepository $role */
        $role = app()->make(RoleRepository::class);

        /** @var MenuRepository $menu */
        $menu = app()->make(MenuRepository::class);

        if ($admin->level) {
            $rules = $role->idsByRules($request->merId(), $admin->roles);
            $menus = $menu->idsByRoutes($rules);
        } else {
            $rules = [];
            $menus = [];
        }

        $request->macro('adminAuth', function () use (&$menus) {
            return $menus;
        });

        $request->macro('adminRule', function () use (&$rules) {
            return $rules;
        });

        $request->macro('checkAuth', function ($name, $vars) use (&$admin, &$menus, &$menu) {
            if (!$name || !$admin->level) return true;
            $isset = false;
            foreach ($menus as $_menu) {
                $keys = $menu->tidyParams($_menu['params']);
                if ($_menu['route'] != $name) continue;
                $isset = true;
                if (!count($keys)) return true;
                if ($menu->checkParams($keys, $vars))
                    return true;
            }
            if ($isset || $menu->routeExists($name))
                return false;
            return true;
        });

        $rule = $request->rule();
        if (!$request->checkAuth($rule->getName(), $rule->getVars()))
            throw new ValidateException('没有权限访问');
    }

    public function after(Response $response)
    {
        // TODO: Implement after() method.
    }
}