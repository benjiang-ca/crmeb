<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

class MenuValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'pid|选择父级分类' => 'require|integer|max:5',
        'icon|图标' => 'max:16',
        'menu_name|按钮名' => 'require|max:32',
        'route|菜单地址' => 'require|max:64',
        'sort|排序' => 'integer|max:3',
        'is_show|是否显示' => 'integer|in:0,1',
    ];

    public function isAuth()
    {
        unset($this->rule['icon|图标']);
        unset($this->rule['is_show|是否显示']);
        return $this;
    }
}