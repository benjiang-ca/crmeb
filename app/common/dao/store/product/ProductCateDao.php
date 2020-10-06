<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\common\dao\store\product;

use app\common\dao\BaseDao;
use app\common\model\store\product\ProductCate as model;

class ProductCateDao extends BaseDao
{
    protected function getModel(): string
    {
        return model::class;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/9
     * @param int $productId
     * @return mixed
     */
    public function clearAttr(int $productId)
    {
        return ($this->getModel())::where('product_id',$productId)->delete();
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/13
     * @param array $data
     * @return mixed
     */
    public function insert(array $data)
    {
        return ($this->getModel()::getDB())->insertAll($data);
    }

}