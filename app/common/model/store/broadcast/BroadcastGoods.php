<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\store\broadcast;


use app\common\model\BaseModel;
use app\common\model\store\product\Product;
use app\common\model\system\merchant\Merchant;

/**
 * Class BroadcastGoods
 * @package app\common\model\store\broadcast
 * @author xaboy
 * @day 2020/7/29
 */
class BroadcastGoods extends BaseModel
{

    /**
     * @return string|null
     * @author xaboy
     * @day 2020/7/29
     */
    public static function tablePk(): ?string
    {
        return 'broadcast_goods_id';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/7/29
     */
    public static function tableName(): string
    {
        return 'broadcast_goods';
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'mer_id', 'mer_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }
}