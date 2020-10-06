<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/17
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;


use think\facade\Cache;

class ExpressService
{
    const API = 'https://wuliu.market.alicloudapi.com/kdi';

    public static function query($no, $type = '')
    {
        $appCode = systemConfig('express_app_code');
        if (!$appCode) return false;
        $res = HttpService::getRequest(self::API, compact('no', 'type'), ['Authorization:APPCODE ' . $appCode]);
        return json_decode($res, true) ?: null;
    }

    public static function express($delivery_id)
    {
        if (Cache::has('express_' . $delivery_id)) {
            $result = Cache::get('express_' . $delivery_id);
        } else {
            $result = self::query($delivery_id);
            if (is_array($result) &&
                isset($result['result']) &&
                isset($result['result']['deliverystatus']) &&
                $result['result']['deliverystatus'] >= 3)
                $cacheTime = 0;
            else
                $cacheTime = 1800;
            $result = $result['result']['list'] ?? [];
            Cache::set('express_' . $delivery_id, $result, $cacheTime);
        }
        return $result;
    }
}