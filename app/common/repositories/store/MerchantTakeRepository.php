<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store;


use app\common\repositories\system\config\ConfigValueRepository;

class MerchantTakeRepository
{
    public function get($merId)
    {
        return merchantConfig($merId, [
            'mer_take_status', 'mer_take_name', 'mer_take_phone', 'mer_take_address', 'mer_take_location', 'mer_take_day', 'mer_take_time'
        ]);
    }

    public function set($merId, array $data)
    {
        $configValueRepository = app()->make(ConfigValueRepository::class);
        $configValueRepository->setFormData($data, $merId);
    }

    public function has($merId)
    {
        return merchantConfig($merId, 'mer_take_status') == '1';
    }
}