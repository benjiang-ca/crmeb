<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/6/11
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\jobs;


use app\common\repositories\store\product\ProductReplyRepository;
use app\common\repositories\store\product\ProductRepository;
use app\common\repositories\system\merchant\MerchantRepository;
use crmeb\interfaces\JobInterface;

class UpdateProductReplyJob implements JobInterface
{

    public function fire($job, $productId)
    {
        $productReplyRepository = app()->make(ProductReplyRepository::class);

        $total = $productReplyRepository->productTotalRate($productId);
        if (!$total) return $job->delete();
        $rate = bcdiv($total['total_rate'], $total['total_count'], 1);
        app()->make(ProductRepository::class)->update($productId, [
            'rate' => $rate,
            'reply_count' => $total['total_count']
        ]);
        $data = $productReplyRepository->getWhere(['product_id' => $productId], 'mer_id');
        $merchantRate = $productReplyRepository->merchantTotalRate($data['mer_id']);
        app()->make(MerchantRepository::class)->update($data['mer_id'], $merchantRate);
        $job->delete();
    }

    public function failed($data)
    {
        // TODO: Implement failed() method.
    }
}
