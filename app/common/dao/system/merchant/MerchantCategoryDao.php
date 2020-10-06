<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-06
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\merchant;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\merchant\MerchantCategory;
use think\db\BaseQuery;
use think\facade\Db;

/**
 * Class MerchantCategoryDao
 * @package app\common\dao\system\merchant
 * @author xaboy
 * @day 2020-05-06
 */
class MerchantCategoryDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return MerchantCategory::class;
    }


    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-05-06
     */
    public function search(array $where = [])
    {
        return MerchantCategory::getDB();
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020-05-06
     */
    public function allOptions()
    {
        return MerchantCategory::getDB()->column('category_name', 'merchant_category_id');
    }

    public function dateMerchantPriceGroup($date, $limit = 4)
    {
        return MerchantCategory::getDB()->alias('A')->leftJoin('Merchant B', 'A.merchant_category_id = B.category_id')
            ->leftJoin('StoreOrder C', 'C.mer_id = B.mer_id')->field(Db::raw('sum(C.pay_price) as pay_price,A.category_name'))
            ->when($date, function ($query, $date) {
                getModelTime($query, $date, 'C.pay_time');
            })->group('A.merchant_category_id')->where('pay_price', '>', 0)->order('pay_price DESC')->limit($limit)->select();
    }
}