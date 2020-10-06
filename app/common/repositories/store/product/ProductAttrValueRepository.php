<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\common\repositories\store\product;

use app\common\repositories\BaseRepository;
use app\common\dao\store\product\ProductAttrValueDao as dao;

/**
 * Class ProductAttrValueRepository
 * @package app\common\repositories\store\product
 * @mixin dao
 */
class ProductAttrValueRepository extends BaseRepository
{

    protected $dao;

    /**
     * ProductRepository constructor.
     * @param dao $dao
     */
    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param int $id
     * @return mixed
     */
    public function priceCount(int $id)
    {
       return  min($this->dao->getFieldColumnt('product_id',$id,'price'));
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param int $id
     * @return mixed
     */
    public function stockCount(int $id)
    {
        return  $this->dao->getFieldSum('product_id',$id,'stock');
    }

    /**
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param int|null $merId
     * @param string $value
     * @return bool
     */
    public function merUniqueExists(?int $merId,string $value)
    {
        return $this->dao->merFieldExists($merId,'unique',$value);
    }

    /**
     * TODO
     * @param $unique
     * @return mixed
     * @author Qinii
     * @day 2020-08-05
     */
    public function getOptionByUnique($unique)
    {
        return  $this->dao->getFieldExists(null,'unique',$unique)->find();
    }
}
