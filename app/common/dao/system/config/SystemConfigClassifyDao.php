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
use app\common\model\system\config\SystemConfigClassify;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class SystemConfigClassifyDao
 * @package app\common\dao\system\config
 * @author xaboy
 * @day 2020-03-27
 */
class SystemConfigClassifyDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return SystemConfigClassify::class;
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020-03-27
     */
    public function getOptions()
    {
        return SystemConfigClassify::column('pid,classify_name', 'config_classify_id');
    }

    /**
     * @return array
     * @author xaboy
     * @day 2020-04-22
     */
    public function getTopOptions()
    {
        return SystemConfigClassify::getDB()->where('pid', 0)->column('classify_name', 'config_classify_id');
    }

    /**
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-31
     */
    public function all()
    {
        return SystemConfigClassify::getDB()->select();
    }

    /**
     * @return int
     * @author xaboy
     * @day 2020-03-31
     */
    public function count()
    {
        return SystemConfigClassify::getDB()->count();
    }

    /**
     * @param int $pid
     * @param string $field
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-27
     */
    public function children(int $pid, string $field = 'id,classify_name')
    {
        return SystemConfigClassify::getDB()->where('pid', $pid)->field($field)->select();
    }

    /**
     * @param int $id
     * @return bool
     * @author xaboy
     * @day 2020-03-27
     */
    public function existsChild(int $id): bool
    {
        return $this->fieldExists('pid', $id);
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
        return $this->fieldExists('classify_key', $key, $except);
    }

    /**
     * @param int $pid
     * @param int|null $except
     * @return bool
     * @author xaboy
     * @day 2020-03-27
     */
    public function pidExists(int $pid, ?int $except = null): bool
    {
        return $this->fieldExists('config_classify_id', $pid, $except);
    }

    /**
     * @param string $key
     * @return mixed
     * @author xaboy
     * @day 2020-04-22
     */
    public function keyById(string $key)
    {
        return SystemConfigClassify::getDB()->where('classify_key', $key)->value('config_classify_id');
    }

    /**
     * @param string $key
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-22
     */
    public function keyByData(string $key)
    {
        return SystemConfigClassify::getDB()->where('classify_key', $key)->find();
    }
}