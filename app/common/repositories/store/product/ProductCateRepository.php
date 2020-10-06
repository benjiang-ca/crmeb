<?php
/**
 * @package crmeb_merchant
 * @Author: Qinii
 * @Date: 2020/5/8
 */
namespace app\common\repositories\store\product;

use app\common\repositories\BaseRepository;
use app\common\dao\store\product\ProductCateDao as dao;

class ProductCateRepository extends BaseRepository
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



}