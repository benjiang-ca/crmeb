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
use app\common\model\user\UserVisit;
use think\facade\Db;
use think\Model;

/**
 * Class UserVisitDao
 * @package app\common\dao\user
 * @author xaboy
 * @day 2020/5/27
 */
class UserVisitDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/5/27
     */
    protected function getModel(): string
    {
        return UserVisit::class;
    }

    /**
     * @param int $uid
     * @param string $type
     * @param int $type_id
     * @param string|null $content
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020/5/27
     */
    public function addVisit(int $uid, string $type, int $type_id, ?string $content = '')
    {
        return $this->create(compact('uid', 'type', 'type_id', 'content'));
    }

    /**
     * @param int $uid
     * @param int $productId
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020/5/27
     */
    public function visitProduct(int $uid, int $productId)
    {
        return $this->addVisit($uid, 'product', $productId);
    }

    /**
     * @param int $uid
     * @param string $page
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020/5/27
     */
    public function visitPage(int $uid, string $page)
    {
        return $this->addVisit($uid, 'page', 0, $page);
    }

    /**
     * @param int $uid
     * @param string $page
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020/5/27
     */
    public function visitSmallProgram(int $uid, string $page)
    {
        return $this->addVisit($uid, 'smallProgram', 0, $page);
    }

    /**
     * @param int $uid
     * @param string $keyword
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020/5/27
     */
    public function searchProduct(int $uid, string $keyword)
    {
        return $this->addVisit($uid, 'searchProduct', 0, $keyword);
    }

    /**
     * @param int $uid
     * @param string $keyword
     * @return BaseDao|Model
     * @author xaboy
     * @day 2020/5/27
     */
    public function searchMerchant(int $uid, string $keyword)
    {
        return $this->addVisit($uid, 'searchMerchant', 0, $keyword);
    }

    /**
     * @param $uid
     * @return int
     * @author xaboy
     * @day 2020/6/19
     */
    public function userTotalVisit($uid)
    {
        return UserVisit::getDB()->where('uid', $uid)->where('type', 'product')->count();
    }


    public function search(?int $uid, string $type)
    {
        $query = ($this->getModel()::getDB())->when($uid, function ($query) use ($uid) {
            $query->where('uid', $uid);
        })->when($type, function ($query) use ($type) {
            $query->where('type', $type);
        });

        return $query->order('create_time DESC');
    }

    public function dateVisitUserNum($date, $merId = null)
    {
        return UserVisit::getDB()->alias('A')->join('StoreProduct B', 'A.type_id = B.product_id')->when($date, function ($query, $date) {
            getModelTime($query, $date, 'A.create_time');
        })->when($merId, function ($query, $merId) {
            $query->where('B.mer_id', $merId);
        })->where('A.type', 'product')->group('uid')->count();
    }

    public function dateVisitMerchantNum($date, $limit = 7)
    {
        return UserVisit::getDB()->alias('A')->join('StoreProduct B', 'A.type_id = B.product_id')
            ->join('Merchant C', 'C.mer_id = B.mer_id')
            ->field(Db::raw('count(A.type) as total,B.mer_id,C.mer_name'))
            ->when($date, function ($query, $date) {
                getModelTime($query, $date, 'A.create_time');
            })->where('A.type', 'product')->limit($limit)->group('B.mer_id')->order('total DESC')->select();
    }

    public function dateVisitProductNum($date, $merId, $limit = 7)
    {
        return UserVisit::getDB()->alias('A')->join('StoreProduct B', 'A.type_id = B.product_id')
            ->join('Merchant C', 'C.mer_id = B.mer_id')
            ->field(Db::raw('count(A.type_id) as total,B.image,B.store_name'))
            ->when($date, function ($query, $date) {
                getModelTime($query, $date, 'A.create_time');
            })->where('A.type', 'product')->where('B.mer_id', $merId)->group('A.type_id')->order('total DESC')
            ->limit($limit)->select();
    }

    public function dateVisitMerchantTotal($date)
    {
        return UserVisit::getDB()->when($date, function ($query, $date) {
            getModelTime($query, $date, 'create_time');
        })->whereIn('type', 'product')->count();
    }

    public function dateVisitNum($date)
    {
        return UserVisit::getDB()->when($date, function ($query, $date) {
            getModelTime($query, $date, 'create_time');
        })->whereIn('type', ['page', 'smallProgram'])->count();
    }

    public function dateVisitNumGroup($date)
    {
        return UserVisit::getDB()->when($date, function ($query, $date) {
            getModelTime($query, $date, 'create_time');
        })->field(Db::raw('from_unixtime(unix_timestamp(create_time),\'%m-%d\') as time, count(DISTINCT uid) as total'))
            ->group('time')
            ->order('time ASC')->select();
    }
}