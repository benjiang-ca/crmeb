<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/5
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\merchant;


use app\common\dao\system\merchant\FinancialRecordDao;
use app\common\repositories\BaseRepository;

/**
 * Class FinancialRecordRepository
 * @package app\common\repositories\system\merchant
 * @author xaboy
 * @day 2020/8/5
 * @mixin FinancialRecordDao
 */
class FinancialRecordRepository extends BaseRepository
{
    public function __construct(FinancialRecordDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }
}