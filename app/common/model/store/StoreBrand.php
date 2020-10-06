<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store;

use app\common\model\BaseModel;

class StoreBrand extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'brand_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_brand';
    }

    public function brandCategory()
    {
        return $this->belongsTo(StoreBrandCategory::class,'brand_category_id','store_brand_category_id');
    }

}
