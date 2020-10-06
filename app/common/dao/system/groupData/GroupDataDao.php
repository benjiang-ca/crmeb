<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\groupData;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\groupData\SystemGroupData;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class GroupDataDao
 * @package app\common\dao\system\groupData
 * @author xaboy
 * @day 2020-03-27
 */
class GroupDataDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return SystemGroupData::class;
    }

    /**
     * @param $merId
     * @param $groupId
     * @return BaseQuery
     * @author xaboy
     * @day 2020-03-30
     */
    public function getGroupDataWhere($merId, $groupId): BaseQuery
    {
        return SystemGroupData::getDB()->withAttr('value', function ($val) {
            return json_decode($val, true);
        })->where('mer_id', $merId)->where('group_id', $groupId)->order('sort DESC');
    }

    /**
     * @param $merId
     * @param $groupId
     * @param int|null $page
     * @param int|null $limit
     * @return array
     * @author xaboy
     * @day 2020/5/27
     */
    public function getGroupData($merId, $groupId, ?int $page = null, ?int $limit = 10)
    {
        $query = SystemGroupData::getDB()->where('mer_id', $merId)->where('group_id', $groupId)->where('status', 1)->order('sort DESC');
        if (!is_null($page)) $query->page($page, $limit);
        $groupData = [];
        foreach ($query->column('value') as $k => $v) {
            $groupData[] = json_decode($v, true);
        }
        return $groupData;
    }

    /**
     * @param $merId
     * @param $groupId
     * @param int|null $page
     * @param int|null $limit
     * @return array
     * @author xaboy
     * @day 2020/6/3
     */
    public function getGroupDataId($merId, $groupId, ?int $page = null, ?int $limit = 10)
    {
        $query = SystemGroupData::getDB()->where('mer_id', $merId)->where('group_id', $groupId)->where('status', 1)->order('sort DESC');
        if (!is_null($page)) $query->page($page, $limit);
        $groupData = [];
        foreach ($query->column('value', 'group_data_id') as $k => $v) {
            $groupData[] = ['id' => $k, 'data' => json_decode($v, true)];
        }
        return $groupData;
    }

    /**
     * @param $merId
     * @param $id
     * @param $data
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-03-30
     */
    public function merUpdate($merId, $id, $data)
    {
        $data['value'] = json_encode($data['value']);
        return SystemGroupData::getDB()->where('group_data_id', $id)->where('mer_id', $merId)->update($data);
    }

    /**
     * @param $merId
     * @param $id
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-03-30
     */
    public function merDelete($merId, $id)
    {
        return SystemGroupData::getDB()->where('mer_id', $merId)->where('group_data_id', $id)->delete();
    }

    /**
     * @param int $merId
     * @param int $id
     * @return bool
     * @author xaboy
     * @day 2020-04-02
     */
    public function merExists(int $merId, int $id)
    {
        return ($this->getModel())::getDB()->where('mer_id', $merId)->where($this->getPk(), $id)->count() > 0;
    }

    /**
     * @param int $groupId
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-05-16
     */
    public function clearGroup(int $groupId)
    {
        return SystemGroupData::getDB()->where('group_id', $groupId)->delete();
    }

    /**
     * @param $id
     * @param $merId
     * @return array|Model|null
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/2
     */
    public function merGet($id, $merId)
    {
        $data = SystemGroupData::getDB()->where('group_data_id', $id)->where('mer_id', $merId)->find();
        return $data ? $data['value'] : null;
    }

}