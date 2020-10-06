<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020/6/8
 *
 * Copyright (c) http://crmeb.net
 */
namespace app\common\dao\user;

use app\common\dao\BaseDao;
use app\common\model\user\FeedBackCategory as model;
use crmeb\traits\CategoresDao;

class FeedbackCateoryDao extends BaseDao
{

    use CategoresDao;

    /**
     * @return string
     * @author Qinii
     */
    protected function getModel(): string
    {
        return model::class;
    }

    /**
     * @return int
     * @author Qinii
     */
    public function getMaxLevel()
    {
        return 2;
    }

}
