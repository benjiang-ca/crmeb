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

class StoreBrandCategory extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'store_brand_category_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_brand_category';
    }

    public function getAncestorsAttr($value)
    {
        $value = self::whereIn('store_brand_category_id',$this->path_ids)->order('level ASC')->column('cate_name');
        return implode('/',$value).'/'.$this->cate_name;
    }

    public function getPathIdsAttr()
    {
        return explode('/',$this->path);
    }

}
