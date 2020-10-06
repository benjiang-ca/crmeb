<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-08-03
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\store;

use app\common\model\BaseModel;

class StoreSeckillActive extends BaseModel
{

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 2020-07-30
     */
    public static function tablePk(): string
    {
        return 'seckill_active_id';
    }

    /**
     * TODO
     * @return string
     * @author Qinii
     * @day 2020-07-30
     */
    public static function tableName(): string
    {
        return 'store_seckill_active';
    }

}
