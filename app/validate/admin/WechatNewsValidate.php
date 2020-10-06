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

class WechatNewsValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'data' => 'array|checkArray'
    ];



    protected function checkArray($value,$rule,$data = [])
    {
        foreach ($value as $v) {
            if(empty($v['title']))
                return '标题不能为空';
            if(empty($v['author']))
                return '作者不能为空';
            if(empty($v['synopsis']))
                return '摘要不能为空';
            if(empty($v['image_input']))
                return '图片不能为空';
            if(empty($v['content']))
                return '内容不能为空';
        }
        return true;
    }


}
