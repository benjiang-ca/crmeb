<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */

namespace app\common\model\store\product;

use app\common\model\BaseModel;

class ProductAttrValue extends BaseModel
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
        return 'store_product_attr_value';
    }

    public function getDetailAttr($value)
    {
        return json_decode($value);
    }

    public function getBcExtensionOneAttr()
    {
        if(!intval(systemConfig('extension_status')))  return 0;
        if($this->extension_one)  return $this->extension_one;
        return floatval(bcmul(systemConfig('extension_one_rate'), $this->price, 2));
    }

    public function getBcExtensionTwoAttr()
    {
        if(!intval(systemConfig('extension_status')))  return 0;
        if($this->extension_two)  return $this->extension_two;
        return floatval(bcmul(systemConfig('extension_two_rate'), $this->price, 2));
    }

}
