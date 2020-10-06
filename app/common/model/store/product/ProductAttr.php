<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\common\model\store\product;

use app\common\model\BaseModel;

class ProductAttr extends BaseModel
{

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @return string
     */
    public static function tablePk(): string
    {
        return '';
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @return string
     */
    public static function tableName(): string
    {
        return 'store_product_attr';
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param $value
     * @return array
     */
    public function getAttrValuesAttr($value)
    {
        return explode('-!-',$value);
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param $value
     * @return string
     */
    public function setAttrValuesAttr($value)
    {
        return implode('-!-',$value);
    }

}