<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-26
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\validate\admin;


use think\Validate;

/**
 * Class MerchantValidate
 * @package app\validate\admin
 * @author xaboy
 * @day 2020-04-17
 */
class MerchantValidate extends Validate
{
    /**
     * @var bool
     */
    protected $failException = true;

    /**
     * @var array
     */
    protected $rule = [
        'category_id|商户分类' => 'require',
        'mer_name|商户名称' => 'require|max:32',
        'mer_account|商户账号' => 'require|alphaNum|min:4|max:16',
        'mer_password|商户密码' => 'require|min:4|max:16',
        'real_name|商户姓名' => 'max:16',
        'mer_phone|商户手机号' => 'require',
        'sort|排序' => 'require',
        'mer_keyword|商户关键字' => 'max:64',
        'mer_address|商户地址' => 'max:64',
        'mark|备注' => 'max:64',
        'status|开启状态' => 'require|in:0,1',
        'is_audit|产品审核状态' => 'require|in:0,1',
        'is_best|推荐状态' => 'require|in:0,1',
        'is_bro_goods|直播商品状态' => 'require|in:0,1',
        'is_bro_room|直播间状态' => 'require|in:0,1',
        'is_trader|自营状态' => 'require|in:0,1',
        'commission_rate|提成比例' => 'egt:0'
    ];

    /**
     * @return $this
     * @author xaboy
     * @day 2020-04-17
     */
    public function isUpdate()
    {
        unset($this->rule['mer_account|商户账号'], $this->rule['mer_password|商户密码'], $this->rule['status|开启状态']);
        return $this;
    }
}