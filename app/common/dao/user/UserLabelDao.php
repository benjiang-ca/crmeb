<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-07
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\user;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\user\UserLabel;
use think\db\BaseQuery;

/**
 * Class UserLabelDao
 * @package app\common\dao\user
 * @author xaboy
 * @day 2020-05-07
 */
class UserLabelDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return UserLabel::class;
    }


    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-05-06
     */
    public function search(array $where = [])
    {
        return UserLabel::getDB();
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020-05-07
     */
    public function allOptions()
    {
        return UserLabel::getDB()->column('label_name', 'label_id');
    }

    /**
     * @param array $ids
     * @return array
     * @author xaboy
     * @day 2020-05-08
     */
    public function labels(array $ids)
    {
        return UserLabel::getDB()->whereIn('label_id', $ids)->column('label_name');
    }
}