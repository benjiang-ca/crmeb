<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/29
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\service;


use app\common\dao\store\service\StoreServiceLogDao;
use app\common\model\store\service\StoreServiceLog;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\order\StoreRefundOrderRepository;
use app\common\repositories\store\product\ProductRepository;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Db;
use think\model\Relation;

/**
 * Class StoreServiceLogRepository
 * @package app\common\repositories\store\service
 * @author xaboy
 * @day 2020/5/29
 * @mixin StoreServiceLogDao
 */
class StoreServiceLogRepository extends BaseRepository
{
    /**
     * StoreServiceLogRepository constructor.
     * @param StoreServiceLogDao $dao
     */
    public function __construct(StoreServiceLogDao $dao)
    {
        $this->dao = $dao;
    }

    public function tidyLogList($list, $sendType)
    {
        foreach ($list as $k => $log) {
            $list[$k]['last'] = $this->dao->getLastLog($log['mer_id'], $log['uid']);
            $list[$k]['num'] = ($list[$k]['last'] && $list[$k]['last']['send_type'] == $sendType && $list[$k]['last']['type'] == 0) ? $this->getUnReadNum($log['mer_id'], $log['uid'], $sendType) : 0;
        }
        return $list;
    }

    /**
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/16
     */
    public function userMerchantList($uid, $page, $limit)
    {
        $query = $this->dao->getMerchantListQuery($uid);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('uid,mer_id,create_time,type')->with(['merchant' => function ($query) {
            $query->field('mer_id,mer_avatar,mer_name');
        }])->page($page, $limit)->select()->toArray();
        $list = $this->tidyLogList($list, 1);
        return compact('count', 'list');
    }

    /**
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/16
     */
    public function serviceUserList($uid, $page, $limit)
    {
        $service = app()->make(StoreServiceRepository::class)->getService($uid);
        if (!$service || !$service['status'])
            throw new ValidateException('没有权限');
        $query = $this->dao->getUserListQuery($service->service_id);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('uid,mer_id,create_time,type')->with(['user'])->page($page, $limit)->select()->toArray();
        $list = $this->tidyLogList($list, 0);
        return compact('count', 'list');
    }

    /**
     * @param $merId
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/15
     */
    public function userList($merId, $uid, $page, $limit)
    {
        $query = $this->search(['mer_id' => $merId, 'uid' => $uid])->order('service_log_id DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->with(['user', 'service'])->select();
        if ($page == 1) $this->dao->userRead($merId, $uid);
        $list = array_reverse($this->getSendDataList($list)->toArray());
        return compact('count', 'list');
    }

    /**
     * @param $merId
     * @param $toUid
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author xaboy
     * @day 2020/6/15
     */
    public function merList($merId, $toUid, $uid, $page, $limit)
    {
        $service = app()->make(StoreServiceRepository::class)->getService($uid, $merId);
        if (!$service || !$service['status'])
            throw new ValidateException('没有权限');
        $query = $this->search(['mer_id' => $merId, 'uid' => $toUid])->order('service_log_id DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->with(['user', 'service'])->select();
        if ($page == 1) $this->dao->serviceRead($merId, $service->service_id);
        $list = array_reverse($this->getSendDataList($list)->toArray());
        return compact('count', 'list');
    }

    /**
     * @param $merId
     * @param $uid
     * @param $type
     * @param $msn
     * @author xaboy
     * @day 2020/6/13
     */
    public function checkMsn($merId, $uid, $type, $msn)
    {
        if ($type == 4 && !app()->make(ProductRepository::class)->merExists($merId, $msn))
            throw new ValidateException('商品不存在');
        else if ($type == 5 && !app()->make(StoreOrderRepository::class)->existsWhere(['uid' => $uid, 'mer_id' => $merId, 'order_id' => $msn]))
            throw new ValidateException('订单不存在');
        else if ($type == 6 && !app()->make(StoreRefundOrderRepository::class)->existsWhere(['uid' => $uid, 'mer_id' => $merId, 'refund_order_id' => $msn]))
            throw new ValidateException('退款单不存在');
    }

    /**
     * @param StoreServiceLog $log
     * @return StoreServiceLog
     * @author xaboy
     * @day 2020/6/15
     */
    public function getSendData(StoreServiceLog $log)
    {
        if ($log->msn_type == 4)
            $log->product;
        else if ($log->msn_type == 5)
            $log->orderInfo;
        else if ($log->msn_type == 6)
            $log->refundOrder;
        return $log;
    }

    public function getSendDataList($list)
    {
        $cache = [];
        foreach ($list as $log) {
            if (!in_array($log->msn_type, [4, 5, 6])) continue;
            $key = $log->msn_type . $log->msn;
            if (isset($cache[$key])) {
                if ($log->msn_type == 4)
                    $log->set('product', $cache[$key]);
                else if ($log->msn_type == 5)
                    $log->set('orderInfo', $cache[$key]);
                else
                    $log->set('refundOrder', $cache[$key]);
            } else {
                if ($log->msn_type == 4)
                    $cache[$key] = $log->product;
                else if ($log->msn_type == 5)
                    $cache[$key] = $log->orderInfo;
                else
                    $cache[$key] = $log->refundOrder;
            }
        }
        return $list;
    }

    /**
     * @param $uid
     * @param $merId
     * @author xaboy
     * @day 2020/6/15
     */
    public function userToChat($uid, $merId)
    {
        Cache::set('u_chat' . $uid, $merId, 3600);
    }

    /**
     * @param $uid
     * @param $toUid
     * @author xaboy
     * @day 2020/6/15
     */
    public function serviceToChat($uid, $toUid)
    {
        Cache::set('s_chat' . $uid, $toUid, 3600);
    }

    /**
     * @param $uid
     * @param bool $isService
     * @author xaboy
     * @day 2020/6/15
     */
    public function getChat($uid, $isService = false)
    {
        $key = ($isService ? 's_chat' : 'u_chat') . $uid;
        return Cache::get($key);
    }

    /**
     * @param $uid
     * @param bool $isService
     * @author xaboy
     * @day 2020/6/15
     */
    public function unChat($uid, $isService = false)
    {
        $key = ($isService ? 's_chat' : 'u_chat') . $uid;
        Cache::delete($key);
    }

    /**
     * TODO 获取某个客服的用户列表
     * @param $service_id
     * @param $page
     * @param $limit
     * @return array
     * @author Qinii
     * @day 2020-06-18
     */
    public function getServiceUserList($service_id,$page, $limit)
    {
        $query = $this->dao->getUserListQuery($service_id);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('uid,mer_id,create_time,type')->with(['user'])->page($page, $limit)->select();
        return compact('count','list');
    }

    /**
     * TODO 获取商户的聊天用户列表
     * @param $merId
     * @param $page
     * @param $limit
     * @return array
     * @author Qinii
     * @day 2020-06-19
     */
    public function getMerchantUserList($merId,$page, $limit)
    {
        $query = $this->dao->getMerchantUserList($merId);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('uid,mer_id,create_time,type')->with(['user'])->page($page, $limit)->select();
        return compact('count','list');
    }

    /**
     * TODO
     * @param $merId
     * @param $uid
     * @param $page
     * @param $limit
     * @return array
     * @author Qinii
     * @day 2020-06-19
     */
    public function getUserMsn(int $uid,$page,$limit,?int $merId = null,?int $serviceId = null)
    {
        $where['uid'] = $uid;
        if($merId) $where['mer_id'] = $merId;
        if($serviceId) $where['service_id'] = $serviceId;
        $query = $this->search($where)->order('service_log_id DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->with(['user', 'service'])->select();
        return compact('count','list');
    }
}
