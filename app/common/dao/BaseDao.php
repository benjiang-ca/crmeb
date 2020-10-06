<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao;


use app\common\model\BaseModel;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class BaseDao
 * @package app\common\dao
 * @author xaboy
 * @day 2020-03-30
 */
abstract class BaseDao
{
    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    abstract protected function getModel(): string;


    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public function getPk()
    {
        return ($this->getModel())::tablePk();
    }

    /**
     * @param int $id
     * @return bool
     * @author xaboy
     * @day 2020-03-27
     */
    public function exists(int $id)
    {
        return $this->fieldExists($this->getPk(), $id);
    }


    /**
     * @param $field
     * @param $value
     * @param int|null $except
     * @return bool
     * @author xaboy
     * @day 2020-03-30
     */
    public function fieldExists($field, $value, ?int $except = null): bool
    {
        $query = ($this->getModel())::getDB()->where($field, $value);
        if (!is_null($except)) $query->where($this->getPk(), '<>', $except);
        return $query->count() > 0;
    }

    /**
     * @param array $data
     * @return self|Model
     * @author xaboy
     * @day 2020-03-27
     */
    public function create(array $data)
    {
        return ($this->getModel())::create($data);
    }


    /**
     * @param int $id
     * @param array $data
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-03-27
     */
    public function update(int $id, array $data)
    {
        return ($this->getModel())::getDB()->where($this->getPk(), $id)->update($data);
    }

    /**
     * @param array $ids
     * @param array $data
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020/6/8
     */
    public function updates(array $ids, array $data)
    {
        return ($this->getModel())::getDB()->whereIn($this->getPk(), $ids)->update($data);
    }


    /**
     * @param int $id
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-03-27
     */
    public function delete(int $id)
    {
        return ($this->getModel())::getDB()->where($this->getPk(), $id)->delete();
    }


    /**
     * @param int $id
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-27
     */
    public function get($id)
    {
        return ($this->getModel())::getInstance()->find($id);
    }

    /**
     * @param array $where
     * @param string $field
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/1
     */
    public function getWhere(array $where, string $field = '*', array $with = [])
    {
        return ($this->getModel())::getInstance()->where($where)->when($with, function ($query) use ($with) {
            $query->with($with);
        })->field($field)->find();
    }

    /**
     * @param array $where
     * @param string $field
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/1
     */
    public function selectWhere(array $where, string $field = '*')
    {
        return ($this->getModel())::getInstance()->where($where)->field($field)->select();
    }

    /**
     * @param int $id
     * @param array $with
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-03-27
     */
    public function getWith(int $id, $with = [])
    {
        return ($this->getModel())::getInstance()->with($with)->find($id);
    }


    /**
     * @param array $data
     * @return int
     * @author xaboy
     * @day 2020/6/8
     */
    public function insertAll(array $data)
    {
        return ($this->getModel())::getDB()->insertAll($data);
    }

    /**
     * TODO 通过条件判断是否存在
     * @param array $where
     * @author Qinii
     * @day 2020-06-13
     */
    public function getWhereCount(array $where)
    {
        return ($this->getModel()::getDB())->where($where)->count();
    }

    public function existsWhere($where)
    {
        return ($this->getModel())::getDB()->where($where)->count() > 0;
    }

    /**
     * TODO 查询,如果不存在就创建
     * @Author:Qinii
     * @Date: 2020/9/8
     * @param array $where
     * @return array|Model|null
     */
    public function findOrCreate(array $where)
    {
       $res = ($this->getModel()::getDB())->where($where)->find();
       if(!$res)$res = $this->getModel()::create($where);
       return $res;
    }
}
