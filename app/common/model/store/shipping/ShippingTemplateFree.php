<?php
/**
  * @package crmeb_merchant
  * Author: Qinii
  * Date: 2020/5/6
  * Time: 14:19
  */
namespace app\common\model\store\shipping;

use app\common\model\BaseModel;

class ShippingTemplateFree extends BaseModel
{
    /**
     * Author:Qinii
     * Date: 2020/5/6
     * Time: 14:20
     * @return string
     */
    public static function tablePk(): string
    {
        return 'shipping_template_free_id';
    }


    /**
     * Author:Qinii
     * Date: 2020/5/6
     * Time: 14:20
     * @return string
     */
    public static function tableName(): string
    {
        return 'shipping_template_free';
    }


    public function getCityIDsAttr($value,$data)
    {
        $city_id = explode('/',$data['city_id']);
        $result = [];
        if(is_array($city_id)){
            foreach ($city_id as $v){
                $result[] = [City::where('city_id',$v)->value('parent_id') ?? 0,intval($v) ];
            }
        }
        return $result;
    }

    public function setCityIdAttr($value)
    {
        return implode('/',$value);
    }
}