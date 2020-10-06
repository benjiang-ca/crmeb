<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-05-13
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\coupon;


use app\common\dao\store\coupon\StoreCouponDao;
use app\common\dao\store\coupon\StoreCouponProductDao;
use app\common\model\store\coupon\StoreCoupon;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\product\ProductRepository;
use FormBuilder\Exception\FormBuilderException;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Route;

/**
 * Class StoreCouponIssueRepository
 * @package app\common\repositories\store\coupon
 * @author xaboy
 * @day 2020-05-13
 * @mixin StoreCouponDao
 */
class StoreCouponRepository extends BaseRepository
{
    /**
     * @var StoreCouponDao
     */
    protected $dao;

    /**
     * StoreCouponIssueRepository constructor.
     * @param StoreCouponDao $dao
     */
    public function __construct(StoreCouponDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param int $merId
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-05-14
     */
    public function getList(?int $merId, array $where, $page, $limit)
    {
        $baseQuery = $this->dao->search($merId, $where)->with(['merchant' => function ($query) {
            $query->field('mer_id,mer_name,is_trader');
        }]);
        $count = $baseQuery->count($this->dao->getPk());
        $list = $baseQuery->page($page, $limit)->select();
        foreach ($list as $item) {
            $item->append(['used_num', 'send_num']);
        }
        return compact('count', 'list');
    }

    /**
     * @param array $data
     * @author xaboy
     * @day 2020/5/26
     */
    public function create(array $data)
    {
        if (isset($data['total_count'])) $data['remain_count'] = $data['total_count'];
        Db::transaction(function () use ($data) {
            $products = array_column((array)$data['product_id'], 'id');
            unset($data['product_id']);
            if ($data['type'] == 1 && !count($products))
                throw new ValidateException('请选择产品');
            $coupon = $this->dao->create($data);
            if (!count($products)) return $coupon;
            $lst = [];
            foreach ($products as $product) {
                $lst[] = [
                    'product_id' => (int)$product,
                    'coupon_id' => $coupon->coupon_id
                ];
            }
            app()->make(StoreCouponProductDao::class)->insertAll($lst);
        });
    }

    /**
     * @param $id
     * @return Form
     * @throws DataNotFoundException
     * @throws DbException
     * @throws FormBuilderException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/5/26
     */
    public function cloneCouponForm($id)
    {
        $couponInfo = $this->dao->getWith($id, ['product'])->toArray();
        if ($couponInfo['is_timeout']) {
            $couponInfo['range_date'] = [$couponInfo['start_time'], $couponInfo['end_time']];
        }
        if ($couponInfo['coupon_type']) {
            $couponInfo['use_start_time'] = [$couponInfo['use_start_time'], $couponInfo['use_end_time']];
        }
        $couponInfo['product_id'] = [];
        if (count($couponInfo['product'])) {
            $productIds = array_column($couponInfo['product'], 'product_id');
            /** @var ProductRepository $make */
            $make = app()->make(ProductRepository::class);
            $products = $make->productIdByImage($couponInfo['mer_id'], $productIds);
            foreach ($products as $product) {
                $couponInfo['product_id'][] = ['id' => $product['product_id'], 'src' => $product['image']];
            }
        }
        $couponInfo['use_type'] = $couponInfo['use_min_price'] > 0 ? 1 : 0;
        return $this->form()->formData($couponInfo)->setTitle('复制优惠券');
    }

    /**
     * @return Form
     * @throws FormBuilderException
     * @author xaboy
     * @day 2020/5/20
     */
    public function form()
    {
        return Elm::createForm(Route::buildUrl('merchantCouponCreate')->build(), [
            Elm::input('title', '优惠券名称')->required(),
            Elm::radio('type', '优惠券类型', 0)
                ->setOptions([
                    ['value' => 0, 'label' => '店铺券'],
                    ['value' => 1, 'label' => '商品券'],
                ])->control([
                    [
                        'value' => 1,
                        'rule' => [
                            Elm::frameImages('product_id', '商品', '/' . config('admin.merchant_prefix') . '/setting/storeProduct?field=product_id')
                                ->width('680px')->height('480px')->modal(['modal' => false])->prop('srcKey', 'src')->required(),
                        ]
                    ],
                ]),
            Elm::number('coupon_price', '优惠券面值')->min(0)->required(),
            Elm::radio('use_type', ' 使用门槛', 0)
                ->setOptions([
                    ['value' => 0, 'label' => '无门槛'],
                    ['value' => 1, 'label' => '有门槛'],
                ])->appendControl(0, [
                    Elm::hidden('use_min_price', 0)
                ])->appendControl(1, [
                    Elm::number('use_min_price', '优惠券最低消费')->min(0)->required(),
                ]),
            Elm::radio('coupon_type', '使用有效期', 0)
                ->setOptions([
                    ['value' => 0, 'label' => '天数'],
                    ['value' => 1, 'label' => '时间段'],
                ])->control([
                    [
                        'value' => 0,
                        'rule' => [
                            Elm::number('coupon_time', ' ', 0)->min(0)->required(),
                        ]
                    ],
                    [
                        'value' => 1,
                        'rule' => [
                            Elm::dateTimeRange('use_start_time', ' ')->required(),
                        ]
                    ],
                ]),
            Elm::radio('is_timeout', '领取时间', 0)->options([['label' => '限时', 'value' => 1], ['label' => '不限时', 'value' => 0]])
                ->appendControl(1, [Elm::dateTimeRange('range_date', ' ')->placeholder('不填为永久有效')]),
            Elm::radio('send_type', '类型', 0)->setOptions([
                ['value' => 0, 'label' => '领取'],
//                ['value' => 1, 'label' => '消费满赠'],
                ['value' => 2, 'label' => '新人券'],
                ['value' => 3, 'label' => '赠送券']
            ])->appendControl(1, [Elm::number('full_reduction', '满赠金额', 0)->min(0)->placeholder('赠送优惠券的最低消费金额')]),
            Elm::radio('is_limited', '是否限量', 0)->options([['label' => '限量', 'value' => 1], ['label' => '不限量', 'value' => 0]])
                ->appendControl(1, [Elm::number('total_count', '发布数量', 0)->min(0)]),
            Elm::number('sort', '排序', 0),
            Elm::radio('status', '状态', 1)->options([['label' => '开启', 'value' => 1], ['label' => '关闭', 'value' => 0]]),
        ])->setTitle('发布优惠券');
    }

    public function receiveCoupon($id, $uid)
    {
        $coupon = $this->dao->validCoupon($id, $uid);
        if (!$coupon)
            throw new ValidateException('优惠券失效');
        if (!is_null($coupon['issue']))
            throw new ValidateException('优惠券已领取');
        $this->sendCoupon($coupon, $uid);
    }

    public function sendCoupon(StoreCoupon $coupon, $uid, $type = 'receive')
    {
        $data = [
            'uid' => $uid,
            'coupon_title' => $coupon['title'],
            'coupon_price' => $coupon['coupon_price'],
            'use_min_price' => $coupon['use_min_price'],
            'type' => $type,
            'coupon_id' => $coupon['coupon_id'],
            'mer_id' => $coupon['mer_id']
        ];
        if ($coupon['coupon_type'] == 1) {
            $data['start_time'] = $coupon['use_start_time'];
            $data['end_time'] = $coupon['use_end_time'];
        } else {
            $data['start_time'] = date('Y-m-d H:i:s');
            $data['end_time'] = date('Y-m-d H:i:s', strtotime("+ {$coupon['coupon_time']}day"));
        }
        Db::transaction(function () use ($data, $coupon) {
            app()->make(StoreCouponUserRepository::class)->create($data);
            app()->make(StoreCouponIssueUserRepository::class)->issue($coupon['coupon_id'], $data['uid']);
            if ($coupon->is_limited) {
                $coupon->remain_count--;
                $coupon->save();
            }
        });
    }

    /**
     * TODO 优惠券发送费多用户
     * @param $uid
     * @param $id
     * @author Qinii
     * @day 2020-06-19
     */
    public function sendCouponByUser($uid,$id)
    {
        foreach($uid as $item){
            $coupon = $this->dao->validCoupon($id, $item);
            if(!$coupon || !is_null($coupon['issue']))
                continue;
            if ($coupon->is_limited && 0 == $coupon->remain_count)
                continue;
            $this->sendCoupon($coupon, $item);
        }
    }
}
