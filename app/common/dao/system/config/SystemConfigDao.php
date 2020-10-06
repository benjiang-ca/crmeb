<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\config;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\config\SystemConfig;
use think\Collection;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class SystemConfigDao
 * @package app\common\dao\system\config
 * @author xaboy
 * @day 2020-03-27
 */
class SystemConfigDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return SystemConfig::class;
    }

    /**
     * @param int $classify_id
     * @return bool
     * @author xaboy
     * @day 2020-03-27
     */
    public function classifyIdExists(int $classify_id)
    {
        return $this->fieldExists('config_classify_id', $classify_id);
    }

    /**
     * @param $key
     * @param int|null $except
     * @return bool
     * @author xaboy
     * @day 2020-03-27
     */
    public function keyExists($key, ?int $except = null): bool
    {
        return $this->fieldExists('config_key', $key, $except);
    }

    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-03-31
     */
    public function search(array $where)
    {
        $query = SystemConfig::getDB();
        if ($where['keyword'])
            $query->where('config_name|config_key', '%' . $where['keyword'] . '%');
        if (isset($where['pid']) && $where['pid'] !== '') $query->where('config_classify_id', $where['pid']);
        if (isset($where['config_classify_id']))
            $query->where('config_classify_id', $where['config_classify_id']);
        return $query;
    }

    /**
     * @param int $cid
     * @param int $user_type
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-23
     */
    public function cidByConfig(int $cid, int $user_type)
    {
        return SystemConfig::getDB()->where('config_classify_id', $cid)->where('user_type', $user_type)->where('status', 1)->select();
    }

    /**
     * @param int $cid
     * @param $keys
     * @return array
     * @author xaboy
     * @day 2020-04-22
     */
    public function intersectionKey(int $cid, $keys): array
    {
        return SystemConfig::where('config_classify_id', $cid)->whereIn('config_key', $keys)->where('status', 1)->column('config_type,config_name', 'config_key');
    }
}
