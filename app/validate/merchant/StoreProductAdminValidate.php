<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\validate\merchant;

use think\Validate;

class StoreProductAdminValidate extends Validate
{
    protected $failException = true;

    protected $rule = [
        "store_name|商品名称" => 'require',
        "content|商品详情" => 'require',
        "is_hot|是否热卖" => "in:0,1",
        "is_best|是否精品" => "in:0,1",
        "ficti|虚拟销量" => "number",
        "status|审核状态" => "in:0,1,-1",
        "refusal|拒绝理由" => "requireIf:status,-1"
    ];
}