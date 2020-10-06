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
use app\common\model\user\UserGroup;
use think\db\BaseQuery;

/**
 * Class UserGroupDao
 * @package app\common\dao\user
 * @author xaboy
 * @day 2020-05-07
 */
class UserGroupDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return UserGroup::class;
    }


    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-05-06
     */
    public function search(array $where = [])
    {
        return UserGroup::getDB();
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020-05-07
     */
    public function allOptions()
    {
        return UserGroup::getDB()->column('group_name', 'group_id');
    }
}