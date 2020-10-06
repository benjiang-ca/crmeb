<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/30
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\order;


use app\common\model\BaseModel;
use app\common\model\store\product\Product;
use app\common\model\store\product\ProductAttrValue;
use app\common\model\system\merchant\Merchant;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\product\ProductAttrValueRepository;
use app\common\repositories\store\StoreSeckillActiveRepository;

class StoreCart extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'cart_id';
    }

    public static function tableName(): string
    {
        return 'store_cart';
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }

    public function cartProduct()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id')->field('product_id,image,store_name,is_show,status,is_del,unit_name,price,mer_status,temp_id');
    }

    public function productAttr()
    {
        return $this->hasOne(ProductAttrValue::class, 'unique', 'product_attr_unique');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'mer_id', 'mer_id');
    }


    public function getCheckProductSkuAttr()
    {
        if ($this->is_fail) return false;
        if (is_null($this->product) || is_null($this->productAttr) || !$this->product->check()) {
            $this->is_fail = 1;
            $this->save();
            return false;
        }
        if ($this->productAttr->stock < $this->cart_num) return false;
        return true;
    }

    /**
     * TODO
     * @return bool
     * @author Qinii
     * @day 2020-08-15
     */
    public function getCheckSeckillProductSkuAttr()
    {
        if ($this->product['product_type'] !== 1) return false;
        if ($this->is_fail) return false;
        if (is_null($this->product) || is_null($this->productAttr) || !$this->product->check()) {
            $this->is_fail = 1;
            $this->save();
            return false;
        }
        //结束时间
        if ($this->product->end_time < time()) return false;
        //限量
        $order_make = app()->make(StoreOrderRepository::class);
        $count = $order_make->seckillOrderCounut($this->product_id);
        if ($this->productAttr->stock <= $count) return false;

        //原商品sku库存
        $value_make = app()->make(ProductAttrValueRepository::class);
        $sku = $value_make->getWhere(['sku' => $this->productAttr->sku, 'product_id' => $this->product->old_product_id]);
        if (!$sku || $sku['stock'] <= 0) return false;

        return true;
    }


    /**
     * TODO 秒杀商品购买限制
     * @return bool
     * @author Qinii
     * @day 2020-08-17
     */
    public function getUserSecikllPayCountAttr()
    {
        if($this->product['product_type'] !== 1) return false;
        $make = app()->make(StoreOrderRepository::class);
        if(!$make->getDayPayCount($this->uid,$this->product_id) || !$make->getPayCount($this->uid,$this->product_id))
            return false;
        return true;
    }
}
