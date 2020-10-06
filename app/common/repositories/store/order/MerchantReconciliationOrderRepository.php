<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-15
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\repositories\store\order;

use app\common\repositories\BaseRepository;
use app\common\dao\store\order\MerchantReconciliationOrderDao as dao;

class MerchantReconciliationOrderRepository extends BaseRepository
{
    public function __construct(dao $dao)
    {
        $this->dao = $dao;
    }

    public function getIds($where)
    {
        return $this->dao->search($where)->column('order_id');
    }
}
