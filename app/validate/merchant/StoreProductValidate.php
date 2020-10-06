<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\validate\merchant;

use think\File;
use think\Validate;

class StoreProductValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        "image|主图" => 'require|max:128',
        "slider_image|轮播图" => 'require',
        "store_name|商品名称" => 'require|max:128',
        "brand_id|品牌ID" => 'require',
        "cate_id|平台分类" => 'require',
        "mer_cate_id|商户分类" => 'array',
        "unit_name|单位名" => 'require|max:4',
        "temp_id|运费模板" => 'require',
        "spec_type" => "in:0,1",
        "is_show｜是否商家" => "in:0,1",
        "extension_type" => "in:0,1",
        "attr|商品规格" => "requireIf:spec_type,1|Array|checkUnique",
        "attrValue|商品属性" => "Array|productAttrValue"
    ];

    protected function productAttrValue($value,$rule,$data)
    {
        $arr = [];
        $extension_one_rate = systemConfig('extension_one_rate');
        $extension_two_rate = systemConfig('extension_two_rate');
        foreach ($value as $v){
            $sku = '';
            if(isset($v['detail']) && is_array($v['detail'])){
                sort($v['detail'],SORT_STRING);
                $sku = implode(',',$v['detail']);
            }
            if(in_array($sku,$arr)) return '商品SKU重复';
            $arr[] = $sku;
            if(isset($data['extension_type']) && $data['extension_type'] && systemConfig('extension_status')){
                if(!isset($v['extension_one']) || !isset($v['extension_two'])) return '佣金比例必须填写';
                if(($v['extension_one'] < 0) || ($v['extension_two'] < 0))
                    return '不可存在负数';
                if($v['price'] < bcadd($v['extension_one'],$v['extension_two'],2))
                    return '自定义佣金金额不能大于商品价格';
                if(isset($v['extension_one'])){
                    if((bccomp($v['extension_one'],bcmul($v['price'],$extension_one_rate,2),2)) == -1)
                        return '设置一级佣金不能低于系统比例';
                }
                if(isset($v['extension_two'])){
                    if((bccomp($v['extension_two'],bcmul($v['price'],$extension_two_rate,2),2)) == -1)
                        return '设置二级佣金不能低于系统比例';
                }

            }
        }
        return true;
    }

    public function checkUnique($value)
    {
        $arr = [];
       foreach ($value as $item){
           if(in_array($item['value'],$arr)) return '规格重复';
           $arr[] = $item['value'];
           if (count($item['detail']) != count(array_unique($item['detail']))) return '属性重复';
       }
       return true;
    }
}
