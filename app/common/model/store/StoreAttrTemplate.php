<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store;


use app\common\model\BaseModel;

/**
 * Class StoreAttrTemplate
 * @package app\common\model\store
 * @author xaboy
 * @day 2020-05-06
 */
class StoreAttrTemplate extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'attr_template_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_attr_template';
    }

    /**
     * @param $val
     * @return mixed
     * @author xaboy
     * @day 2020-05-06
     */
    public function getTemplateValueAttr($val)
    {
        return json_decode($val, true);
    }

    /**
     * @param $val
     * @return false|string
     * @author xaboy
     * @day 2020-05-06
     */
    public function setTemplateValueAttr($val)
    {
        return json_encode($val);
    }
}