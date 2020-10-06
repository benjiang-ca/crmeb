<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/5
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\merchant;


use app\common\model\BaseModel;
use app\common\model\user\User;
use app\common\repositories\system\merchant\MerchantRepository;

class FinancialRecord extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'financial_record_id';
    }

    public static function tableName(): string
    {
        return 'financial_record';
    }

    public function user()
    {
        return $this->hasOne(User::class,'uid','user_id');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class,'mer_id','mer_id');
    }
}
