<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-16
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\user;

use app\common\model\BaseModel;

class UserExtract extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'extract_id';
    }

    public static function tableName(): string
    {
        return 'user_extract';
    }

    public function user()
    {
        return $this->hasOne(User::class,'uid','uid');
    }

}
