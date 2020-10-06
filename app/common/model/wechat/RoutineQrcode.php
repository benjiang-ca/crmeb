<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\wechat;


use app\common\model\BaseModel;

class RoutineQrcode extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'routine_qrcode_id';
    }

    public static function tableName(): string
    {
        return 'routine_qrcode';
    }
}