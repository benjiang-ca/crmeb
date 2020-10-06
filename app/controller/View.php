<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/9/18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\controller;


use crmeb\basic\BaseController;

class View extends BaseController
{
    public function mobile()
    {
        $siteName = systemConfig('site_name');
        $url = $this->request->url(true);
        $url .= (strpos($url, '?') === false ? '?' : '&');
        $url .= 'inner_frame=1';
        return \think\facade\View::fetch('/mobile/view', compact('siteName','url'));
    }

    public function h5()
    {
        if ((!$this->request->isMobile()) && (!$this->request->param('inner_frame'))) return $this->mobile();
        $DB = DIRECTORY_SEPARATOR;
        return view(app()->getRootPath() . 'public' . $DB . 'index.html');
    }
}