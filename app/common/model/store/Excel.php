<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-30
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\store;

use app\common\model\BaseModel;
use app\common\model\system\merchant\MerchantAdmin;

class Excel extends BaseModel
{

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 2020-07-30
     */
    public static function tablePk(): string
    {
        return 'excel_id';
    }

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 2020-07-30
     */
    public static function tableName(): string
    {
        return 'excel';
    }

    public function merAdmin()
    {
        return $this->hasOne(MerchantAdmin::class,'merchant_admin_id','admin_id');
    }
    public function sys_admin()
    {

    }
}
