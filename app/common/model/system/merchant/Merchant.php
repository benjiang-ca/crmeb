<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-16
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\system\merchant;


use app\common\dao\store\product\ProductDao;
use app\common\model\BaseModel;
use app\common\model\store\coupon\StoreCouponProduct;
use app\common\model\store\coupon\StoreCouponUser;
use app\common\model\store\product\Product;

class Merchant extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'mer_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'merchant';
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'mer_id', 'mer_id');
    }

    public function showProduct()
    {
        return $this->hasMany(Product::class, 'mer_id', 'mer_id')
            ->where((new ProductDao())->productShow())
            ->field('mer_id,product_id,store_name,image,price,is_show,status,is_gift_bag');
    }

    public function recommend()
    {
        return $this->hasMany(Product::class, 'mer_id', 'mer_id')
            ->where((new ProductDao())->productShow())
            ->where('is_good',1)
            ->field('mer_id,product_id,store_name,image,price,is_show,status,is_gift_bag,is_good')
            ->order('is_good DESC')
            ->limit(3);
    }

    public function coupon()
    {
        $time = date('Y-m-d H:i:s');
        return $this->hasMany(StoreCouponUser::class, 'mer_id', 'mer_id')->where('start_time', '<', $time)->where('end_time', '>', $time)
            ->where('is_fail', 0)->where('status', 0)->order('coupon_price DESC')
            ->with(['product' => function ($query) {
                $query->field('coupon_id,product_id');
            }, 'coupon' => function ($query) {
                $query->field('coupon_id,type');
            }]);
    }

    public function merchantCategory()
    {
        return $this->hasOne(MerchantCategory::class, 'merchant_category_id', 'category_id');
    }

    public function getMerCommissionRateAttr()
    {
        return $this->commission_rate > 0 ? $this->commission_rate : bcmul($this->merchantCategory->commission_rate, 100, 2);
    }

}
