<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\service;


use app\common\model\BaseModel;
use app\common\model\user\User;

class StoreService extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'service_id';
    }

    public static function tableName(): string
    {
        return 'store_service';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }
}