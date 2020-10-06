<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\coupon;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\coupon\StoreCoupon;
use app\common\repositories\system\merchant\MerchantRepository;
use think\Collection;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * Class StoreCouponIssueDao
 * @package app\common\dao\store\coupon
 * @author xaboy
 * @day 2020-05-14
 */
class StoreCouponDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return StoreCoupon::class;
    }

    /**
     * @param int $merId
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-05-14
     */
    public function search(?int $merId, array $where)
    {
        if(isset($where['is_trader']) && $where['is_trader'] !== ''){
            $query = StoreCoupon::hasWhere('merchant',function($query)use($where){
                $query->where('is_trader',$where['is_trader']);
            });
        }else{
            $query = StoreCoupon::getDB()->alias('StoreCoupon');
        }
        $query->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
            $query->where('StoreCoupon.status', (int)$where['status']);
        })->when(isset($where['coupon_name']) && $where['coupon_name'] !== '', function ($query) use ($where) {
            $query->whereLike('title', "%{$where['coupon_name']}%");
        })->when(isset($where['send_type']) && $where['send_type'] !== '', function ($query) use ($where) {
            $query->whereLike('send_type', $where['send_type']);
        })->when($merId !== null, function ($query) use ($merId) {
            $query->where('StoreCoupon.mer_id', $merId);
        });
        return $query->where('StoreCoupon.is_del', 0)->order(($merId ? 'StoreCoupon.sort DESC,' : '') . 'coupon_id DESC');
    }

    /**
     * @param int|null $type
     * @param int $send_type
     * @return BaseQuery
     * @author xaboy
     * @day 2020/6/18
     */
    public function validCouponQuery(int $type = null, $send_type = 0)
    {
        $query = StoreCoupon::getDB()->where('status', 1)->where('send_type', $send_type)->where('is_del', 0)->order('sort DESC,coupon_id DESC')->when(!is_null($type), function ($query) use ($type) {
            $query->where('type', $type);
        });
        $query->where(function (BaseQuery $query) {
            $query->where('is_limited', 0)->whereOr(function (BaseQuery $query) {
                $query->where('is_limited', 1)->where('remain_count', '>', 0);
            });
        })->where(function (BaseQuery $query) {
            $query->where('is_timeout', 0)->whereOr(function (BaseQuery $query) {
                $time = date('Y-m-d H:i:s');
                $query->where('is_timeout', 1)->where('start_time', '<', $time)->where('end_time', '>', $time);
            });
        })->where(function (BaseQuery $query) {
            $query->where('coupon_type', 0)->whereOr(function (BaseQuery $query) {
                $query->where('coupon_type', 1)->where('use_end_time', '>', date('Y-m-d H:i:s'));
            });
        });
        return $query;
    }

    /**
     * @param $id
     * @param $uid
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/19
     */
    public function validCoupon($id, $uid)
    {
        return $this->validCouponQuery()->when($uid, function (BaseQuery $query, $uid) {
            $query->with(['issue' => function (BaseQuery $query) use ($uid) {
                $query->where('uid', $uid);
            }]);
        })->where('coupon_id', $id)->find();
    }

    /**
     * @param $merId
     * @param null $uid
     * @return Collection
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/1
     */
    public function validMerCoupon($merId, $uid = null)
    {
        return $this->validCouponQuery(0)->when($uid, function (BaseQuery $query, $uid) {
            $query->with(['issue' => function (BaseQuery $query) use ($uid) {
                $query->where('uid', $uid);
            }]);
        })->where('mer_id', $merId)->select();
    }

    /**
     * @param $merId
     * @param null $uid
     * @return int
     * @author xaboy
     * @day 2020/6/19
     */
    public function validMerCouponExists($merId, $uid = null)
    {
        return $this->validCouponQuery(0)->when($uid, function (BaseQuery $query, $uid) {
            $query->with(['issue' => function (BaseQuery $query) use ($uid) {
                $query->where('uid', $uid);
            }]);
        })->where('mer_id', $merId)->count();
    }

    /**
     * @param array $couponIds
     * @param null $uid
     * @return Collection
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/1
     */
    public function validProductCoupon(array $couponIds, $uid = null)
    {
        return $this->validCouponQuery(1)->when($uid, function (BaseQuery $query, $uid) {
            $query->with(['issue' => function (BaseQuery $query) use ($uid) {
                $query->where('uid', $uid);
            }]);
        })->whereIn('coupon_id', $couponIds)->select();
    }

    /**
     * @param array $couponIds
     * @param null $uid
     * @return int
     * @author Qinii
     */
    public function validProductCouponExists(array $couponIds, $uid = null)
    {
        return $this->validCouponQuery(1)->when($uid, function (BaseQuery $query, $uid) {
            $query->with(['issue' => function (BaseQuery $query) use ($uid) {
                $query->where('uid', $uid);
            }]);
        })->whereIn('coupon_id', $couponIds)->count();
    }

    /**
     * @param int $id
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-05-13
     */
    public function delete(int $id)
    {
        return StoreCoupon::getDB()->where($this->getPk(), $id)->update(['is_del' => 1]);
    }

    /**
     * @param int $id
     * @return bool
     * @author xaboy
     * @day 2020-05-13
     */
    public function exists(int $id)
    {
        return StoreCoupon::getDB()->where($this->getPk(), $id)->where('is_del', 0)->count($this->getPk()) > 0;
    }

    /**
     * @param int $merId
     * @param int $id
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-05-13
     */
    public function merDelete(int $merId, int $id)
    {
        return StoreCoupon::getDB()->where($this->getPk(), $id)->where('mer_id', $merId)->update(['is_del' => 1]);
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
        return StoreCoupon::getDB()->where($this->getPk(), $id)->where('mer_id', $merId)->where('is_del', 0)->count($this->getPk()) > 0;
    }

    /**
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/18
     */
    public function newPeopleCoupon()
    {
        return $this->validCouponQuery(null, 2)->select();
    }

    /**
     * @param array|null $ids
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/19
     */
    public function getGiveCoupon(array $ids = null)
    {
        return $this->validCouponQuery(null, 3)->when($ids, function ($query, $ids) {
            $query->whereIn('coupon_id', $ids);
        })->select();
    }
}
