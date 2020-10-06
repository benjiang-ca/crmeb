<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/12
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\store\order;


use app\common\dao\store\order\StoreRefundOrderDao;
use app\common\model\store\order\StoreOrder;
use app\common\model\store\order\StoreRefundOrder;
use app\common\model\store\order\StoreRefundProduct;
use app\common\repositories\BaseRepository;
use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\system\merchant\FinancialRecordRepository;
use app\common\repositories\user\UserBillRepository;
use app\common\repositories\user\UserRepository;
use app\controller\merchant\store\order\RefundOrder;
use crmeb\jobs\SendSmsJob;
use crmeb\jobs\SendTemplateMessageJob;
use crmeb\services\ExpressService;
use crmeb\services\SwooleTaskService;
use Exception;
use FormBuilder\Factory\Elm;
use FormBuilder\Form;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Queue;
use think\facade\Route;
use crmeb\services\WechatService;
use crmeb\services\MiniProgramService;
use think\Model;

/**
 * Class StoreRefundOrderRepository
 * @package app\common\repositories\store\order
 * @author xaboy
 * @day 2020/6/12
 * @mixin StoreRefundOrderDao
 */
class StoreRefundOrderRepository extends BaseRepository
{
    /**
     * StoreRefundOrderRepository constructor.
     * @param StoreRefundOrderDao $dao
     */
    public function __construct(StoreRefundOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $products
     * @return int|string
     * @author xaboy
     * @day 2020/6/12
     */
    public function getRefundTotalPrice($products)
    {
        $totalRefundPrice = 0;
        foreach ($products as $product) {
            $totalRefundPrice = bcadd(bcmul(bcdiv($product['product_price'], $product['product_num'], 2), $product['refund_num'], 2), $totalRefundPrice, 2);
        }

        return $totalRefundPrice;
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
     * @day 2020/6/12
     */
    public function userList(array $where, $page, $limit)
    {
        $query = $this->dao->search($where);
        $count = $query->count();
        $list = $query->setOption('field', [])->field('refund_order_id,refund_price,mer_id,status')
            ->with(['merchant' => function ($query) {
                $query->field('mer_name,mer_id');
            }, 'refundProduct.product'])->page($page, $limit)->select();
        return compact('list', 'count');
    }

    /**
     * @param $id
     * @param $uid
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/12
     */
    public function userDetail($id, $uid)
    {
        return $this->dao->search([
            'id' => $id,
            'uid' => $uid,
            'is_del' => 0,
        ])->with('refundProduct.product')->append(['auto_refund_time'])->find();
    }

    /**
     * @param $id
     * @param $uid
     * @author xaboy
     * @day 2020/6/17
     */
    public function userDel($id, $uid)
    {
        Db::transaction(function () use ($uid, $id) {
            $this->dao->userDel($uid, $id);
            app()->make(StoreRefundStatusRepository::class)->status($id, 'delete', '删除记录');
        });
    }

    /**
     * @param StoreOrder $order
     * @param array $ids
     * @param $uid
     * @param array $data
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/17
     */
    public function refunds(StoreOrder $order, array $ids, $uid, array $data)
    {
        $orderId = $order->order_id;
        $products = app()->make(StoreOrderProductRepository::class)->userRefundProducts($ids, $uid, $orderId);
        if (!$products || count($ids) != count($products))
            throw new ValidateException('请选择正确的退款商品');
        $totalRefundPrice = !$order->status ? $order->pay_postage : 0;
        $totalRefundNum = 0;
        $total_extension_one = 0;
        $total_extension_two = 0;
        foreach ($products as $product) {
            if ($product['extension_one'] > 0)
                $total_extension_one = bcadd($total_extension_one, bcmul($product['refund_num'], $product['extension_one'], 2), 2);
            if ($product['extension_two'] > 0)
                $total_extension_two = bcadd($total_extension_two, bcmul($product['refund_num'], $product['extension_two'], 2), 2);
            $totalRefundNum += $product['refund_num'];
            if ($product['product_price'] > 0)
                $totalRefundPrice = bcadd(bcmul(bcdiv($product['product_price'], $product['product_num'], 2), $product['refund_num'], 2), $totalRefundPrice, 2);
        }
        if ($totalRefundPrice < $data['refund_price'])
            throw new ValidateException('最高可退款' . floatval($totalRefundPrice) . '元');
        $data['order_id'] = $products[0]['order_id'];
        $data['uid'] = $products[0]['uid'];
        $data['mer_id'] = app()->make(StoreOrderRepository::class)->getMerId($data['order_id']);
        $data['refund_order_sn'] = $this->getNewOrderId();
        $data['refund_num'] = $totalRefundNum;
        $userBillRepository = app()->make(UserBillRepository::class);
        $data['extension_one'] = $userBillRepository->existsWhere(['link_id' => $data['order_id'], 'category' => 'brokerage', 'type' => 'order_one', 'status' => 0]) ? $total_extension_one : 0;
        $data['extension_two'] = $userBillRepository->existsWhere(['link_id' => $data['order_id'], 'category' => 'brokerage', 'type' => 'order_two', 'status' => 0]) ? $total_extension_two : 0;
        return Db::transaction(function () use ($data, $products, $order) {
            $refund = $this->dao->create($data);
            $refundProduct = [];
            foreach ($products as $product) {
                $refundProduct[] = [
                    'refund_num' => $product['refund_num'],
                    'refund_order_id' => $refund->refund_order_id,
                    'order_product_id' => $product['order_product_id']
                ];
                $product->refund_num = 0;
                $product->is_refund = 1;
                $product->save();
            }
            app()->make(StoreRefundStatusRepository::class)->status($refund->refund_order_id, 'create', '创建批量退款单');
            app()->make(StoreRefundProductRepository::class)->insertAll($refundProduct);
            $this->applyRefundAfter($refund, $order);
            return $refund;
        });
    }

    public function applyRefundAfter($refund, $order)
    {
        $tempData = ['tempCode' => 'ORDER_REFUND_STATUS', 'id' => $refund->refund_order_id];
        Queue::push(SendTemplateMessageJob::class, $tempData);
        Queue::push(SendSmsJob::class, [
            'tempId' => 'ADMIN_RETURN_GOODS_CODE',
            'id' => $refund->refund_order_id
        ]);
        SwooleTaskService::merchant('notice', [
            'type' => 'new_refund_order',
            'data' => [
                'title' => '新退款单',
                'message' => '您有一个新的退款单',
                'id' => $refund->refund_order_id
            ]
        ], $order->mer_id);
    }

    /**
     * @param StoreOrder $order
     * @param $productId
     * @param $num
     * @param $uid
     * @param array $data
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/17
     */
    public function refund(StoreOrder $order, $productId, $num, $uid, array $data)
    {
        $orderId = $order->order_id;
        //TODO 订单状态生成佣金
        $product = app()->make(StoreOrderProductRepository::class)->userRefundProducts([$productId], $uid, $orderId);
        if (!$product)
            throw new ValidateException('请选择正确的退款商品');
        $product = $product[0];
        if ($product['refund_num'] < $num)
            throw new ValidateException('可退款商品不足' . floatval($num) . '件');
        $refundPrice = !$order->status ? $order->pay_postage : 0;
        if ($product['product_price'] > 0)
            $refundPrice = bcadd($refundPrice, bcmul(bcdiv($product['product_price'], $product['product_num'], 2), $num, 2), 2);
        if ($refundPrice < $data['refund_price'])
            throw new ValidateException('最高可退款' . floatval($refundPrice) . '元');
        $data['order_id'] = $product['order_id'];

        $total_extension_one = 0;
        $total_extension_two = 0;
        if ($order->status >= 2) {
            if ($product['extension_one'] > 0)
                $total_extension_one = bcmul($num, $product['extension_one'], 2);
            if ($product['extension_two'] > 0)
                $total_extension_two = bcmul($num, $product['extension_two'], 2);
        }

        $data['uid'] = $product['uid'];
        $data['mer_id'] = app()->make(StoreOrderRepository::class)->getMerId($data['order_id']);
        $data['refund_order_sn'] = $this->getNewOrderId();
        $data['refund_num'] = $num;
        $data['extension_one'] = $total_extension_one;
        $data['extension_two'] = $total_extension_two;
        return Db::transaction(function () use ($order, $data, $product, $productId, $num) {
            $refund = $this->dao->create($data);
            app()->make(StoreRefundProductRepository::class)->create([
                'refund_num' => $num,
                'refund_order_id' => $refund->refund_order_id,
                'order_product_id' => $productId
            ]);
            $product->refund_num -= $num;
            $product->is_refund = 1;
            $product->save();
            app()->make(StoreRefundStatusRepository::class)->status($refund->refund_order_id, 'create', '创建退款单');
            $this->applyRefundAfter($refund, $order);
            return $refund;
        });
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
        $orderId = 'rwx' . $msectime . mt_rand(10000, max(intval($msec * 10000) + 10000, 98369));
        return $orderId;
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-06-12
     */
    public function getList(array $where, int $page, int $limit)
    {
        $query = $this->dao->search($where)->where('is_system_del', 0)->with(['order' => function ($query) {
            $query->field('order_id,order_sn');
        }, 'refundProduct.product', 'user' => function ($query) {
            $query->field('uid,nickname,phone');
        }])->order('create_time DESC');
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        $stat = [
            'count' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id']]),
            'audit' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 0]),
            'refuse' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => -1]),
            'agree' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 1]),
            'backgood' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 2]),
            'end' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 3]),
        ];
        return compact('count', 'list', 'stat');
    }


    public function getAdminList(array $where, int $page, int $limit)
    {
        $query = $this->dao->search($where)->with(['order' => function ($query) {
            $query->field('order_id,order_sn');
        }, 'refundProduct.product', 'user' => function ($query) {
            $query->field('uid,nickname,phone');
        }]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        $stat = [
            'count' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id']]),
            'audit' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 0]),
            'refuse' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => -1]),
            'agree' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 1]),
            'backgood' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 2]),
            'end' => $this->dao->getWhereCount(['is_system_del' => 0, 'mer_id' => $where['mer_id'], 'status' => 3]),
        ];
        return compact('count', 'list', 'stat');
    }


    /**
     * TODO 总后台所有订单
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @author Qinii
     * @day 2020-06-25
     */
    public function getAllList(array $where, int $page, int $limit)
    {
        $query = $this->dao->search($where)->with(['order' => function ($query) {
            $query->field('order_id,order_sn');
        }, 'merchant' => function ($query) {
            $query->field('mer_id,mer_name,is_trader');
        }, 'refundProduct.product', 'user' => function ($query) {
            $query->field('uid,nickname,phone');
        }]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();
        $stat = [
            'all' => $this->dao->getWhereCount([]),
            'audit' => $this->dao->getWhereCount(['status' => 0]),
            'refuse' => $this->dao->getWhereCount(['status' => -1]),
            'agree' => $this->dao->getWhereCount(['status' => 1]),
            'backgood' => $this->dao->getWhereCount(['status' => 2]),
            'end' => $this->dao->getWhereCount(['status' => 3]),
        ];
        return compact('count', 'list', 'stat');
    }

    public function reconList($where, $page, $limit)
    {
        $ids = app()->make(MerchantReconciliationOrderRepository::class)->getIds($where);
        $query = $this->dao->search([])->whereIn('refund_order_id', $ids)->with(['order' => function ($query) {
            $query->field('order_id,order_sn');
        }, 'refundProduct.product', 'user' => function ($query) {
            $query->field('uid,nickname,phone');
        }]);
        $count = $query->count();
        $list = $query->page($page, $limit)->select();

        return compact('count', 'list');
    }

    /**
     * @param int $merId
     * @param int $id
     * @return bool
     * @author Qinii
     * @day 2020-06-12
     */
    public function getStatusExists(int $merId, int $id)
    {
        $where = [
            'mer_id' => $merId,
            'refund_order_id' => $id,
            'status' => 0,
        ];
        return $this->dao->getFieldExists($where);
    }

    /**
     * TODO 是否为收到退货状态
     * @param int $merId
     * @param int $id
     * @return bool
     * @author Qinii
     * @day 2020-06-13
     */
    public function getRefundPriceExists(int $merId, int $id)
    {
        $where = [
            'mer_id' => $merId,
            'refund_order_id' => $id,
            'status' => 2,
        ];
        return $this->dao->getFieldExists($where);
    }

    public function getUserDelExists(int $merId, int $id)
    {
        $where = [
            'mer_id' => $merId,
            'refund_order_id' => $id,
            'is_del' => 1,
        ];
        return $this->dao->getFieldExists($where);
    }

    /**
     * @param int $merId
     * @param int $id
     * @return bool
     * @author Qinii
     * @day 2020-06-12
     */
    public function getExistsById(int $merId, int $id)
    {
        $where = [
            'mer_id' => $merId,
            'refund_order_id' => $id,
        ];
        return $this->dao->getFieldExists($where);
    }

    public function markForm($id)
    {
        $data = $this->dao->get($id);
        $form = Elm::createForm(Route::buildUrl('merchantStoreRefundMark', ['id' => $id])->build());
        $form->setRule([
            Elm::text('mer_mark', '备注', $data['mer_mark'])
        ]);

        return $form->setTitle('备注信息');
    }

    public function adminMarkForm($id)
    {
        $data = $this->dao->get($id);
        $form = Elm::createForm(Route::buildUrl('systemMerchantRefundOrderMark', ['id' => $id])->build());
        $form->setRule([
            Elm::text('admin_mark', '备注', $data['admin_mark'])
        ]);

        return $form->setTitle('备注信息');
    }

    /**
     * TODO 退款单已发货
     * @param $id
     * @return Form
     * @author Qinii
     * @day 2020-06-13
     */

    public function backGoods($uid, $id, $data)
    {
        $refund = $this->userDetail($id, $uid);
        if (!$refund)
            throw new ValidateException('退款单不存在');
        if ($refund->status != 1)
            throw new ValidateException('退款单状态有误');
        $refund->status = 2;
        $refund->status_time = date('Y-m-d H:i:s');
        Db::transaction(function () use ($refund, $data, $id) {
            $refund->save($data);
            app()->make(StoreRefundStatusRepository::class)->status($id, 'back_goods', '已发货');
        });
        Queue::push(SendSmsJob::class, [
            'tempId' => 'ADMIN_DELIVERY_CODE',
            'id' => $id
        ]);
    }

    /**
     * TODO
     * @param $id
     * @return Form
     * @author Qinii
     * @day 2020-06-13
     */
    public function statusForm($id)
    {
        $res = $this->getWhere(['refund_order_id' => $id]);
        $form = Elm::createForm(Route::buildUrl('merchantStoreRefundOrderSwitchStatus', ['id' => $id])->build());

        if ($res['refund_type'] == 1) {
            $form->setRule([
                Elm::radio('status', '审核', -1)->setOptions([
                    ['value' => 1, 'label' => '同意'],
                    ['value' => -1, 'label' => '拒绝'],
                ])->control([
                    [
                        'value' => -1,
                        'rule' => [
                            Elm::input('fail_message', '拒绝原因')->required()
                        ]
                    ],
                ]),
            ]);
        }
        if ($res['refund_type'] == 2) {
            $form->setRule([
                Elm::radio('status', '审核', -1)->setOptions([
                    ['value' => 1, 'label' => '同意'],
                    ['value' => -1, 'label' => '拒绝'],
                ])->control([
                    [
                        'value' => 1,
                        'rule' => [
                            Elm::input('mer_delivery_user', '收货人')->required(),
                            Elm::input('mer_delivery_address', '收货地址')->required(),
                            Elm::input('phone', '手机号')->required(),
                        ]
                    ],
                    [
                        'value' => -1,
                        'rule' => [
                            Elm::input('fail_message', '拒绝原因')->required()
                        ]
                    ],
                ]),

            ]);
        }

        return $form->setTitle('退款审核');
    }

    /**
     * TODO 拒绝退款
     * @param $id
     * @param $data
     * @author Qinii
     * @day 2020-06-13
     */
    public function refuse($id, $data)
    {
        Db::transaction(function () use ($id, $data) {
            $res = $this->getWhere(['refund_order_id' => $id], '*', ['refundProduct.product']);
            $data['status_time'] = date('Y-m-d H:i:s');
            $this->getProductRefundNumber($res, -1);
            $this->dao->update($id, $data);
            $tempData = ['tempCode' => 'ORDER_REFUND_NOTICE', 'id' => $id];
            Queue::push(SendTemplateMessageJob::class, $tempData);
            app()->make(StoreRefundStatusRepository::class)->status($id, 'refund_refuse', '订单退款已拒绝');
            Queue::push(SendSmsJob::class, [
                'tempId' => 'REFUND_FAIL_CODE',
                'id' => $id
            ]);
        });
    }


    /**
     * TODO 同意退款
     * @param $id
     * @param $data
     * @param $adminId
     * @author Qinii
     * @day 2020-06-13
     */
    public function agree(int $id, array $data, int $adminId)
    {
        $this->checkRefundPrice($id);

        Db::transaction(function () use ($id, $data, $adminId) {
            $res = $this->getWhere(['refund_order_id' => $id], '*', ['refundProduct.product']);
            $this->getProductRefundNumber($res, 1);
            if ($res['refund_type'] == 1) {
                //TODO 退款单同意退款
                $refund = $this->doRefundPrice($id, $adminId);
                $data['status'] = 3;
                app()->make(StoreRefundStatusRepository::class)->status($id, 'refund_price', '退款成功');
                $this->refundAfter($refund);
            }
            if ($res['refund_type'] == 2) {
                $data['status'] = 1;
                app()->make(StoreRefundStatusRepository::class)->status($id, 'refund_agree', '退款申请已通过，请将商品寄回');
                $tempData = ['tempCode' => 'ORDER_REFUND_NOTICE', 'id' => $id];
                Queue::push(SendTemplateMessageJob::class, $tempData);
                Queue::push(SendSmsJob::class, [
                    'tempId' => 'REFUND_SUCCESS_CODE',
                    'id' => $id
                ]);
            }
            $data['status_time'] = date('Y-m-d H:i:s');
            $this->dao->update($id, $data);
        });
    }

    /**
     * @Author:Qinii
     * @Date: 2020/8/29
     * @param $res
     * @param $status
     * @return bool
     */
    public function getProductRefundNumber($res, $status)
    {
        /**
         * 1.同意退款
         *   1.1 仅退款
         *      1.1.1 是 , 如果退款数量 等于 购买数量 is_refund = 3 全退退款 不等于 is_refund = 2 部分退款
         *      1.1.2 否, is_refund = 1 退款中
         *   1.2 退款退货 is_refund = 1
         *
         * 2. 拒绝退款
         *   2.1 如果退款数量 等于 购买数量 返还可退款数 is_refund = 0
         *   2.2 商品总数小于可退数量 返还可退数 以商品数为准
         *   2.3 是否存在其他图款单,是 ,退款中 ,否, 部分退款
         */
        $refundId = $this->getRefundCount($res->order_id,$res['refund_order_id']);
        $make = app()->make(StoreRefundProductRepository::class);
        foreach ($res['refundProduct'] as $item) {
            $is_refund = $item->product->is_refund;
            if ($status == 1) { //同意
                if($res['refund_type'] == 1){
                    $is_refund = ($item->refund_num == $item->product->product_num) ? 3 : 2;
                }
            } else {  //拒绝
                $refund_num = $item->refund_num + $item->product->refund_num; //返还可退款数
                if ($item->product->product_num == $refund_num) $is_refund = 0;
                if ($item->product->product_num < $refund_num) $refund_num = $item->product->product_num;
                $item->product->refund_num = $refund_num;
            }
            if (!empty($refundId)) {
                $has = $make->getWhereCount([['refund_order_id', 'in', $refundId], ['order_product_id', '=', $item->product->order_product_id]]);
                if ($has) $is_refund = 1;
            }
            $item->product->is_refund = $is_refund;
            $item->product->save();
        }
        return true;
    }

    /**
     * 获取订单存在的未处理完成的退款单
     * @Author:Qinii
     * @Date: 2020/9/25
     * @param int $orderId
     * @param int|null $refundOrderId
     * @return array
     */
    public function getRefundCount(int $orderId,?int $refundOrderId)
    {
        $where = [
            'type' => 1,
            'order_id' => $orderId,
        ];
        return $this->dao->search($where)->when($refundOrderId,function($query)use($refundOrderId){
            $query->where('refund_order_id','<>',$refundOrderId);
        })->column('refund_order_id');
    }

    /**
     * @param StoreRefundOrder $refundOrder
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020/6/17
     */
    public function descBrokerage(StoreRefundOrder $refundOrder)
    {
        $userBillRepository = app()->make(UserBillRepository::class);
        if ($refundOrder['extension_one'] > 0) {
            $bill = $userBillRepository->getWhere(['category' => 'brokerage', 'type' => 'order_one', 'link_id' => $refundOrder->order_id, 'status' => 0]);
            if ($bill) {
                $userBillRepository->decBill($bill->uid, 'brokerage', 'refund_one', [
                    'link_id' => $refundOrder->order_id,
                    'status' => 1,
                    'title' => '用户退款',
                    'number' => $refundOrder['extension_one'],
                    'mark' => '用户退款扣除推广佣金' . floatval($refundOrder['extension_one']),
                    'balance' => 0
                ]);
                $userRepository = app()->make(UserRepository::class);
                $userRepository->incBrokerage($bill->uid, $refundOrder['extension_one'], '-');
                app()->make(FinancialRecordRepository::class)->inc([
                    'order_id' => $refundOrder->refund_order_id,
                    'order_sn' => $refundOrder->refund_order_sn,
                    'user_info' => $userRepository->getUsername($bill->uid),
                    'user_id' => $bill->uid,
                    'financial_type' => 'refund_brokerage_one',
                    'number' => $refundOrder['extension_one'],
                ], $refundOrder->mer_id);
            }
        }
        if ($refundOrder['extension_two'] > 0) {
            $bill = $userBillRepository->getWhere(['category' => 'brokerage', 'type' => 'order_two', 'link_id' => $refundOrder->order_id, 'status' => 0]);
            if ($bill) {
                $userBillRepository->decBill($bill->uid, 'brokerage', 'refund_two', [
                    'link_id' => $refundOrder->order_id,
                    'status' => 1,
                    'title' => '用户退款',
                    'number' => $refundOrder['extension_two'],
                    'mark' => '用户退款扣除推广佣金' . floatval($refundOrder['extension_two']),
                    'balance' => 0
                ]);
                $userRepository = app()->make(UserRepository::class);
                $userRepository->incBrokerage($bill->uid, $refundOrder['extension_two'], '-');
                app()->make(FinancialRecordRepository::class)->inc([
                    'order_id' => $refundOrder->refund_order_id,
                    'order_sn' => $refundOrder->refund_order_sn,
                    'user_info' => $userRepository->getUsername($bill->uid),
                    'user_id' => $bill->uid,
                    'financial_type' => 'refund_brokerage_two',
                    'number' => $refundOrder['extension_two'],
                ], $refundOrder->mer_id);
            }
        }
    }

    /**
     * //TODO 退款后
     * @param StoreRefundOrder $refundOrder
     * @author xaboy
     * @day 2020/6/17
     */
    public function refundAfter(StoreRefundOrder $refundOrder)
    {
        //返还库存
        $refundOrder->append(['refundProduct.product']);
        $productRepository = app()->make(ProductRepository::class);
        if ($refundOrder['refund_type'] == 2) {
            foreach ($refundOrder->refundProduct as $item) {
                $productRepository->orderProductIncStock($item->product, $item->refund_num);
            }
        }
        app()->make(StoreOrderRepository::class)->checkRefundStatusById($refundOrder['order_id'], $refundOrder['refund_order_id']);
        $tempData = ['tempCode' => 'ORDER_REFUND_END', 'id' => $refundOrder->refund_order_id];
        Queue::push(SendTemplateMessageJob::class, $tempData);
        Queue::push(SendSmsJob::class, [
            'tempId' => 'REFUND_CONFORM_CODE',
            'id' => $refundOrder->refund_order_id
        ]);
        $this->descBrokerage($refundOrder);
        app()->make(FinancialRecordRepository::class)->dec([
            'order_id' => $refundOrder->refund_order_id,
            'order_sn' => $refundOrder->refund_order_sn,
            'user_info' => $refundOrder->user->nickname,
            'user_id' => $refundOrder->uid,
            'financial_type' => 'refund_order',
            'number' => $refundOrder->refund_price,
        ], $refundOrder->mer_id);
    }


    /**
     * TODO 退款单同意退款退货
     * @param $id
     * @param $admin
     * @author Qinii
     * @day 2020-06-13
     */
    public function adminRefund($id, $admin)
    {
        Db::transaction(function () use ($admin, $id) {
            $data['status'] = 3;
            $data['status_time'] = date('Y-m-d H:i:s');
            app()->make(StoreRefundStatusRepository::class)->status($id, 'refund_price', '退款成功' . ($admin ? '' : '[自动]'));
            $this->dao->update($id, $data);
            $refund = $this->doRefundPrice($id, $admin);
            $this->refundAfter($refund);
        });
    }

    /**
     * TODO 退款操作
     * @param $id
     * @param $adminId
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author Qinii
     * @day 2020-06-13
     */
    public function doRefundPrice($id, $adminId)
    {
        $res = $this->dao->getWhere(['refund_order_id' => $id], "*", ['order']);
        $refundData = [
            'refund_id' => $id,
            'pay_price' => $res->order->pay_price,
            'refund_price' => $res->refund_price
        ];
        //0余额 1微信 2小程序
        if ($res->order->pay_type == 0) {
            try {
                $user = app()->make(UserRepository::class)->get($res->uid);
                $balance = bcadd($user->now_money, $res->refund_price, 2);
                $user->save(['now_money' => $balance]);
                app()->make(UserBillRepository::class)->incBill($res->uid, 'now_money', 'refund', [
                    'link_id' => $id,
                    'status' => 1,
                    'title' => '退款增加余额',
                    'number' => $res->refund_price,
                    'mark' => '退款增加' . floatval($res->refund_price) . '余额，退款订单号:' . $res->refund_order_sn,
                    'balance' => $balance
                ]);
            } catch (Exception $e) {
                throw new ValidateException('余额退款失败');
            }
        }
        if ($res->order->pay_type == 1) {
            try {
                WechatService::create()->payOrderRefund($res->order->groupOrder->group_order_sn, $refundData);
            } catch (Exception $e) {
                throw new ValidateException($e->getMessage());
            }
        }
        if ($res->order->pay_type == 2) {
            try {
                MiniProgramService::create()->payOrderRefund($res->order->groupOrder->group_order_sn, $refundData);
            } catch (Exception $e) {
                throw new ValidateException($e->getMessage());
            }
        }
        return $res;
    }


    public function express($ordertId)
    {
        $product = $this->dao->get($ordertId);
        return ExpressService::express($product['delivery_id']);
    }

    /**
     *  退款金额是否超过可退金额
     * @Author:Qinii
     * @Date: 2020/9/2
     * @param int $refundId
     * @return bool
     */
    public function checkRefundPrice(int $refundId)
    {
        $refund = $this->get($refundId);
        $order = app()->make(StoreOrderRepository::class)->get($refund['order_id']);
        $refund_price = $this->dao->refundPirceByOrder([$refund['order_id']]);
        if (bccomp(bcsub($order['pay_price'], $refund_price, 2), $refund['refund_price'], 2) == -1) throw new ValidateException('退款金额超出订单可退金额');
        return true;
    }
}
