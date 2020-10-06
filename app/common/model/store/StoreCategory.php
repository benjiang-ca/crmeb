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
use think\Model;

class StoreCategory extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'store_category_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'store_category';
    }

    /**
     * 获取父级名称
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param $value
     * @return string
     */
    public function getAncestorsAttr($value)
    {
        $value = self::whereIn('store_category_id',$this->path_ids)->order('level ASC')->column('cate_name');
        return implode('/',$value).'/'.$this->cate_name;
    }

    /**
     * 获取path的id
     * @Author:Qinii
     * @Date: 2020/5/22
     * @return array
     */
    public function getPathIdsAttr()
    {
        return explode('/',$this->path);
    }

    /**
     * 获取子集id
     * @Author:Qinii
     * @Date: 2020/5/22
     * @param $value
     * @return array
     */
    public function getChildIdsAttr($value)
    {
        return self::where('path','like','%/'.$this->store_category_id.'/%')->column('store_category_id');
    }

}
