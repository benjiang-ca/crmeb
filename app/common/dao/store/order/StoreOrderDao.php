<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\store\order;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\store\order\StoreOrder;
use app\common\model\store\order\StoreOrderStatus;
use app\common\model\user\User;
use app\common\repositories\system\merchant\MerchantRepository;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\Model;

/**
 * Class StoreOrderDao
 * @package app\common\dao\store\order
 * @author xaboy
 * @day 2020/6/8
 */
class StoreOrderDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/8
     */
    protected function getModel(): string
    {
        return StoreOrder::class;
    }

    /**
     * @param array $where
     * @param int $sysDel
     * @return BaseQuery
     * @author xaboyCRMEB
     * @day 2020/6/16
     */
    public function search(array $where, $sysDel = 0)
    {
        if (isset($where['is_trader']) && $where['is_trader'] !== '') {
            $query = StoreOrder::hasWhere('merchant', function ($query) use ($where) {
                $query->where('is_trader', $where['is_trader']);
            });
        } else {
            $query = StoreOrder::getDB()->alias('StoreOrder');
        }
        $query->when(($sysDel !== null), function ($query) use ($sysDel) {
            $query->where('is_system_del', $sysDel);
        })
            ->when(isset($where['order_type']) && $where['order_type'] >= 0 && $where['order_type'] !== '', function ($query) use ($where) {
                $query->where('order_type', $where['order_type']);
            })
            ->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
                if ($where['status'] == -2)
                    $query->where('paid', 1);
                else
                    $query->where('StoreOrder.status', $where['status']);
            })
            ->when(isset($where['uid']) && $where['uid'] !== '', function ($query) use ($where) {
                $query->where('uid', $where['uid']);
            })
            ->when(isset($where['order_id']) && $where['order_id'] !== '', function ($query) use ($where) {
                $query->where('order_id', $where['order_id']);
            })
            ->when(isset($where['take_order']) && $where['take_order'] != '', function ($query) use ($where) {
                $query->where('order_type', 1)->whereNotNull('verify_time');
            })
            ->when(isset($where['mer_id']) && $where['mer_id'] !== '', function ($query) use ($where) {
                $query->where('mer_id', $where['mer_id']);
            })
            ->when(isset($where['date']) && $where['date'] !== '', function ($query) use ($where) {
                getModelTime($query, $where['date'], 'StoreOrder.create_time');
            })
            ->when(isset($where['verify_date']) && $where['verify_date'] !== '', function ($query) use ($where) {
                getModelTime($query, $where['verify_date'], 'verify_time');
            })
            ->when(isset($where['order_sn']) && $where['order_sn'] !== '', function ($query) use ($where) {
                $query->where('order_sn', 'like', '%' . $where['order_sn'] . '%');
            })
            ->when(isset($where['paid']) && $where['paid'] !== '', function ($query) use ($where) {
                $query->where('paid', $where['paid']);
            })
            ->when(isset($where['is_del']) && $where['is_del'] !== '', function ($query) use ($where) {
                $query->where('StoreOrder.is_del', $where['is_del']);
            })
            ->when(isset($where['service_id']) && $where['service_id'] !== '', function ($query) use ($where) {
                $query->where('service_id', $where['service_id']);
            })
            ->when(isset($where['username']) && $where['username'] !== '', function ($query) use ($where) {
                $uid = User::where('nickname', 'like', "%{$where['username']}%")
                    ->whereOr('phone', 'like', "%{$where['username']}%")
                    ->column('uid');
                $query->where('uid', 'in', $uid);
            })
            ->when(isset($where['keywords']) && $where['keywords'] !== '', function ($query) use ($where) {
                $query->where(function ($query) use ($where) {
                    $query->whereLike('real_name|user_phone|order_sn', "%" . $where['keywords'] . "%");
                });
            })
            ->when(isset($where['reconciliation_type']) && $where['reconciliation_type'] !== '', function ($query) use ($where) {
                $query->when($where['reconciliation_type'], function ($query) use ($where) {
                    $query->where('reconciliation_id', '<>', 0);
                }, function ($query) use ($where) {
                    $query->where('reconciliation_id', 0);
                });
            })->order('StoreOrder.create_time DESC');

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
     * @day 2020/6/11
     */
    public function userOrder($id, $uid)
    {
        return StoreOrder::getDB()->where('order_id', $id)->where('uid', $uid)->where('is_del', 0)->where('paid', 1)->where('is_system_del', 0)->find();
    }

    /**
     * @param array $where
     * @param $ids
     * @return BaseQuery
     * @author xaboy
     * @day 2020/6/26
     */
    public function usersOrderQuery(array $where, $ids)
    {
        return StoreOrder::getDB()->whereIn('uid', $ids)->when(isset($where['date']) && $where['date'] !== '', function ($query) use ($where) {
            getModelTime($query, $where['date'], 'pay_time');
        })->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
            $query->where('order_id|order_sn', $where['keyword']);
        })->where('paid', 1)->order('pay_time DESC');
    }

    /**
     * @param $field
     * @param $value
     * @param int|null $except
     * @return bool
     * @author xaboy
     * @day 2020/6/11
     */
    public function fieldExists($field, $value, ?int $except = null): bool
    {
        return ($this->getModel()::getDB())->when($except, function ($query) use ($field, $except) {
                $query->where($field, '<>', $except);
            })->where($field, $value)->count() > 0;
    }

    /**
     * @param $id
     * @return mixed
     * @author xaboy
     * @day 2020/6/12
     */
    public function getMerId($id)
    {
        return StoreOrder::getDB()->where('order_id', $id)->value('mer_id');
    }

    /**
     * @param array $where
     * @return bool
     * @author Qinii
     * @day 2020-06-12
     */
    public function merFieldExists(array $where)
    {
        return ($this->getModel()::getDB())->where($where)->count() > 0;
    }

    /**
     * TODO
     * @param $reconciliation_id
     * @return mixed
     * @author Qinii
     * @day 2020-06-15
     */
    public function reconciliationUpdate($reconciliation_id)
    {
        return ($this->getModel()::getDB())->whereIn('reconciliation_id', $reconciliation_id)->update(['reconciliation_id' => 0]);
    }

    public function dayOrderNum($day, $merId = null)
    {
        return StoreOrder::getDB()->where('paid', 1)->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($day, function ($query, $day) {
            getModelTime($query, $day, 'pay_time');
        })->count();
    }

    public function dayOrderPrice($day, $merId = null)
    {
        return getModelTime(StoreOrder::getDB()->where('paid', 1)->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        }), $day, 'pay_time')->sum('pay_price');
    }

    public function dateOrderPrice($date, $merId = null)
    {
        return StoreOrder::getDB()->where('paid', 1)->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($date, function ($query, $date) {
            getModelTime($query, $date, 'pay_time');
        })->sum('pay_price');
    }

    public function dateOrderNum($date, $merId = null)
    {
        return StoreOrder::getDB()->where('paid', 1)->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($date, function ($query, $date) {
            getModelTime($query, $date, 'pay_time');
        })->count();
    }

    public function dayOrderUserNum($day, $merId = null)
    {
        return StoreOrder::getDB()->where('paid', 1)->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($day, function ($query, $day) {
            getModelTime($query, $day, 'pay_time');
        })->group('uid')->count();
    }

    public function orderUserNum($date, $paid = null, $merId = null)
    {
        return StoreOrder::getDB()->when($paid, function ($query, $paid) {
            $query->where('paid', $paid);
        })->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($date, function ($query, $date) use ($paid) {
            if (!$paid) {
                $query->where(function ($query) use ($date) {
                    $query->where(function ($query) use ($date) {
                        $query->where('paid', 1);
                        getModelTime($query, $date, 'pay_time');
                    })->whereOr(function ($query) use ($date) {
                        $query->where('paid', 0);
                        getModelTime($query, $date);
                    });
                });
            } else
                getModelTime($query, $date, 'pay_time');
        })->group('uid')->count();
    }

    public function orderUserGroup($date, $paid = null, $merId = null)
    {
        return StoreOrder::getDB()->when($paid, function ($query, $paid) {
            $query->where('paid', $paid);
        })->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($date, function ($query, $date) {
            getModelTime($query, $date, 'pay_time');
        })->group('uid')->field(Db::raw('uid,sum(pay_price) as pay_price,count(order_id) as total'))->select();
    }

    public function oldUserNum(array $ids, $merId = null)
    {
        return StoreOrder::getDB()->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->whereIn('uid', $ids)->where('paid', 1)->group('uid')->count();
    }

    public function oldUserIds(array $ids, $merId = null)
    {
        return StoreOrder::getDB()->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->whereIn('uid', $ids)->where('paid', 1)->group('uid')->column('uid');
    }

    public function orderPrice($date, $paid = null, $merId = null)
    {
        return StoreOrder::getDB()->when($paid, function ($query, $paid) {
            $query->where('paid', $paid);
        })->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->when($date, function ($query, $date) use ($paid) {
            if (!$paid) {
                $query->where(function ($query) use ($date) {
                    $query->where(function ($query) use ($date) {
                        $query->where('paid', 1);
                        getModelTime($query, $date, 'pay_time');
                    })->whereOr(function ($query) use ($date) {
                        $query->where('paid', 0);
                        getModelTime($query, $date);
                    });
                });
            } else
                getModelTime($query, $date, 'pay_time');
        })->sum('pay_price');
    }

    public function orderGroupNum($date, $merId = null)
    {
        return StoreOrder::getDB()->field(Db::raw('sum(pay_price) as pay_price,count(*) as total,count(distinct uid) as user,from_unixtime(unix_timestamp(pay_time),\'%m-%d\') as `day`'))
            ->where('paid', 1)->when($date, function ($query, $date) {
                getModelTime($query, $date, 'pay_time');
            })->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->order('day ASC')->group('day')->select();
    }

    public function orderGroupNumPage($where, $page, $limit, $merId = null)
    {
        return StoreOrder::getDB()->when(isset($where['dateRange']), function ($query) use ($where) {
            getModelTime($query, date('Y/m/d 00:00:00', $where['dateRange']['start']) . '-' . date('Y/m/d 00:00:00', $where['dateRange']['stop']), 'pay_time');
        })->field(Db::raw('sum(pay_price) as pay_price,count(*) as total,count(distinct uid) as user,from_unixtime(unix_timestamp(pay_time),\'%m-%d\') as `day`'))
            ->where('paid', 1)->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->order('day DESC')->page($page, $limit)->group('day')->select();
    }

    public function dayOrderPriceGroup($date, $merId = null)
    {
        return StoreOrder::getDB()->field(Db::raw('sum(pay_price) as price, from_unixtime(unix_timestamp(pay_time),\'%H:%i\') as time'))
            ->where('paid', 1)->when($date, function ($query, $date) {
                getModelTime($query, $date, 'pay_time');
            })->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->group('time')->select();
    }

    public function dayOrderNumGroup($date, $merId = null)
    {
        return StoreOrder::getDB()->field(Db::raw('count(*) as total, from_unixtime(unix_timestamp(pay_time),\'%H:%i\') as time'))
            ->where('paid', 1)->when($date, function ($query, $date) {
                getModelTime($query, $date, 'pay_time');
            })->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->group('time')->select();
    }

    public function dayOrderUserGroup($date, $merId = null)
    {
        return StoreOrder::getDB()->field(Db::raw('count(DISTINCT uid) as total, from_unixtime(unix_timestamp(pay_time),\'%H:%i\') as time'))
            ->where('paid', 1)->when($date, function ($query, $date) {
                getModelTime($query, $date, 'pay_time');
            })->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })->group('time')->select();
    }

    /**
     * 获取当前时间到指定时间的支付金额 管理员
     * @param string $start 开始时间
     * @param string $stop 结束时间
     * @return mixed
     */
    public function chartTimePrice($start, $stop, $merId = null)
    {
        return StoreOrder::getDB()->where('paid', 1)
            ->where('pay_time', '>=', $start)
            ->where('pay_time', '<', $stop)
            ->when($merId, function ($query, $merId) {
                $query->where('mer_id', $merId);
            })
            ->field('sum(pay_price) as num,FROM_UNIXTIME(unix_timestamp(pay_time), \'%Y-%m-%d\') as time')
            ->group('time')
            ->order('pay_time ASC')->select()->toArray();
    }

    /**
     * @param $date
     * @param null $merId
     * @return mixed
     */
    public function chartTimeNum($date, $merId = null)
    {
        return StoreOrder::getDB()->where('paid', 1)->when($date, function ($query) use ($date) {
            getModelTime($query, $date, 'pay_time');
        })->when($merId, function ($query, $merId) {
            $query->where('mer_id', $merId);
        })->field('count(order_id) as num,FROM_UNIXTIME(unix_timestamp(pay_time), \'%Y-%m-%d\') as time')
            ->group('time')
            ->order('pay_time ASC')->select()->toArray();
    }

    /**
     * TODO 根据商品ID获取订单
     * @param int $productId
     * @param array $where
     * @return BaseQuery
     * @author Qinii
     * @day 2020-08-05
     */
    public function getOrderByProductId(int $productId, array $where)
    {
        $query = StoreOrder::hasWhere('orderProduct', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })
            ->when(isset($where['paid']) && $where['paid'] !== '', function ($query) use ($where) {
                $query->where('paid', $where['paid']);
            })
            ->when(isset($where['day']) && $where['day'] !== '', function ($query) use ($where) {
                $query->whereDay('StoreOrder.create_time', $where['day']);
            })
            ->when(isset($where['uid']) && $where['uid'] !== '', function ($query) use ($where) {
                $query->where('StoreOrder.uid', $where['uid']);
            });
        return $query;
    }

    /**
     * TODO 根据sku获取订单
     * @Author:Qinii
     * @Date: 2020/8/26
     * @param int $sku
     * @param array $where
     * @return BaseQuery
     */
    public function getOrderByProductSku(string $sku, array $where)
    {
        $query = StoreOrder::hasWhere('orderProduct', function ($query) use ($sku) {
            $query->where('product_sku', $sku);
        })
            ->when(isset($where['paid']) && $where['paid'] !== '', function ($query) use ($where) {
                $query->where('paid', $where['paid']);
            })
            ->when(isset($where['day']) && $where['day'] !== '', function ($query) use ($where) {
                $query->whereDay('StoreOrder.create_time', $where['day']);
            })
            ->when(isset($where['uid']) && $where['uid'] !== '', function ($query) use ($where) {
                $query->where('StoreOrder.uid', $where['uid']);
            });
        return $query;
    }

    /**
     * @param $end
     * @return mixed
     * @author xaboy
     * @day 2020/9/16
     */
    public function getFinishTimeoutIds($end)
    {
        return StoreOrderStatus::getDB()->alias('A')->leftJoin('StoreOrder B', 'A.order_id = B.order_id')
            ->where('A.change_type', 'take')
            ->where('A.change_time', '<', $end)->where('B.paid', 1)->where('B.status', 2)
            ->column('A.order_id');
    }
}
