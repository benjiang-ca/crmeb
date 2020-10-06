<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-28
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\model\user;


use app\common\model\BaseModel;
use app\common\model\store\service\StoreService;
use app\common\model\wechat\WechatUser;
use app\common\repositories\store\coupon\StoreCouponUserRepository;
use app\common\repositories\store\order\StoreGroupOrderRepository;
use app\common\repositories\store\service\StoreServiceLogRepository;
use app\common\repositories\user\UserBillRepository;
use app\common\repositories\user\UserExtractRepository;
use app\common\repositories\user\UserRechargeRepository;
use app\common\repositories\user\UserRelationRepository;
use app\common\repositories\user\UserRepository;
use app\common\repositories\user\UserVisitRepository;

/**
 * Class User
 * @package app\common\model\user
 * @author xaboy
 * @day 2020-04-28
 */
class User extends BaseModel
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tablePk(): string
    {
        return 'uid';
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020-03-30
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * @param $value
     * @return string
     * @author xaboy
     * @day 2020-05-09
     */
    public function getBirthdayAttr($value)
    {
        return $value == '0000-00-00' ? '' : $value;
    }

    /**
     * @param $value
     * @return array
     * @author xaboy
     * @day 2020-05-09
     */
    public function getLabelIdAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * @param $value
     * @return string
     * @author xaboy
     * @day 2020-05-09
     */
    public function setLabelIdAttr($value)
    {
        return implode(',', $value);
    }

    public function getValidSpreadUidAttr()
    {
        if (!$this->spread_uid) return 0;
        else {
            $data = self::getDB()->where('uid', $this->spread_uid)->field('is_promoter,spread_uid')->find();
            if ($data && $data['is_promoter'])
                return $this->spread_uid;
            else
                return 0;
        }
    }

    public function getValidTopUidAttr()
    {
        if (!$this->top_uid) return 0;
        else {
            $data = self::getDB()->where('uid', $this->top_uid)->field('is_promoter,spread_uid')->find();
            if ($data && $data['is_promoter'])
                return $this->top_uid;
            else
                return 0;
        }
    }

    public function getTopUidAttr()
    {
        return self::getDB()->where('uid', $this->spread_uid)->value('spread_uid') ?: 0;
    }

    /**
     * @return \think\model\relation\HasOne
     * @author xaboy
     * @day 2020-05-09
     */
    public function group()
    {
        return $this->hasOne(UserGroup::class, 'group_id', 'group_id');
    }

    public function spread()
    {
        return $this->hasOne(User::class, 'uid', 'spread_uid');
    }

    /**
     * @param $spreadUid
     * @author xaboy
     * @day 2020-04-28
     */
    public function setSpread($spreadUid)
    {
        if (self::getDB()->where('uid', $spreadUid)->value('is_promoter'))
            $this->save([
                'spread_uid' => $spreadUid,
                'spread_time' => date('Y-m-d H:i:s')
            ]);
    }

    public function service()
    {
        return $this->hasOne(StoreService::class, 'uid', 'uid')->field('service_id,uid,nickname,avatar,customer,mer_id,is_verify')->where('is_del', 0);
    }

    public function getLockBrokerageAttr()
    {
        return app()->make(UserBillRepository::class)->lockBrokerage($this->uid) ?: 0;
    }

    public function getYesterdayBrokerageAttr()
    {
        return app()->make(UserBillRepository::class)->yesterdayBrokerage($this->uid) ?: 0;
    }

    public function getTotalExtractAttr()
    {
        return app()->make(UserExtractRepository::class)->userTotalExtract($this->uid) ?: 0;
    }

    public function getTotalBrokerageAttr()
    {
        return app()->make(UserBillRepository::class)->totalBrokerage($this->uid) ?: 0;
    }

    public function getTotalBrokeragePriceAttr()
    {
        return bcadd($this->lock_brokerage, $this->brokerage_price, 2);
    }

    public function getTotalRechargeAttr()
    {
        return app()->make(UserBillRepository::class)->userNowMoneyIncTotal($this->uid);
    }

    public function getTotalConsumeAttr()
    {
        return app()->make(StoreGroupOrderRepository::class)->totalNowMoney($this->uid);
    }

    public function getTotalCollectProductAttr()
    {
        return app()->make(UserRelationRepository::class)->getWhereCount(['uid' => $this->uid, 'type' => 1]);
    }

    public function getTotalCollectStoreAttr()
    {
        return app()->make(UserRelationRepository::class)->getWhereCount(['uid' => $this->uid, 'type' => 10]);
    }

    public function getTotalVisitProductAttr()
    {
        return app()->make(UserVisitRepository::class)->userTotalVisit($this->uid);
    }

    public function getTotalCouponAttr()
    {
        return app()->make(StoreCouponUserRepository::class)->userTotal($this->uid);
    }

    public function getTotalUnreadAttr()
    {
        return app()->make(StoreServiceLogRepository::class)->totalUnReadNum($this->uid);
    }

    public function getOneLevelCountAttr()
    {
        return app()->make(UserRepository::class)->getOneLevelCount($this->uid);
    }

    public function getTwoLevelCountAttr()
    {
        return app()->make(UserRepository::class)->getTwoLevelCount($this->uid);
    }

    public function getSpreadTotalAttr()
    {
        return $this->one_level_count + $this->two_level_count;
    }

    public function wechat()
    {
        return $this->hasOne(WechatUser::class,'wechat_user_id','wechat_user_id');
    }
}
