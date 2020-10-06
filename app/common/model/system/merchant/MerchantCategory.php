<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\merchant;


use app\common\model\BaseModel;

/**
 * Class MerchantCategory
 * @package app\common\model\system\merchant
 * @author xaboy
 * @day 2020-05-06
 */
class MerchantCategory extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'merchant_category_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'merchant_category';
    }
}