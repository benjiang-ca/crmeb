<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\service;


use app\common\dao\BaseDao;
use app\common\model\store\service\StoreService;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class StoreServiceDao
 * @package app\common\dao\store\service
 * @author xaboy
 * @day 2020/5/29
 */
class StoreServiceDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/5/29
     */
    protected function getModel(): string
    {
        return StoreService::class;
    }

    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020/5/29
     */
    public function search(array $where)
    {
        return StoreService::getDB()->where('is_del', 0)->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
            $query->where('status', $where['status']);
        })->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
            $query->whereLike('nickname', "%{$where['keyword']}%");
        })->when(isset($where['mer_id']) && $where['mer_id'] !== '', function ($query) use ($where) {
            $query->whereLike('mer_id', $where['mer_id']);
        })->order('sort DESC');
    }

    public function getService($uid, $merId = null)
    {
        return StoreService::getDB()->where('uid', $uid)->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->where('is_del', 0)->find();
    }

    /**
     * @param int $merId
     * @param int $id
     * @return bool
     * @author xaboy
     * @day 2020-05-13
     */
    public function merExists(int $merId, int $id)
    {
        return StoreService::getDB()->where($this->getPk(), $id)->where('mer_id', $merId)->where('is_del', 0)->count($this->getPk()) > 0;
    }

    /**
     * @param $merId
     * @param $uid
     * @param int|null $except
     * @return bool
     * @author xaboy
     * @day 2020/5/29
     */
    public function issetService($merId, $uid, ?int $except = null)
    {
        return StoreService::getDB()->where('uid', $uid)->when($except, function ($query, $except) {
                $query->where($this->getPk(), '<>', $except);
            })->where('mer_id', $merId)->where('is_del', 0)->count($this->getPk()) > 0;
    }

    /**
     * @param $uid
     * @param int|null $except
     * @return bool
     * @author xaboy
     * @day 2020/5/29
     */
    public function isBindService($uid, ?int $except = null)
    {
        return StoreService::getDB()->where('uid', $uid)->when($except, function ($query, $except) {
                $query->where($this->getPk(), '<>', $except);
            })->where('is_del', 0)->count($this->getPk()) > 0;
    }

    /**
     * @param int $id
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/5/29
     */
    public function delete(int $id)
    {
        return StoreService::getDB()->where($this->getPk(), $id)->update(['is_del' => 1]);
    }

    /**
     * @param $merId
     * @return array|Model|null
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function getChatService($merId)
    {
        return StoreService::getDB()->where('mer_id', $merId)->where('is_del', 0)->where('status', 1)->order('status DESC, sort DESC, create_time ASC')
            ->hidden(['is_del'])->find();
    }

    /**
     * @param $id
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/29
     */
    public function getValidServiceInfo($id)
    {
        return StoreService::getDB()->where('service_id', $id)->where('status', 1)->where('is_del', 0)->hidden(['is_del'])->find();
    }

    /**
     * @param $merId
     * @return array
     * @author xaboy
     * @day 2020/7/1
     */
    public function getNoticeServiceInfo($merId)
    {
        return StoreService::getDB()->where('mer_id', $merId)->where('status', 1)->where('notify', 1)
            ->where('is_del', 1)->where('phone', '<>', '')->column('phone,nickname');
    }


}
