<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\user;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\user\Feedback;
use think\db\BaseQuery;

/**
 * Class FeedbackDao
 * @package app\common\dao\user
 * @author xaboy
 * @day 2020/5/28
 */
class FeedbackDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/5/28
     */
    protected function getModel(): string
    {
        return Feedback::class;
    }

    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020/5/28
     */
    public function search(array $where)
    {
        return Feedback::getDB()->when(isset($where['uid']) && $where['uid'] !== '', function ($query) use ($where) {
            $query->where('uid', $where['uid']);
        })->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
            $query->where('content','like', '%'.$where['keyword'].'%')->whereOr('reply','like', '%'.$where['keyword'].'%')->whereOr('remake','like', '%'.$where['keyword'].'%');
        })->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where) {
            $query->where('type',$where['type']);
        })->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
            $query->where('status', $where['status']);
        })->when(isset($where['realname']) && $where['realname'] !== '', function ($query) use ($where) {
            $query->where('realname','like', '%'.$where['realname'].'%');
        })->when(isset($where['is_del']) && $where['is_del'] !== '', function ($query) use ($where) {
            $query->where('is_del',$where['is_del']);
        })->order('create_time DESC');
    }

    /**
     * @param $id
     * @param $uid
     * @return bool
     * @author xaboy
     * @day 2020/5/28
     */
    public function uidExists($id, $uid): bool
    {
        return Feedback::getDB()->where($this->getPk(), $id)->where('uid', $uid)->where('is_del', 0)->count($this->getPk()) > 0;
    }
}
