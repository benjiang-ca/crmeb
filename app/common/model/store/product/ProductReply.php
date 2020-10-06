<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */

namespace app\common\model\store\product;

use app\common\model\BaseModel;
use app\common\model\store\order\StoreOrderProduct;
use app\common\repositories\store\product\ProductAttrValueRepository;

/**
 * Class ProductReply
 * @package app\common\model\store\product
 * @author xaboy
 * @day 2020/5/30
 */
class ProductReply extends BaseModel
{

    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @return string
     */
    public static function tablePk(): string
    {
        return 'reply_id';
    }


    /**
     * @Author:Qinii
     * @Date: 2020/5/8
     * @return string
     */
    public static function tableName(): string
    {
        return 'store_product_reply';
    }

    public function getPicsAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function setPicsAttr($value)
    {
        return $value ? implode(',', $value) : '';
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }

    public function orderProduct()
    {
        return $this->hasOne(StoreOrderProduct::class,'order_product_id','order_product_id');
    }

}
