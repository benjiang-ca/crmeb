<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/9/9
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\store\order\StoreOrderRepository;
use app\common\repositories\store\order\StoreOrderStatusRepository;
use app\common\repositories\store\product\ProductReplyRepository;
use crmeb\interfaces\JobInterface;
use think\facade\Db;
use think\facade\Log;
use think\facade\Queue;

class OrderReplyJob implements JobInterface
{

    public function fire($job, $orderId)
    {
        $storeOrderRepository = app()->make(StoreOrderRepository::class);
        $productReplyRepository = app()->make(ProductReplyRepository::class);
        $order = $storeOrderRepository->getWhere(['order_id' => $orderId, 'status' => 2]);
        if ($order) {
            $data = ['comment' => '系统默认好评', 'product_score' => 5, 'service_score' => 5, 'postage_score' => 5, 'rate' => 5];
            $data['uid'] = $order->uid;
            $data['nickname'] = $order->user['nickname'];
            $data['avatar'] = $order->user['avatar'];
            $data['mer_id'] = $order->mer_id;
            $ids = [];
            try {
                Db::transaction(function () use ($productReplyRepository, $order, &$ids, $data) {
                    foreach ($order->orderProduct as $orderProduct) {
                        if ($orderProduct->is_reply) continue;
                        $data['order_product_id'] = $orderProduct['order_product_id'];
                        $data['product_type'] = $orderProduct['cart_info']['product']['product_type']??0;
                        $ids[] = $data['product_id'] = $orderProduct['product_id'];
                        $data['unique'] = $orderProduct['cart_info']['productAttr']['unique'];
                        $productReplyRepository->create($data);
                        $orderProduct->is_reply = 1;
                        $orderProduct->save();
                    }
                    $order->status = 3;
                    $order->save();
                    //TODO 交易完成
                    app()->make(StoreOrderStatusRepository::class)->status($order->order_id, 'auto_over', '交易完成');
                });
                foreach ($ids as $id) {
                    Queue::push(UpdateProductReplyJob::class, $id);
                }
            } catch (\Exception $e) {
                Log::error($orderId . '自动评价商品失败' . $e->getMessage());
            }
        }
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}