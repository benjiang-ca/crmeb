<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreOrderDao;
use app\common\model\store\order\StoreGroupOrder;
use app\common\model\store\order\StoreOrder;
use app\common\model\user\User;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\coupon\StoreCouponUserRepository;
use app\common\repositories\store\MerchantTakeRepository;
use app\common\repositories\store\product\ProductAttrValueRepository;
use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\store\shipping\ExpressRepository;
use app\common\repositories\store\StoreSeckillActiveRepository;
use app\common\repositories\system\attachment\AttachmentRepository;
use app\common\repositories\system\merchant\FinancialRecordRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use app\common\repositories\user\UserAddressRepository;
use app\common\repositories\user\UserBillRepository;
use app\common\repositories\user\UserRepository;
use app\common\repositories\wechat\RoutineQrcodeRepository;
use app\common\repositories\wechat\WechatUserRepository;
use crmeb\jobs\PayGiveCouponJob;
use crmeb\jobs\SendSmsJob;
use crmeb\jobs\SendTemplateMessageJob;
use crmeb\services\ExpressService;
use crmeb\services\MiniProgramService;
use crmeb\services\QrcodeService;
use crmeb\services\printer\Printer;
use crmeb\services\SwooleTaskService;
use crmeb\services\UploadService;
use crmeb\services\WechatService;
use Exception;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Queue;
use think\facade\Route;
use think\Model;

/**
 * Class StoreOrderRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/9
 * @mixin StoreOrderDao
 */
class StoreOrderRepository extends BaseRepository
{
    /**
     * 支付类型
     */
    const PAY_TYPE = ['balance', 'weixin', 'routine', 'h5'];

    /**
     * StoreOrderRepository constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param User $user
     * @param int $pay_type
     * @param array $cartId
     * @param int $addressId
     * @param array $coupons
     * @param array $takes
     * @param array $mark
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/8/1
     */
    public function createOrder(User $user, int $pay_type, array $cartId, int $addressId, array $coupons, array $takes, array $mark)
    {
        $uid = $user->uid;
        $couponUserRepository = app()->make(StoreCouponUserRepository::class);
        foreach ($coupons as $merId => $coupon) {
            if (!is_array($coupon)) {
                unset($coupons[$merId]);
                continue;
            }
            $storeCoupon = $coupon['store'] ?? ($coupon['store'] = 0);
            $productCoupon = array_unique($coupon['product'] ?? ($coupon['product'] = []));
            $_coupons = $storeCoupon ? array_merge($productCoupon, [$storeCoupon]) : $productCoupon;
            if (!count($_coupons)) {
                unset($coupons[$merId]);
                continue;
            }
            if (count($couponUserRepository->validIntersection($merId, $uid, $_coupons)) != count($_coupons))
                throw new ValidateException('请选择正确的优惠券');
        }
        $order = $this->cartIdByOrderInfo($uid, $cartId, $addressId, false);
        if ($order['status'] == 'noDeliver')
            throw new ValidateException('部分商品不支持该区域');
        if (!$order['address']) throw new ValidateException('请选择正确的收货地址');
        $orderPrice = 0;
        $orderInfo = $order['order'];
        $address = $order['address'];
        $orderList = [];
        $totalPostage = 0;
        $totalPayPrice = 0;
        $totalCouponPrice = 0;
        $totalNum = 0;
        $totalCost = 0;
        $allUseCoupon = [];
        $productCouponList = [];
        $storeCouponList = [];
        $spreadUid = $user->valid_spread_uid;
        $topUid = $user->valid_top_uid;
        $giveCouponIds = [];
        $merchantRepository = app()->make(MerchantRepository::class);
        foreach ($orderInfo as $k => $cartInfo) {

            if (in_array($cartInfo['mer_id'], $takes) && !$cartInfo['take']['mer_take_status'])
                throw new ValidateException('该店铺不支持到店自提');

            $useCoupon = [];
            if (isset($coupons[$cartInfo['mer_id']])) {
                $coupon = $coupons[$cartInfo['mer_id']];
                if (count($coupon['product'])) {
                    foreach ($cartInfo['coupon'] as $_k => $_coupon) {
                        if (!$_coupon['coupon']['type']) continue;
                        if (in_array($_coupon['coupon_user_id'], $coupon['product'])) {
                            $productId = array_search($_coupon['coupon_user_id'], $coupon['product']);
                            if (!in_array($productId, array_column($_coupon['product'], 'product_id')))
                                throw new ValidateException('请选择正确的优惠券');
                            $useCoupon[] = $_coupon['coupon_user_id'];
                            unset($coupon['product'][$productId]);
                            $productCouponList[$productId] = [
                                'rate' => $cartInfo['order']['product_price'][$productId] > 0 ? bcdiv($_coupon['coupon_price'], $cartInfo['order']['product_price'][$productId], 4) : 1,
                                'coupon_price' => $_coupon['coupon_price'],
                                'price' => $cartInfo['order']['product_price'][$productId]
                            ];
                            $cartInfo['order']['pay_price'] = max(bcsub($cartInfo['order']['pay_price'], $_coupon['coupon_price'], 2), 0);
                            $cartInfo['order']['coupon_price'] = bcadd($cartInfo['order']['coupon_price'], $_coupon['coupon_price'], 2);
                        }
                    }
                    if (count($coupon['product'])) throw new ValidateException('请选择正确的优惠券');
                }
                if ($coupon['store']) {
                    $flag = false;
                    foreach ($cartInfo['coupon'] as $_coupon) {
                        if ($_coupon['coupon']['type']) continue;
                        if ($_coupon['coupon_user_id'] == $coupon['store']) {
                            $flag = true;
                            $useCoupon[] = $_coupon['coupon_user_id'];
                            $storeCouponList[$cartInfo['mer_id']] = [
                                'rate' => $cartInfo['order']['pay_price'] > 0 ? bcdiv($_coupon['coupon_price'], $cartInfo['order']['pay_price'], 4) : 1,
                                'coupon_price' => $_coupon['coupon_price'],
                                'price' => $cartInfo['order']['coupon_price']
                            ];
                            $cartInfo['order']['pay_price'] = max(bcsub($cartInfo['order']['pay_price'], $_coupon['coupon_price'], 2), 0);
                            $cartInfo['order']['coupon_price'] = bcadd($cartInfo['order']['coupon_price'], $_coupon['coupon_price'], 2);
                            break;
                        }
                    }
                    if (!$flag) throw new ValidateException('请选择正确的优惠券');
                }
            }
            $cost = 0;
            $total_extension_one = 0;
            $total_extension_two = 0;
            if (systemConfig('extension_status')) {
                foreach ($cartInfo['list'] as $cart) {
                    $totalNum += $cart['cart_num'];
                    $giveCouponIds = array_merge($giveCouponIds, $cart['product']['give_coupon_ids']);
                    $cost = bcadd(bcmul($cart['productAttr']['cost'], $cart['cart_num'], 2), $cost, 2);
                    if ($spreadUid && $cart['productAttr']['bc_extension_one'] > 0)
                        $total_extension_one = bcadd($total_extension_one, bcmul($cart['cart_num'], $cart['productAttr']['bc_extension_one'], 2), 2);
                    if ($topUid && $cart['productAttr']['bc_extension_two'] > 0)
                        $total_extension_two = bcadd($total_extension_two, bcmul($cart['cart_num'], $cart['productAttr']['bc_extension_two'], 2), 2);
                }
            }
            if (in_array($cartInfo['mer_id'], $takes)) {
                $cartInfo['order']['pay_price'] = max(bcsub($cartInfo['order']['pay_price'], $cartInfo['order']['postage_price'], 2), 0);
                $cartInfo['order']['postage_price'] = 0;
            } else {
                $cartInfo['order']['pay_price'] = max($cartInfo['order']['pay_price'], $cartInfo['order']['postage_price']);
            }

            //TODO 生成订单

            $_order = [
                'commission_rate' => (float)$merchantRepository->get($cartInfo['mer_id'])->mer_commission_rate,
                'order_type' => in_array($cartInfo['mer_id'], $takes) == 1 ? 1 : 0,
                'extension_one' => $total_extension_one,
                'extension_two' => $total_extension_two,
                'orderInfo' => $cartInfo['order'],
                'cartInfo' => $cartInfo['list'],
                'order_sn' => $this->getNewOrderId() . ($k + 1),
                'uid' => $uid,
                'real_name' => $address['real_name'],
                'user_phone' => $address['phone'],
                'user_address' => $address['province'] . $address['city'] . $address['district'] . ' ' . $address['detail'],
                'cart_id' => implode(',', array_column($cartInfo['list'], 'cart_id')),
                'total_num' => $cartInfo['order']['total_num'],
                'total_price' => $cartInfo['order']['total_price'],
                'total_postage' => $cartInfo['order']['postage_price'],
                'pay_postage' => $cartInfo['order']['postage_price'],
                'pay_price' => $cartInfo['order']['pay_price'],
                'mer_id' => $cartInfo['mer_id'],
                'cost' => $cost,
                'coupon_id' => implode(',', $useCoupon),
                'mark' => $mark[$cartInfo['mer_id']] ?? '',
                'coupon_price' => $cartInfo['order']['coupon_price'] > $cartInfo['order']['total_price'] ? $cartInfo['order']['total_price'] : $cartInfo['order']['coupon_price'],
                'pay_type' => $pay_type
            ];

            $allUseCoupon = array_merge($allUseCoupon, $useCoupon);
            $orderList[] = $_order;
            $orderInfo[$k] = $cartInfo;
            $orderPrice = bcadd($orderPrice, $_order['total_price'], 2);
            $totalPostage = bcadd($totalPostage, $_order['total_postage'], 2);
            $totalPayPrice = bcadd($totalPayPrice, $_order['pay_price'], 2);
            $totalCouponPrice = bcadd($totalCouponPrice, $_order['coupon_price'], 2);
            $totalCost = bcadd($totalCost, $cost, 2);
        }
        $groupOrder = [
            'uid' => $uid,
            'group_order_sn' => $this->getNewOrderId() . '0',
            'total_postage' => $totalPostage,
            'total_price' => $orderPrice,
            'total_num' => $totalNum,
            'real_name' => $address['real_name'],
            'user_phone' => $address['phone'],
            'user_address' => $address['province'] . $address['city'] . $address['district'] . ' ' . $address['detail'],
            'pay_price' => $totalPayPrice,
            'coupon_price' => $totalCouponPrice,
            'pay_postage' => $totalPostage,
            'cost' => $totalCost,
            'pay_type' => $pay_type,
            'give_coupon_ids' => implode(',', $giveCouponIds)
        ];
        $storeGroupOrderRepository = app()->make(StoreGroupOrderRepository::class);
        $storeCartRepository = app()->make(StoreCartRepository::class);
        $attrValueRepository = app()->make(ProductAttrValueRepository::class);
        $productRepository = app()->make(ProductRepository::class);
        $storeOrderProductRepository = app()->make(StoreOrderProductRepository::class);

        $group = Db::transaction(function () use ($topUid, $spreadUid, $uid, $productCouponList, $storeCouponList, $allUseCoupon, $couponUserRepository, $storeOrderProductRepository, $productRepository, $attrValueRepository, $storeCartRepository, $storeGroupOrderRepository, $groupOrder, $orderList, $orderInfo) {
            $cartIds = [];
            $uniqueList = [];
            //更新库存
            foreach ($orderInfo as $cartInfo) {
                $cartIds = array_merge($cartIds, array_column($cartInfo['list'], 'cart_id'));
                foreach ($cartInfo['list'] as $cart) {
                    if (!isset($uniqueList[$cart['productAttr']['product_id'] . $cart['productAttr']['unique']]))
                        $uniqueList[$cart['productAttr']['product_id'] . $cart['productAttr']['unique']] = true;
                    else
                        throw new ValidateException('购物车商品信息重复');

                    if (!$cart['product_type']) {
                        $attrValueRepository->descStock($cart['productAttr']['product_id'], $cart['productAttr']['unique'], $cart['cart_num']);
                        $productRepository->descStock($cart['product']['product_id'], $cart['cart_num']);
                    }
                    if ($cart['product_type']) {
                        $attrValueRepository->descSkuStock($cart['product']['old_product_id'], $cart['productAttr']['sku'], $cart['cart_num']);
                        $productRepository->descStock($cart['product']['old_product_id'], $cart['cart_num']);
                    }
                }
            }
            //修改购物车状态
            $storeCartRepository->updates($cartIds, [
                'is_pay' => 1
            ]);
            //使用优惠券
            $couponUserRepository->updates($allUseCoupon, [
                'use_time' => date('Y-m-d H:i:s'),
                'status' => 1
            ]);
            $groupOrder = $storeGroupOrderRepository->create($groupOrder);
            foreach ($orderList as $k => $order) {
                $orderList[$k]['group_order_id'] = $groupOrder->group_order_id;
            }
            $storeOrderStatusRepository = app()->make(StoreOrderStatusRepository::class);
            $orderProduct = [];
            $orderStatus = [];
            foreach ($orderList as $order) {
                $cartInfo = $order['cartInfo'];
                $_orderInfo = $order['orderInfo'];
                unset($order['cartInfo'], $order['orderInfo']);
                $_order = $this->dao->create($order);
                foreach ($cartInfo as $k => $cart) {
                    $cartTotalPrice = bcmul($cart['productAttr']['price'], $cart['cart_num'], 2);
                    if (!$cart['product_type'] && $cartTotalPrice > 0 && isset($productCouponList[$cart['product_id']])) {
                        if ($productCouponList[$cart['product_id']]['rate'] >= 1) {
                            $cartTotalPrice = 0;
                        } else {
                            array_pop($_orderInfo['product_cart']);
                            if (!count($_orderInfo['product_cart'])) {
                                $cartTotalPrice = bcsub($cartTotalPrice, $productCouponList[$cart['product_id']]['coupon_price'], 2);
                            } else {
                                $couponPrice = bcmul($cartTotalPrice, $productCouponList[$cart['product_id']]['rate'], 2);
                                $cartTotalPrice = bcsub($cartTotalPrice, $couponPrice, 2);
                                $productCouponList[$cart['product_id']]['coupon_price'] = bcsub($productCouponList[$cart['product_id']]['coupon_price'], $couponPrice, 2);
                            }
                        }
                    }

                    if (!$cart['product_type'] && $cartTotalPrice > 0 && isset($storeCouponList[$order['mer_id']])) {
                        if ($storeCouponList[$order['mer_id']]['rate'] >= 1) {
                            $cartTotalPrice = 0;
                        } else {
                            if (count($cartInfo) == $k + 1) {
                                $cartTotalPrice = bcsub($cartTotalPrice, $storeCouponList[$order['mer_id']]['coupon_price'], 2);
                            } else {
                                $couponPrice = bcmul($cartTotalPrice, $storeCouponList[$order['mer_id']]['rate'], 2);
                                $cartTotalPrice = bcsub($cartTotalPrice, $couponPrice, 2);
                                $storeCouponList[$order['mer_id']]['coupon_price'] = bcsub($storeCouponList[$order['mer_id']]['coupon_price'], $couponPrice, 2);
                            }
                        }
                    }

                    $cartTotalPrice = max($cartTotalPrice, 0);

                    $orderStatus[] = [
                        'order_id' => $_order->order_id,
                        'change_message' => '订单生成',
                        'change_type' => 'create'
                    ];

                    $orderProduct[] = [
                        'order_id' => $_order->order_id,
                        'cart_id' => $cart['cart_id'],
                        'uid' => $uid,
                        'product_id' => $cart['product_id'],
                        'product_price' => $cartTotalPrice,
                        'extension_one' => $spreadUid ? $cart['productAttr']['bc_extension_one'] : 0,
                        'extension_two' => $topUid ? $cart['productAttr']['bc_extension_two'] : 0,
                        'product_sku' => $cart['productAttr']['unique'],
                        'product_num' => $cart['cart_num'],
                        'refund_num' => $cart['cart_num'],
                        'cart_info' => json_encode([
                            'product' => $cart['product'],
                            'productAttr' => $cart['productAttr'],
                            'product_type' => $cart['product_type']
                        ])
                    ];
                }
                app()->make(MerchantRepository::class)->incSales($order['mer_id'], $order['total_num']);
            }
            $storeOrderStatusRepository->insertAll($orderStatus);
            $storeOrderProductRepository->insertAll($orderProduct);
            return $groupOrder;
        });
        foreach ($orderInfo as $cartInfo) {
            foreach ($cartInfo['list'] as $cart) {
                if (($cart['productAttr']['stock'] - $cart['cart_num']) < (int)merchantConfig($cartInfo['mer_id'], 'mer_store_stock')) {
                    SwooleTaskService::merchant('notice', [
                        'type' => 'min_stock',
                        'data' => [
                            'title' => '库存不足',
                            'message' => $cart['product']['store_name'] . '(' . $cart['productAttr']['sku'] . ')库存不足',
                            'id' => $cart['product']['product_id']
                        ]
                    ], $cartInfo['mer_id']);
                }
            }
        }
        queue::push(SendTemplateMessageJob::class, ['tempCode' => 'ORDER_CREATE', 'id' => $group->group_order_id]);
        return $group;
    }

    /**
     * @param string $type
     * @param User $user
     * @param StoreGroupOrder $groupOrder
     * @return mixed
     * @author xaboy
     * @day 2020/6/9
     */
    public function pay(string $type, User $user, StoreGroupOrder $groupOrder)
    {
        $method = 'pay' . ucfirst($type);
        if (!method_exists($this, $method))
            throw new ValidateException('不支持该支付方式');
        return $this->{$method}($user, $groupOrder);
    }

    /**
     * @param User $user
     * @param StoreGroupOrder $groupOrder
     * @return mixed
     * @author xaboy
     * @day 2020/6/9
     */
    public function payBalance(User $user, StoreGroupOrder $groupOrder)
    {
        if (!systemConfig('yue_pay_status'))
            throw new ValidateException('未开启余额支付');
        if ($user['now_money'] < $groupOrder['pay_price'])
            throw new ValidateException('余额不足' . floatval($groupOrder['pay_price']));
        Db::transaction(function () use ($user, $groupOrder) {
            $user->now_money = bcsub($user->now_money, $groupOrder['pay_price'], 2);
            $user->save();
            $userBillRepository = app()->make(UserBillRepository::class);
            $userBillRepository->decBill($user['uid'], 'now_money', 'pay_product', [
                'link_id' => $groupOrder['group_order_id'],
                'status' => 1,
                'title' => '购买商品',
                'number' => $groupOrder['pay_price'],
                'mark' => '余额支付支付' . floatval($groupOrder['pay_price']) . '元购买商品',
                'balance' => $user->now_money
            ]);
            $this->paySuccess($groupOrder);
        });
        return app('json')->status('success', '余额支付成功', ['order_id' => $groupOrder['group_order_id']]);
    }

    public function changePayType(StoreGroupOrder $groupOrder, int $pay_type)
    {
        Db::transaction(function () use ($groupOrder, $pay_type) {
            $groupOrder->pay_type = $pay_type;
            foreach ($groupOrder->orderList as $order) {
                $order->pay_type = $pay_type;
                $order->save();
            }
            $order->save();
        });
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/8/3
     */
    public function verifyCode()
    {
        $code = substr(uniqid('', true), 15) . substr(microtime(), 2, 8);
        if ($this->dao->existsWhere(['verify_code' => $code]))
            return $this->verifyCode();
        else
            return $code;
    }

    /**
     * //TODO 支付成功后
     *
     * @param StoreGroupOrder $groupOrder
     * @author xaboy
     * @day 2020/6/9
     */
    public function paySuccess(StoreGroupOrder $groupOrder)
    {
        $groupOrder->append(['user']);
        //修改订单状态
        Db::transaction(function () use ($groupOrder) {
            $time = date('Y-m-d H:i:s');
            $groupOrder->paid = 1;
            $groupOrder->pay_time = $time;
            $orderStatus = [];
            $groupOrder->append(['orderList.orderProduct']);
            $flag = true;
            $finance = [];
            $financialRecordRepository = app()->make(FinancialRecordRepository::class);
            $financeSn = $financialRecordRepository->getSn();
            foreach ($groupOrder->orderList as $_k => $order) {
                $order->paid = 1;
                $order->pay_time = $time;
                if ($order->order_type == 1)
                    $order->verify_code = $this->verifyCode();
                $order->save();
                $orderStatus[] = [
                    'order_id' => $order->order_id,
                    'change_message' => '订单支付成功(' . ($order->pay_type == 0 ? '余额' : ($order->pay_type == 2 ? '小程序' : '微信')) . ')',
                    'change_type' => 'pay_success'
                ];

                //TODO 成为推广员
                foreach ($order->orderProduct as $product) {
                    if ($flag && $product['cart_info']['product']['is_gift_bag']) {
                        app()->make(UserRepository::class)->promoter($order->uid);
                        $flag = false;
                    }
                }

                $finance[] = [
                    'order_id' => $order->order_id,
                    'order_sn' => $order->order_sn,
                    'user_info' => $groupOrder->user->nickname,
                    'user_id' => $groupOrder->uid,
                    'financial_type' => 'order',
                    'financial_pm' => 1,
                    'number' => $order->pay_price,
                    'mer_id' => $order->mer_id,
                    'financial_record_sn' => $financeSn . ($_k + 1)
                ];

                SwooleTaskService::merchant('notice', [
                    'type' => 'new_order',
                    'data' => [
                        'title' => '新订单',
                        'message' => '您有一个新的订单',
                        'id' => $order->order_id
                    ]
                ], $order->mer_id);
            }
            app()->make(UserRepository::class)->update($groupOrder->uid, [
                'pay_count' => Db::raw('pay_count+' . count($groupOrder->orderList)),
                'pay_price' => Db::raw('pay_price+' . $groupOrder->pay_price),
            ]);
            $financialRecordRepository->insertAll($finance);
            app()->make(StoreOrderStatusRepository::class)->insertAll($orderStatus);
            $groupOrder->save();
        });

        if (count($groupOrder['give_coupon_ids']) > 0) {
            try {
                Queue::push(PayGiveCouponJob::class, ['ids' => $groupOrder['give_coupon_ids'], 'uid' => $groupOrder['uid']]);
            } catch (Exception $e) {
            }
        }

        queue::push(SendTemplateMessageJob::class, [
            'tempCode' => 'ORDER_PAY_SUCCESS',
            'id' => $groupOrder->group_order_id
        ]);
        Queue::push(SendSmsJob::class, [
            'tempId' => 'PAY_SUCCESS_CODE',
            'id' => $groupOrder->group_order_id
        ]);
        Queue::push(SendSmsJob::class, [
            'tempId' => 'ADMIN_PAY_SUCCESS_CODE',
            'id' => $groupOrder->group_order_id
        ]);
    }

    /**
     * @param User $user
     * @param StoreGroupOrder $groupOrder
     * @return ValidateException
     * @author xaboy
     * @day 2020/6/9
     */
    public function payWeixin(User $user, StoreGroupOrder $groupOrder)
    {
        $wechatUserRepository = app()->make(WechatUserRepository::class);
        $openId = $wechatUserRepository->idByOpenId($user['wechat_user_id']);
        if (!$openId)
            return new ValidateException('请关联微信公众号!');
        $config = WechatService::create()->jsPay($openId, $groupOrder['group_order_sn'], $groupOrder['pay_price'], 'order', '订单支付');
        return app('json')->status('weixin', ['config' => $config, 'order_id' => $groupOrder['group_order_id']]);
    }

    /**
     * @param User $user
     * @param StoreGroupOrder $groupOrder
     * @return ValidateException
     * @author xaboy
     * @day 2020/6/9
     */
    public function payRoutine(User $user, StoreGroupOrder $groupOrder)
    {
        $wechatUserRepository = app()->make(WechatUserRepository::class);
        $openId = $wechatUserRepository->idByRoutineId($user['wechat_user_id']);
        if (!$openId)
            return new ValidateException('请关联微信小程序!');
        $config = MiniProgramService::create()->jsPay($openId, $groupOrder['group_order_sn'], $groupOrder['pay_price'], 'order', '订单支付');
        return app('json')->status('routine', ['config' => $config, 'order_id' => $groupOrder['group_order_id']]);
    }

    /**
     * @param User $user
     * @param StoreGroupOrder $groupOrder
     * @return mixed
     * @author xaboy
     * @day 2020/6/9
     */
    public function payH5(User $user, StoreGroupOrder $groupOrder)
    {
        $config = WechatService::create()->paymentPrepare(null, $groupOrder['group_order_sn'], $groupOrder['pay_price'], 'order', '订单支付', '', 'MWEB');
        return app('json')->status('h5', ['config' => $config, 'order_id' => $groupOrder['group_order_id']]);
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/9
     */
    public function getNewOrderId()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = number_format((floatval($msec) + floatval($sec)) * 1000, 0, '', '');
        $orderId = 'wx' . $msectime . mt_rand(10000, max(intval($msec * 10000) + 10000, 98369));
        return $orderId;
    }

    /**
     * @param $uid
     * @param array $cartId
     * @param int|null $addressId
     * @param bool $confirm
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/9
     */
    public function cartIdByOrderInfo($uid, array $cartId, int $addressId = null, $confirm = true)
    {
        $address = null;
        if ($addressId) {
            $addressRepository = app()->make(UserAddressRepository::class);
            $address = $addressRepository->getWhere(['uid' => $uid, 'address_id' => $addressId]);
            if (!$address['city_id'])
                throw new ValidateException('请选择正确的收货地址');
        }
        $storeCartRepository = app()->make(StoreCartRepository::class);
        $res = $storeCartRepository->checkCartList($storeCartRepository->cartIbByData($cartId, $uid, isset($address) ? $address['city_id'] : null, $confirm)->append(['checkProductSku', 'userSecikllPayCount', 'checkSeckillProductSku'])->toArray());
        $merchantInfo = $res['list'];
        $fail = $res['fail'];

        if (count($fail)) {
            if ($fail[0]['is_fail'])
                throw new ValidateException($fail[0]['product']['store_name'] . ' 已失效');
            if (!$fail[0]['product_type']) {
                if (!$fail[0]['checkProductSku'])
                    throw new ValidateException($fail[0]['product']['store_name'] . ' 库存不足' . $fail[0]['cart_num']);
            } else {
                if (!$fail[0]['userSecikllPayCount'])
                    throw new ValidateException($fail[0]['product']['store_name'] . ' 已超出购买限制');
                if (!$fail[0]['checkSeckillProductSku'])
                    throw new ValidateException($fail[0]['product']['store_name'] . ' 库存不足');
            }
            throw new ValidateException($fail[0]['product']['store_name'] . ' 已失效');
        }

        $merchantTakeRepository = app()->make(MerchantTakeRepository::class);
        $order_price = 0;
        $order_total_price = 0;
        $noDeliver = false;
        foreach ($merchantInfo as $k => $cartInfo) {
            $postageRule = [];
            $total_price = 0;
            $total_num = 0;
            $valid_total_price = 0;
            $postage_price = 0;
            $product_price = [];

            //TODO 计算邮费
            foreach ($cartInfo['list'] as $_k => $cart) {

                $price = bcmul($cart['cart_num'], $cart['productAttr']['price'], 2);
                $total_price = bcadd($total_price, $price, 2);
                $total_num += $cart['cart_num'];
                if (!$cart['product_type']) {
                    $product_price[$cart['product_id']] = bcadd($product_price[$cart['product_id']] ?? 0, $price, 2);
                    $valid_total_price = bcadd($valid_total_price, $price, 2);
                }
                if (!isset($product_cart[$cart['product_id']]))
                    $product_cart[$cart['product_id']] = [$cart['cart_id']];
                else
                    $product_cart[$cart['product_id']][] = $cart['cart_id'];
                if (!$address || !$cart['product']['temp']) {
                    $cartInfo['list'][$_k]['undelivered'] = true;
                    $noDeliver = true;
                    continue;
                }
                $temp1 = $cart['product']['temp'];
                $cart['undelivered'] = $temp1['undelivery'] && isset($temp1['undelives']);
                $free = $temp1['free'][0] ?? null;
                $region = $temp1['region'][0] ?? null;
                unset($cartInfo['list'][$_k]['product']['temp']);
                $cartInfo['list'][$_k] = $cart;

                if ($cart['undelivered']) {
                    $noDeliver = true;
                    continue;
                }

                if (!isset($postageRule[$cart['product']['temp_id']])) {
                    $postageRule[$cart['product']['temp_id']] = [
                        'free' => null,
                        'region' => null
                    ];
                }
                $number = $this->productByTempNumber($cart);
                $freeRule = $postageRule[$cart['product']['temp_id']]['free'];
                $regionRule = $postageRule[$cart['product']['temp_id']]['region'];
                if ($temp1['appoint'] && $free) {
                    if (!isset($freeRule)) {
                        $freeRule = $free;
                        $freeRule['cart_price'] = 0;
                        $freeRule['cart_number'] = 0;
                    }
                    $freeRule['cart_number'] = bcadd($freeRule['cart_number'], $number, 2);
                    $freeRule['cart_price'] = bcadd($freeRule['cart_price'], $price, 2);
                }

                if ($region) {
                    if (!isset($regionRule)) {
                        $regionRule = $region;
                        $regionRule['cart_price'] = 0;
                        $regionRule['cart_number'] = 0;
                    }
                    $regionRule['cart_number'] = bcadd($regionRule['cart_number'], $number, 2);
                    $regionRule['cart_price'] = bcadd($regionRule['cart_price'], $price, 2);
                }
                $postageRule[$cart['product']['temp_id']]['free'] = $freeRule;
                $postageRule[$cart['product']['temp_id']]['region'] = $regionRule;
            }

            foreach ($postageRule as $item) {
                $freeRule = $item['free'];
                if ($freeRule && $freeRule['cart_number'] >= $freeRule['number'] && $freeRule['cart_price'] >= $freeRule['price'])
                    continue;
                if (!$item['region']) continue;
                $regionRule = $item['region'];
                $postage = bcadd($postage_price, $regionRule['first_price'], 2);
                if ($regionRule['cart_number'] > $regionRule['first']) {
                    $num = ceil(bcdiv(bcsub($regionRule['cart_number'], $regionRule['first'], 2), $regionRule['continue'], 2));
                    $postage = bcadd($postage, bcmul($num, $regionRule['continue_price'], 2));
                }
                $postage_price = bcadd($postage_price, $postage, 2);
            }
            $coupon_price = 0;
            $use_coupon_product = [];
            $use_store_coupon = 0;
            foreach ($cartInfo['coupon'] as $__k => $coupon) {
                if (!$coupon['coupon']['type']) continue;
                //商品券
                if (count(array_intersect(array_column($coupon['product'], 'product_id'), array_keys($product_price))) == 0) {
                    unset($cartInfo['coupon'][$__k]);
                    continue;
                }
                $flag = false;
                foreach ($coupon['product'] as $_product) {
                    if (isset($product_price[$_product['product_id']]) && $product_price[$_product['product_id']] >= $coupon['use_min_price']) {
                        $flag = true;
                        if ($confirm && !isset($use_coupon_product[$_product['product_id']])) {
                            $coupon_price = bcadd($coupon_price, $coupon['coupon_price'], 2);
                            $use_coupon_product[$_product['product_id']] = $coupon['coupon_user_id'];
                            $cartInfo['coupon'][$__k]['checked'] = true;
                        }
                        break;
                    }
                }
                if (!isset($cartInfo['coupon'][$__k]['checked']))
                    $cartInfo['coupon'][$__k]['checked'] = false;
                if (!$flag) unset($cartInfo['coupon'][$__k]);
            }
            $pay_price = max(bcsub($valid_total_price, $coupon_price, 2), 0);
            $_pay_price = $pay_price;
            foreach ($cartInfo['coupon'] as $__k => $coupon) {
                if ($coupon['coupon']['type']) continue;
                //店铺券
                if ($pay_price >= $coupon['use_min_price']) {
                    if (!$confirm || $pay_price <= 0 || $use_store_coupon) {
                        $cartInfo['coupon'][$__k]['checked'] = false;
                        continue;
                    }
                    $use_store_coupon = $coupon['coupon_user_id'];
                    $coupon_price = bcadd($coupon_price, $coupon['coupon_price'], 2);
                    $_pay_price = bcsub($_pay_price, $coupon['coupon_price'], 2);
                    $cartInfo['coupon'][$__k]['checked'] = true;
                } else {
                    unset($cartInfo['coupon'][$__k]);
                }
            }
            $take = $merchantTakeRepository->get($cartInfo['mer_id']);
            $org_price = bcadd(bcsub($total_price, $valid_total_price, 2), max($_pay_price, 0), 2);
            $coupon_price = min($coupon_price, $total_price);
            $pay_price = bcadd($postage_price, $org_price, 2);
            $cartInfo['take'] = $take['mer_take_status'] == '1' ? $take : ['mer_take_status' => 0];
            $cartInfo['coupon'] = array_values($cartInfo['coupon']);
            $cartInfo['order'] = compact('valid_total_price', 'product_cart', 'product_price', 'postage_price', 'org_price', 'total_price', 'total_num', 'pay_price', 'coupon_price', 'use_coupon_product', 'use_store_coupon');
            $merchantInfo[$k] = $cartInfo;
            $order_price = bcadd($order_price, $merchantInfo[$k]['order']['pay_price'], 2);
            $order_total_price = bcadd($order_total_price, $total_price, 2);
        }
        return ['order_price' => $order_price, 'total_price' => $order_total_price, 'address' => $address, 'order' => $merchantInfo, 'status' => $address ? ($noDeliver ? 'noDeliver' : 'finish') : 'noAddress'];
    }

    /**
     * @param $cart
     * @return string
     * @author xaboy
     * @day 2020/6/9
     */
    public function productByTempNumber($cart)
    {
        $type = $cart['product']['temp']['type'];
        $cartNum = $cart['cart_num'];
        if (!$type)
            return $cartNum;
        else if ($type == 2) {
            return bcmul($cartNum, $cart['productAttr']['weight'], 2);
        } else {
            return bcmul($cartNum, $cart['productAttr']['volume'], 2);
        }
    }


    /**
     * @param int $uid
     * @return array
     * @author xaboy
     * @day 2020/6/10
     */
    public function userOrderNumber(int $uid)
    {
        $noPay = app()->make(StoreGroupOrderRepository::class)->orderNumber($uid);
        $noPostage = $this->dao->search(['uid' => $uid, 'status' => 0, 'paid' => 1])->where('is_del', 0)->count();
        $all = $this->dao->search(['uid' => $uid, 'paid' => 1])->where('is_del', 0)->count();
        $noDeliver = $this->dao->search(['uid' => $uid, 'status' => 1, 'paid' => 1])->where('is_del', 0)->count();
        $noComment = $this->dao->search(['uid' => $uid, 'status' => 2, 'paid' => 1])->where('is_del', 0)->count();
        $done = $this->dao->search(['uid' => $uid, 'status' => 3, 'paid' => 1])->where('is_del', 0)->count();
        $refund = app()->make(StoreRefundOrderRepository::class)->getWhereCount(['uid' => $uid, 'status' => [0, 1, 2]]);
        $orderPrice = $this->dao->search(['uid' => $uid, 'paid' => 1])->sum('pay_price');
        $orderCount = $this->dao->search(['uid' => $uid, 'paid' => 1])->count();
        return compact('noComment', 'done', 'refund', 'noDeliver', 'noPay', 'noPostage', 'orderPrice', 'orderCount', 'all');
    }

    /**
     * @param $id
     * @param null $uid
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function getDetail($id, $uid = null)
    {
        $where = [];
        if ($uid) $where['uid'] = $uid;
        return $this->dao->search($where)->where('order_id', $id)->where('is_del', 0)->with(['orderProduct', 'merchant' => function ($query) {
            return $query->field('mer_id,mer_name');
        }])->when(!$uid, function ($query) {
            $query->with(['user' => function ($query) {
                return $query->field('uid,nickname');
            }]);
        })->find();
    }

    public function codeByDetail($code, $uid = null)
    {
        $where = [];
        if ($uid) $where['uid'] = $uid;
        return $this->dao->search($where)->where('verify_code', $code)->where('is_del', 0)->with(['orderProduct', 'merchant' => function ($query) {
            return $query->field('mer_id,mer_name');
        }])->find();
    }

    /**
     * @param StoreOrder $order
     * @param User $user
     * @author xaboy
     * @day 2020/8/3
     */
    public function computed(StoreOrder $order, User $user)
    {
        $userBillRepository = app()->make(UserBillRepository::class);
        //TODO 添加冻结佣金
        if ($order->extension_one > 0 && $user->spread_uid) {
            $userBillRepository->incBill($user->spread_uid, 'brokerage', 'order_one', [
                'link_id' => $order['order_id'],
                'status' => 0,
                'title' => '获得推广佣金',
                'number' => $order->extension_one,
                'mark' => $user['nickname'] . '成功消费' . floatval($order['pay_price']) . '元,奖励推广佣金' . floatval($order->extension_one),
                'balance' => 0
            ]);
            $userRepository = app()->make(UserRepository::class);
            $userRepository->incBrokerage($user->spread_uid, $order->extension_one);
            app()->make(FinancialRecordRepository::class)->dec([
                'order_id' => $order->order_id,
                'order_sn' => $order->order_sn,
                'user_info' => $userRepository->getUsername($user->spread_uid),
                'user_id' => $user->spread_uid,
                'financial_type' => 'brokerage_one',
                'number' => $order->extension_one,
            ], $order->mer_id);
        }
        if ($order->extension_two > 0 && $user->top_uid) {
            $userBillRepository->incBill($user->top_uid, 'brokerage', 'order_two', [
                'link_id' => $order['order_id'],
                'status' => 0,
                'title' => '获得推广佣金',
                'number' => $order->extension_two,
                'mark' => $user['nickname'] . '成功消费' . floatval($order['pay_price']) . '元,奖励推广佣金' . floatval($order->extension_two),
                'balance' => 0
            ]);
            $userRepository = app()->make(UserRepository::class);
            $userRepository->incBrokerage($user->top_uid, $order->extension_two);
            app()->make(FinancialRecordRepository::class)->dec([
                'order_id' => $order->order_id,
                'order_sn' => $order->order_sn,
                'user_info' => $userRepository->getUsername($user->top_uid),
                'user_id' => $user->top_uid,
                'financial_type' => 'brokerage_two',
                'number' => $order->extension_two,
            ], $order->mer_id);
        }
    }

    /**
     * @param StoreOrder $order
     * @param User $user
     * @param string $type
     * @author xaboy
     * @day 2020/8/3
     */
    public function takeAfter(StoreOrder $order, User $user)
    {
        Db::transaction(function () use ($user, $order) {
            $this->computed($order, $user);
            //TODO 确认收货
            app()->make(StoreOrderStatusRepository::class)->status($order->order_id, 'take', '已收货');
            Queue::push(SendTemplateMessageJob::class, [
                'tempCode' => 'ORDER_TAKE_SUCCESS',
                'id' => $order->order_id
            ]);
            Queue::push(SendSmsJob::class, [
                'tempId' => 'TAKE_DELIVERY_CODE',
                'id' => $order->order_id
            ]);
            Queue::push(SendSmsJob::class, [
                'tempId' => 'ADMIN_TAKE_DELIVERY_CODE',
                'id' => $order->order_id
            ]);
            $order->save();
        });
    }

    /**
     * @param $id
     * @param User $user
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/17
     */
    public function takeOrder($id, ?User $user = null)
    {
        $order = $this->dao->search(!$user ? [] : ['uid' => $user->uid], null)->where('order_id', $id)->where('is_del', 0)->find();
        if (!$order)
            throw new ValidateException('订单不存在');
        if ($order['status'] != 1 || $order['order_type'])
            throw new ValidateException('订单状态有误');
        if (!$user) $user = $order->user;
        $order->status = 2;
        Db::transaction(function () use ($order, $user) {
            $this->takeAfter($order, $user);
            $order->save();
        });
    }


    /**
     *  获取订单列表头部统计数据
     * @Author:Qinii
     * @Date: 2020/9/12
     * @param int|null $merId
     * @param int|null $orderType
     * @return array
     */
    public function OrderTitleNumber(?int $merId, ?int $orderType)
    {
        $where = [];
        $sysDel = $merId ? 0 : null;    //商户师傅删除
        if ($merId) $where['mer_id'] = $merId; //商户订单
        if ($orderType === 0) $where['order_type'] = 0; //普通订单
        if ($orderType === 1) $where['take_order'] = 1; //已核销订单
        //1: 未支付 2: 未发货 3: 待收货 4: 待评价 5: 交易完成 6: 已退款 7: 已删除
        $all = $this->dao->search($where, $sysDel)->where($this->getOrderType(0))->count();
        $statusAll = $all;
        $unpaid = $this->dao->search($where, $sysDel)->where($this->getOrderType(1))->count();
        $unshipped = $this->dao->search($where, $sysDel)->where($this->getOrderType(2))->count();
        $untake = $this->dao->search($where, $sysDel)->where($this->getOrderType(3))->count();
        $unevaluate = $this->dao->search($where, $sysDel)->where($this->getOrderType(4))->count();
        $complete = $this->dao->search($where, $sysDel)->where($this->getOrderType(5))->count();
        $refund = $this->dao->search($where, $sysDel)->where($this->getOrderType(6))->count();
        $del = $this->dao->search($where, $sysDel)->where($this->getOrderType(7))->count();

        return compact('all', 'statusAll', 'unpaid', 'unshipped', 'untake', 'unevaluate', 'complete', 'refund', 'del');
    }

    public function orderType(array $where)
    {
        return [
            [
                'count' => $this->dao->search($where)->count(),
                'title' => '全部',
                'order_type' => -1,
            ],
            [
                'count' => $this->dao->search($where)->where('order_type', 0)->count(),
                'title' => '普通订单',
                'order_type' => 0,
            ],
            [
                'count' => $this->dao->search($where)->where('order_type', 1)->count(),
                'title' => '核销订单',
                'order_type' => 1,
            ],
        ];
    }

    /**
     * @param $status
     * @return mixed
     * @author Qinii
     */
    public function getOrderType($status)
    {
        $param['is_del'] = 0;
        switch ($status) {
            case 1:
                $param['paid'] = 0;
                break;    // 未支付
            case 2:
                $param['paid'] = 1;
                $param['status'] = 0;
                break;  // 待发货
            case 3:
                $param['status'] = 1;
                break;  // 待收货
            case 4:
                $param['status'] = 2;
                break;  // 待评价
            case 5:
                $param['status'] = 3;
                break;  // 交易完成
            case 6:
                $param['status'] = -1;
                break; // 已退款
            case 7:
                $param['is_del'] = 1;
                break;  // 已删除
            default:
                unset($param['is_del']);
                break;         //全部
        }
        return $param;
    }

    /**
     * @param $id
     * @return mixed
     * @author Qinii
     */
    public function sendProductForm($id)
    {
        return app()->make(ExpressRepository::class)->sendProductForm($id);
    }

    /**
     * @param int $id
     * @param int|null $merId
     * @return array|Model|null
     * @author Qinii
     */
    public function merDeliveryExists(int $id, ?int $merId)
    {
        $where = ['order_id' => $id, 'is_del' => 0, 'paid' => 1, 'status' => 0];
        if ($merId) $where['mer_id'] = $merId;
        return $this->dao->merFieldExists($where);
    }

    /**
     * TODO
     * @param int $id
     * @param int|null $merId
     * @return bool
     * @author Qinii
     * @day 2020-06-11
     */
    public function merGetDeliveryExists(int $id, ?int $merId)
    {
        $where = ['order_id' => $id, 'is_del' => 0, 'paid' => 1, 'status' => 1];
        if ($merId) $where['mer_id'] = $merId;
        return $this->dao->merFieldExists($where);
    }

    /**
     * @param int $id
     * @param int|null $merId
     * @return array|Model|null
     * @author Qinii
     */
    public function merStatusExists(int $id, ?int $merId)
    {
        $where = ['order_id' => $id, 'is_del' => 0, 'paid' => 0, 'status' => 0];
        if ($merId) $where['mer_id'] = $merId;
        return $this->dao->merFieldExists($where);
    }

    public function userDelExists(int $id, ?int $merId)
    {
        $where = ['order_id' => $id, 'is_del' => 1];
        if ($merId) $where['mer_id'] = $merId;
        return $this->dao->merFieldExists($where);
    }

    /**
     * @param $id
     * @return Form
     * @author Qinii
     */
    public function form($id)
    {
        $data = $this->dao->getWhere([$this->dao->getPk() => $id], 'total_price,pay_price,total_postage,pay_postage');
        $form = Elm::createForm(Route::buildUrl('merchantStoreOrderUpdate', ['id' => $id])->build());
        $form->setRule([
            Elm::number('total_price', '订单总价', $data['total_price'])->required(),
            Elm::number('pay_price', '实际支付金额', $data['pay_price'])->required(),
            Elm::number('total_postage', '订单邮费', $data['total_postage'])->required(),
            Elm::number('pay_postage', '实际支付邮费', $data['pay_postage'])->required()
        ]);
        return $form->setTitle('修改订单');
    }

    public function eidt(int $id, array $data)
    {
        Db::transaction(function () use ($id, $data) {
            $oldOrder = $this->dao->getWhere([$this->dao->getPk() => $id]);
            $make = app()->make(StoreGroupOrderRepository::class);
            $orderGroup = $make->dao->getWhere(['group_order_id' => $oldOrder['group_order_id']]);
            $pay_price = bcadd(bcsub($orderGroup['pay_price'], $oldOrder['pay_price'], 2), $data['pay_price'], 2);
            $orderGroup->pay_price = ($pay_price < 0) ? 0 : $pay_price;
            if (isset($data['total_price'])) {
                $total_price = bcadd(bcsub($orderGroup['total_price'], $oldOrder['total_price'], 2), $data['total_price'], 2);
                $orderGroup->total_price = ($total_price < 0) ? 0 : $total_price;
            }
            if (isset($data['total_postage'])) {
                $total_postage = bcadd(bcsub($orderGroup['total_postage'], $oldOrder['total_postage'], 2), $data['total_postage'], 2);
                $orderGroup->total_postage = ($total_postage < 0) ? 0 : $total_postage;
            }
            if (isset($data['pay_postage'])) {
                $pay_postage = bcadd(bcsub($orderGroup['pay_postage'], $oldOrder['pay_postage'], 2), $data['pay_postage'], 2);
                $orderGroup->pay_postage = ($pay_postage < 0) ? 0 : $pay_postage;
            }

            $orderGroup->group_order_sn = $this->getNewOrderId() . '0';
            $orderGroup->save();
            $this->dao->update($id, $data);
            app()->make(StoreOrderStatusRepository::class)->status($id, 'change', '订单信息修改');
            if ($data['pay_price'] != $oldOrder['pay_price']) {
                Queue::push(SendSmsJob::class, [
                    'tempId' => 'PRICE_REVISION_CODE',
                    'id' => $id
                ]);
            }
        });
    }

    /**
     * @param $id
     * @param $uid
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/12
     */
    public function refundProduct($id, $uid)
    {
        $order = $this->dao->userOrder($id, $uid);
        if (!$order)
            throw new ValidateException('订单不存在');
        if (!count($order->refundProduct))
            throw new ValidateException('没有可退款商品');
        return $order->refundProduct->toArray();
    }

    public function delivery($id, $data)
    {
        $data['status'] = 1;
        $order = $this->dao->get($id);
        if ($data['delivery_type'] == 1) {
            $exprss = app()->make(ExpressRepository::class)->getWhere(['id' => $data['delivery_name']]);
            if (!$exprss) throw new ValidateException('快递公司不存在');
            $data['delivery_name'] = $exprss['name'];
        }
        if ($data['delivery_type'] == 2 && !preg_match("/^1[3456789]{1}\d{9}$/", $data['delivery_id']))
            throw new ValidateException('手机号格式错误');
        $this->dao->update($id, $data);

        if ($data['delivery_type'] == 1) {
            app()->make(StoreOrderStatusRepository::class)->status($id, 'delivery_0', '订单以配送【快递名称】:' . $data['delivery_name'] . '; 【快递单号】：' . $data['delivery_id']);
            queue::push(SendTemplateMessageJob::class, [
                'tempCode' => 'ORDER_POSTAGE_SUCCESS',
                'id' => $order['order_id'],
            ]);
            Queue::push(SendSmsJob::class, [
                'tempId' => 'DELIVER_GOODS_CODE',
                'id' => $order['order_id']
            ]);
        }

        if ($data['delivery_type'] == 2) {
            app()->make(StoreOrderStatusRepository::class)->status($id, 'delivery_1', '订单以配送【送货人姓名】:' . $data['delivery_name'] . '; 【手机号】：' . $data['delivery_id']);
            queue::push(SendTemplateMessageJob::class, [
                'tempCode' => 'ORDER_DELIVER_SUCCESS',
                'id' => $order['order_id'],
            ]);
            Queue::push(SendSmsJob::class, [
                'tempId' => 'DELIVER_GOODS_CODE',
                'id' => $order['order_id']
            ]);
        }
        if ($data['delivery_type'] == 3) {
            app()->make(StoreOrderStatusRepository::class)->status($id, 'delivery_2', '订单以配送【虚拟发货】');
        }
    }

    public function getOne($id, ?int $merId)
    {
        $where = [$this->getPk() => $id];
        if ($merId) {
            $whre['mer_id'] = $merId;
            $whre['is_system_del'] = 0;
        }
        return $this->dao->getWhere($where, '*', ['user' => function ($query) {
            $query->field('uid,real_name,nickname');
        }]);
    }

    public function getOrderStatus($id, $page, $limit)
    {
        return app()->make(StoreOrderStatusRepository::class)->search($id, $page, $limit);
    }

    public function remarkForm($id)
    {
        $data = $this->dao->get($id);
        $form = Elm::createForm(Route::buildUrl('merchantStoreOrderRemark', ['id' => $id])->build());
        $form->setRule([
            Elm::text('remark', '备注', $data['remark'])->required(),
        ]);
        return $form->setTitle('修改备注');
    }

    public function adminMarkForm($id)
    {
        $data = $this->dao->get($id);
        $form = Elm::createForm(Route::buildUrl('systemMerchantOrderMark', ['id' => $id])->build());
        $form->setRule([
            Elm::text('admin_mark', '备注', $data['admin_mark'])->required(),
        ]);
        return $form->setTitle('修改备注');
    }

    /**
     * TODO
     * @param $where
     * @param $page
     * @param $limit
     * @return array
     * @author Qinii
     * @day 2020-06-15
     */
    public function adminMerGetList($where, $page, $limit)
    {
        $where['paid'] = 1;
        $query = $this->dao->search($where, null);
        $count = $query->count();
        $list = $query->with(['orderProduct', 'merchant' => function ($query) {
            return $query->field('mer_id,mer_name,is_trader');
        }])->page($page, $limit)->select();

        return compact('count', 'list');
    }

    public function reconList($where, $page, $limit)
    {
        $ids = app()->make(MerchantReconciliationOrderRepository::class)->getIds($where);
        $query = $this->dao->search([], null)->whereIn('order_id', $ids);
        $count = $query->count();
        $list = $query->with(['orderProduct'])->page($page, $limit)->select();

        return compact('count', 'list');
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @author Qinii
     */
    public function merchantGetList(array $where, $page, $limit)
    {
        $status = $where['status'];
        unset($where['status']);
        $query = $this->dao->search($where)->where($this->getOrderType($status))
            ->with(['orderProduct', 'merchant' => function ($query) {
                return $query->field('mer_id,mer_name');
            }, 'verifyService' => function ($query) {
                return $query->field('service_id,nickname');
            }]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();

        $productId = $this->dao->search($where)->where($this->getOrderType($status))->column('order_id');
        $make = app()->make(StoreRefundOrderRepository::class);
        $orderRefund = $make->refundPirceByOrder($productId);
        $all = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->count();
        $countPay = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->field('sum(pay_price) as pay_price')->find();
        $banclPay = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->where('pay_type', 0)->field('sum(pay_price) as pay_price')->find();
        $wechatpay = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->where('pay_type', '<>', 0)->field('sum(pay_price) as pay_price')->find();

        $stat = [
            [
                'className' => 'el-icon-s-goods',
                'count' => $all,
                'field' => '件',
                'name' => '已支付订单数量'
            ],
            [
                'className' => 'el-icon-s-order',
                'count' => $countPay['pay_price'] ? $countPay['pay_price'] : 0,
                'field' => '元',
                'name' => '实际支付金额'
            ],
            [
                'className' => 'el-icon-s-cooperation',
                'count' => $orderRefund ? $orderRefund : 0,
                'field' => '元',
                'name' => '已退款金额'
            ],
            [
                'className' => 'el-icon-s-cooperation',
                'count' => $wechatpay['pay_price'] ? $wechatpay['pay_price'] : 0,
                'field' => '元',
                'name' => '微信支付金额'
            ],
            [
                'className' => 'el-icon-s-finance',
                'count' => $banclPay['pay_price'] ? $banclPay['pay_price'] : 0,
                'field' => '元',
                'name' => '余额支付金额'
            ],
        ];
        return compact('count', 'list', 'stat');
    }

    public function adminGetList(array $where, $page, $limit)
    {
        $status = $where['status'];
        unset($where['status']);
        $query = $this->dao->search($where, null)->where($this->getOrderType($status))
            ->with(['orderProduct', 'merchant' => function ($query) {
                return $query->field('mer_id,mer_name,is_trader');
            }, 'verifyService' => function ($query) {
                return $query->field('service_id,nickname');
            }]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();

        $productId = $this->dao->search($where)->where($this->getOrderType($status))->column('order_id');
        $make = app()->make(StoreRefundOrderRepository::class);
        $orderRefund = $make->refundPirceByOrder($productId);
        $all = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->count();
        $countPay = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->field('sum(pay_price) as pay_price')->find();
        $banclPay = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->where('pay_type', 0)->field('sum(pay_price) as pay_price')->find();
        $wechatpay = $this->dao->search($where)->where($this->getOrderType($status))->where('paid', 1)->where('pay_type', '<>', 0)->field('sum(pay_price) as pay_price')->find();

        $stat = [
            [
                'className' => 'el-icon-s-goods',
                'count' => $all,
                'field' => '件',
                'name' => '已支付订单数量'
            ],
            [
                'className' => 'el-icon-s-order',
                'count' => $countPay['pay_price'] ? $countPay['pay_price'] : 0,
                'field' => '元',
                'name' => '实际支付金额'
            ],
            [
                'className' => 'el-icon-s-cooperation',
                'count' => $orderRefund ? $orderRefund : 0,
                'field' => '元',
                'name' => '已退款金额'
            ],
            [
                'className' => 'el-icon-s-cooperation',
                'count' => $wechatpay['pay_price'] ? $wechatpay['pay_price'] : 0,
                'field' => '元',
                'name' => '微信支付金额'
            ],
            [
                'className' => 'el-icon-s-finance',
                'count' => $banclPay['pay_price'] ? $banclPay['pay_price'] : 0,
                'field' => '元',
                'name' => '余额支付金额'
            ],
        ];
        return compact('count', 'list', 'stat');
    }

    /**
     * @param array $where
     * @param $page
     * @param $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/10
     */
    public function getList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where)->where('is_del', 0);
        $count = $query->count();
        $list = $query->with(['orderProduct', 'merchant' => function ($query) {
            return $query->field('mer_id,mer_name');
        }])->page($page, $limit)->order('pay_time DESC')->select();

        return compact('list', 'count');
    }

    public function userList($uid, $page, $limit)
    {
        $query = $this->dao->search([
            'uid' => $uid,
            'paid' => 1
        ]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        return compact('count', 'list');
    }

    public function express($orderId)
    {
        $product = $this->dao->get($orderId);
        return ExpressService::express($product['delivery_id']);
    }

    public function checkPrinterConfig(int $merId)
    {
        if (!merchantConfig($merId, 'printing_status'))
            throw new ValidateException('打印功能未开启');
        $config = [
            'clientId' => merchantConfig($merId, 'printing_client_id'),
            'apiKey' => merchantConfig($merId, 'printing_api_key'),
            'partner' => merchantConfig($merId, 'develop_id'),
            'terminal' => merchantConfig($merId, 'terminal_number')
        ];
        if (!$config['clientId'] || !$config['apiKey'] || !$config['partner'] || !$config['terminal'])
            throw new ValidateException('打印机配置错误');
        return $config;
    }

    /**
     * TODO 打印机
     * @param int $id
     * @param int $merId
     * @return bool|mixed|string
     * @author Qinii
     * @day 2020-07-30
     */
    public function printer(int $id, int $merId)
    {
        $res = $this->dao->getWhere(['order_id' => $id], '*', ['orderProduct', 'merchant' => function ($query) {
            $query->field('mer_id,mer_name');
        }]);
        foreach ($res['orderProduct'] as $item) {
            $product[] = [
                'store_name' => $item['cart_info']['product']['store_name'],
                'product_num' => $item['product_num'],
                'price' => $item['product_price'],
                'product_price' => bcmul($item['product_price'], $item['product_num'], 2)
            ];
        }
        $data = [
            'order_sn' => $res['order_sn'],
            'pay_time' => $res['pay_time'],
            'real_name' => $res['real_name'],
            'user_phone' => $res['user_phone'],
            'user_address' => $res['user_address'],
            'total_price' => $res['total_price'],
            'coupon_price' => $res['coupon_price'],
            'pay_price' => $res['pay_price'],
            'total_postage' => $res['total_postage'],
            'pay_postage' => $res['pay_postage'],
            'mark' => $res['mark'],
        ];

        $printer = new Printer('yi_lian_yun', $this->checkPrinterConfig($merId));
        return $res = $printer->setPrinterContent([
            'name' => $res['merchant']['mer_name'],
            'orderInfo' => $data,
            'product' => $product
        ])->startPrinter();
    }

    public function verifyOrder($id, $merId, $serviceId)
    {
        $order = $this->dao->getWhere(['verify_code' => $id, 'mer_id' => $merId]);
        if (!$order)
            throw new ValidateException('订单不存在');
        if ($order->status != 0)
            throw new ValidateException('订单已核销');
        if (!$order->paid)
            throw new ValidateException('订单未支付');
        $order->status = 2;
        $order->verify_time = date('Y-m-d H:i:s');
        $order->verify_service_id = $serviceId;
        Db::transaction(function () use ($order) {
            $this->takeAfter($order, $order->user);
            $order->save();
        });
    }

    public function wxQrcode($orderId, $verify_code)
    {
//        $siteUrl = systemConfig('site_url');
        $name = md5('owx' . $orderId . date('Ymd')) . '.jpg';
        $attachmentRepository = app()->make(AttachmentRepository::class);
        $imageInfo = $attachmentRepository->getWhere(['attachment_name' => $name]);

        if (isset($imageInfo['attachment_src']) && strstr($imageInfo['attachment_src'], 'http') !== false && curl_file_exist($imageInfo['attachment_src']) === false) {
            $imageInfo->delete();
            $imageInfo = null;
        }
        if (!$imageInfo) {
//            $codeUrl = set_http_type(rtrim($siteUrl, '/') . '/pages/admin/order_cancellation/index?verify_code=' . $verify_code, request()->isSsl() ? 0 : 1);//二维码链接
            $imageInfo = app()->make(QrcodeService::class)->getQRCodePath($verify_code, $name);
            if (is_string($imageInfo)) throw new ValidateException('二维码生成失败');

            $imageInfo['dir'] = path_to_url($imageInfo['dir']);
            if (strpos($imageInfo['dir'], 'http') === false) $imageInfo['dir'] = request()->domain() . $imageInfo['dir'];

            $attachmentRepository->create(systemConfig('upload_type') ?: 1, -2, $orderId, [
                'attachment_category_id' => 0,
                'attachment_name' => $imageInfo['name'],
                'attachment_src' => $imageInfo['dir']
            ]);
            $urlCode = $imageInfo['dir'];
        } else $urlCode = $imageInfo['attachment_src'];
        return $urlCode;
    }

    public function routineQrcode($orderId, $verify_code)
    {
        $name = md5('ort' . $orderId . date('Ymd')) . '.jpg';
        $attachmentRepository = app()->make(AttachmentRepository::class);
        $imageInfo = $attachmentRepository->getWhere(['attachment_name' => $name]);

        if (isset($imageInfo['attachment_src']) && strstr($imageInfo['attachment_src'], 'http') !== false && curl_file_exist($imageInfo['attachment_src']) === false) {
            $imageInfo->delete();
            $imageInfo = null;
        }
        if (!$imageInfo) {
            $uploadType = (int)systemConfig('upload_type') ?: 1;
            $routineQrcodeRepository = app()->make(RoutineQrcodeRepository::class);
            $res = $routineQrcodeRepository->getShareCode($orderId, 'store', '/pages/admin/order_cancellation/index?verify_code=' . $verify_code, '');
            if (!$res) throw new ValidateException('二维码生成失败');
            $upload = UploadService::create($uploadType);
            $uploadRes = $upload->to('routine/spread/code')->validate()->stream($res['res'], $name);
            if ($uploadRes === false) {
                throw new ValidateException($upload->getError());
            }
            $imageInfo = $upload->getUploadInfo();
            $imageInfo['image_type'] = $uploadType;

            $imageInfo['dir'] = path_to_url($imageInfo['dir']);
            if (strpos($imageInfo['dir'], 'http') === false) $imageInfo['dir'] = setHttpType(request()->domain() . $imageInfo['dir']);

            $attachmentRepository->create($uploadType, -2, $orderId, [
                'attachment_category_id' => 0,
                'attachment_name' => $imageInfo['name'],
                'attachment_src' => $imageInfo['dir']
            ]);
            $routineQrcodeRepository->setRoutineQrcodeFind($res['id'], ['status' => 1, 'url_time' => date('Y-m-d H:i:s'), 'qrcode_url' => $imageInfo['dir']]);
            $urlCode = $imageInfo['dir'];
        } else $urlCode = $imageInfo['attachment_src'];
        return $urlCode;
    }

    /**
     * TODO 根据商品ID获取订单数
     * @param int $productId
     * @return int
     * @author Qinii
     * @day 2020-08-05
     */
    public function seckillOrderCounut(int $productId)
    {
        $where = ['day' => date('Y-m-d', time()), 'paid' => 1];
        return $this->dao->getOrderByProductId($productId, $where)->count();
    }

    /**
     * TODO 根据商品sku获取订单数
     * @param int $productId
     * @return int
     * @author Qinii
     * @day 2020-08-05
     */
    public function seckillSkuOrderCounut(string $sku)
    {
        $where = ['day' => date('Y-m-d', time()), 'paid' => 1];
        return $this->dao->getOrderByProductSku($sku, $where)->count();
    }

    /**
     * TODO 获取个人当天限购
     * @param int $uid
     * @param int $productId
     * @return int
     * @author Qinii
     * @day 2020-08-15
     */
    public function getDayPayCount(int $uid, int $productId)
    {
        $where = ['day' => date('Y-m-d', time()), 'paid' => 1, 'uid' => $uid];
        $make = app()->make(StoreSeckillActiveRepository::class);
        $active = $make->getWhere(['product_id' => $productId]);
        if ($active['once_pay_count'] == 0) return true;
        $count = $this->dao->getOrderByProductId($productId, $where)->count();
        return ($active['once_pay_count'] > $count);
    }

    /**
     * TODO 获取个人总限购
     * @param int $uid
     * @param int $productId
     * @return int
     * @author Qinii
     * @day 2020-08-15
     */
    public function getPayCount(int $uid, int $productId)
    {
        $where = ['paid' => 1, 'uid' => $uid];
        $make = app()->make(StoreSeckillActiveRepository::class);
        $active = $make->getWhere(['product_id' => $productId]);
        if ($active['all_pay_count'] == 0) return true;
        $count = $this->dao->getOrderByProductId($productId, $where)->count();
        return ($active['all_pay_count'] > $count);
    }

    /**
     *  根据订单id查看是否全部退款
     * @Author:Qinii
     * @Date: 2020/9/11
     * @param int $orderId
     * @return bool
     */
    public function checkRefundStatusById(int $orderId,int $refundId)
    {
        Db::transaction(function () use ($orderId,$refundId) {
            $res = $this->dao->search(['order_id' => $orderId])->with(['orderProduct'])->find();
            $refund = app()->make(StoreRefundOrderRepository::class)->getRefundCount($orderId,$refundId);
            if ($refund) return false;
            foreach ($res['orderProduct'] as $item) {
                if ($item['refund_num'] !== 0) return false;
                $item->is_refund = 3;
                $item->save();
            }
            $res->status = -1;
            $res->save();
            app()->make(StoreOrderStatusRepository::class)->status($orderId, 'refund_all', '订单已全部退款');
        });
    }

    /**
     * @param $id
     * @param $uid
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/9/17
     */
    public function userDel($id, $uid)
    {
        $order = $this->dao->getWhere([['status', 'in', [0, 3, -1]], ['order_id', '=', $id], ['uid', '=', $uid], ['is_del', '=', 0]]);
        if (!$order || ($order->status == 0 && $order->paid == 1))
            throw new ValidateException('订单状态有误');
        Db::transaction(function () use ($id, $order) {
            $order->is_del = 1;
            $order->save();
            app()->make(StoreOrderStatusRepository::class)->status($id, 'delete', '订单删除');
        });
    }
}
