<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\user;


use app\common\model\BaseModel;
use app\common\model\store\product\Product;

class UserVisit extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'user_visit_id';
    }

    public static function tableName(): string
    {
        return 'user_visit';
    }

    public function product()
    {
        return $this->hasOne(Product::class,'product_id','type_id');
    }
}