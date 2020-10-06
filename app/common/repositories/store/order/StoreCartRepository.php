<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/5/30
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreCartDao;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\coupon\StoreCouponProductRepository;
use app\common\repositories\store\coupon\StoreCouponRepository;

/**
 * Class StoreCartRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/5/30
 * @mixin StoreCartDao
 */
class StoreCartRepository extends BaseRepository
{
    /**
     * StoreCartRepository constructor.
     * @param StoreCartDao $dao
     */
    public function __construct(StoreCartDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param $uid
     * @return array
     * @author Qinii
     */
    public function getList($uid)
    {
        $res = $this->dao->getAll($uid)->append(['checkProductSku'])->toArray();
        return $this->checkCartList($res,$uid);
    }

    public function checkCartList($res, $hasCoupon = 0)
    {
        $arr = $fail = [];
        foreach ($res as $item) {
            if (!$item['product_type'] && !$item['checkProductSku']) {
                $fail[] = $item;
            } else if ($item['product_type'] == 1 && (!$item['userSecikllPayCount'] || !$item['checkSeckillProductSku'])) {
                $fail[] = $item;
            } else {
                $merchantData = $item['merchant'];
                unset($item['merchant']);
                if (!isset($arr[$item['mer_id']])) {
                    if ($hasCoupon)
                        $merchantData['hasCoupon'] = app()->make(StoreCouponRepository::class)->validMerCouponExists($item['mer_id'], $hasCoupon);
                    $arr[$item['mer_id']] = $merchantData;
                }
                if ($hasCoupon && !$arr[$item['mer_id']]['hasCoupon']) {
                    $couponIds = app()->make(StoreCouponProductRepository::class)->productByCouponId([$item['product']['product_id']]);
                    $arr[$item['mer_id']]['hasCoupon'] = count($couponIds) ? app()->make(StoreCouponRepository::class)->validProductCouponExists([$item['product']['product_id']], $hasCoupon) : 0;
                }
                $arr[$item['mer_id']]['list'][] = $item;
            }
        }
        $list = array_values($arr);
        return compact('list', 'fail');
    }

    /**
     * 获取单条购物车信息
     * @Author:Qinii
     * @Date: 2020/5/30
     * @param int $id
     * @return mixed
     */
    public function getOne(int $id,int $uid)
    {
        $where = [$this->dao->getPk() => $id,'is_del'=>0,'is_fail'=>0,'is_new'=>0,'is_pay'=>0,'uid' => $uid];
        return ($this->dao->getWhere($where));
    }

    /**
     * @param $sku
     * @param $uid
     * @author Qinii
     */
    public function getCartByProductSku($sku,$uid)
    {
        $where = ['is_del'=>0,'is_fail'=>0,'is_new'=>0,'is_pay'=>0,'uid' => $uid,'product_type' => 0,'product_attr_unique' => $sku];
        return ($this->dao->getWhere($where));
    }


    public function getProductById($productId)
    {
        $where = [
            'is_del' =>0,
            'is_new'=>0,
            'is_pay'=>0,
            'product_id'=>$productId
        ];
        return $this->dao->getWhereCount($where);
    }
}
