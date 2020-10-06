<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\common\model\store\product;

use app\common\model\BaseModel;
use app\common\model\store\StoreCategory;

class ProductCate extends BaseModel
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
        return 'store_product_cate';
    }

    public function category()
    {
        return $this->hasOne(StoreCategory::class,'store_category_id','mer_cate_id')->field('store_category_id,cate_name');
    }

}