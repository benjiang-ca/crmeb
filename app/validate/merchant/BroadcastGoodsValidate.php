<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/7/30
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\merchant;


use think\Validate;

class BroadcastGoodsValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        'name|商品名称' => 'require|min:3',
        'cover_img|商品图' => 'require',
        'price|价格' => 'require|min:0',
        'product_id|商品' => 'require|array|length:2',
    ];

    public function isBatch()
    {
        $this->rule['product_id|商品'] = 'require|integer';
        return $this;
    }
}