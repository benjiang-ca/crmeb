<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-18
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\model\system\merchant;

use app\common\model\BaseModel;

class MerchantIntention extends BaseModel
{
    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'mer_intention_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'merchant_intention';
    }

    public function setImagesAttr($value)
    {
        return implode(',',$value);
    }

    public function getImagesAttr($value)
    {
        return explode(',',$value);
    }

    public function getMerchantCategoryIdAttr($value)
    {
        return MerchantCategory::where('merchant_category_id',$value)->value('category_name');
    }
}
