<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/30
 */
namespace app\common\model\user;


use app\common\model\BaseModel;
use app\common\model\store\product\Product;
use app\common\model\system\merchant\Merchant;

class UserRelation extends BaseModel
{

    public static function tablePk(): ?string
    {
        return 'uid';
    }

    public static function tableName(): string
    {
        return 'user_relation';
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class,'mer_id','type_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class,'product_id','type_id');
    }

}