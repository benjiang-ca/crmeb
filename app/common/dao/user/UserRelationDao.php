<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\user;


use app\common\dao\BaseDao;
use app\common\model\user\UserRelation;
use app\common\model\user\UserRelation as model;

/**
 * Class UserVisitDao
 * @package app\common\dao\user
 * @author xaboy
 * @day 2020/5/27
 */
class UserRelationDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/5/27
     */
    protected function getModel(): string
    {
        return model::class;
    }

    /**
     * @param $field
     * @param $value
     * @param null $type
     * @param null $uid
     * @return mixed
     * @author Qinii
     */
    public function apiFieldExists($field,$value,$type = null,$uid = null)
    {
        return $this->getModel()::getDB()->when($uid,function($query)use($uid){
            $query->where('uid',$uid);
        })->when($type,function($query)use($type){
            $query->where('type',$type);
        })->where($field,$value);
    }

    /**
     * @param $where
     * @return mixed
     * @author Qinii
     */
    public function search($where)
    {
        $query = ($this->getModel()::getDB())->when((isset($where['type']) && $where['type']) ,function($query)use($where){
            $query->where('type',$where['type']);
        })->when((isset($where['uid']) && $where['uid']) ,function($query)use($where){
            $query->where('uid',$where['uid']);
        });

        return $query->order('create_time DESC');
    }


    /**
     * @param array $where
     * @author Qinii
     */
    public function destory(array $where)
    {
        ($this->getModel()::getDB())->where($where)->delete();
    }

    public function dayLikeStore($day, $merId = null)
    {
        return getModelTime(UserRelation::getDB()->where('type', 10)->when($merId, function ($query, $merId) {
            $query->where('type_id', $merId);
        }), $day)->count();
    }

    public function dateVisitStore($date, $merId = null)
    {
        return UserRelation::getDB()->where('type', 11)->when($merId, function ($query, $merId) {
            $query->where('type_id', $merId);
        })->when($date, function ($query, $date) {
            getModelTime($query, $date, 'create_time');
        })->count();
    }
}
