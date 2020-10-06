<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/5
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\merchant;


use app\common\dao\BaseDao;
use app\common\model\system\merchant\FinancialRecord;

class FinancialRecordDao extends BaseDao
{

    protected function getModel(): string
    {
        return FinancialRecord::class;
    }

    /**
     * @return string
     * @author xaboy
     * @day 2020/6/9
     */
    public function getSn()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = number_format((floatval($msec) + floatval($sec)) * 1000, 0, '', '');
        $orderId = 'jy' . $msectime . mt_rand(10000, max(intval($msec * 10000) + 10000, 98369));
        return $orderId;
    }

    public function inc(array $data, $merId)
    {
        $data['mer_id'] = $merId;
        $data['financial_pm'] = 1;
        $data['financial_record_sn'] = $this->getSn();
        return $this->create($data);
    }

    public function dec(array $data, $merId)
    {
        $data['mer_id'] = $merId;
        $data['financial_pm'] = 0;
        $data['financial_record_sn'] = $this->getSn();
        return $this->create($data);
    }

    public function search(array $where)
    {
        $query = $this->getModel()::getDB()
            ->when(isset($where['financial_type']) && $where['financial_type'] !== '', function ($query) use ($where) {
                $query->whereIn('financial_type', $where['financial_type']);
            })
            ->when(isset($where['mer_id']) && $where['mer_id'] !== '', function ($query) use ($where) {
                $query->where('mer_id', $where['mer_id']);
            })
            ->when(isset($where['user_info']) && $where['user_info'] !== '', function ($query) use ($where) {
                $query->where('user_info', $where['user_info']);
            })
            ->when(isset($where['user_id']) && $where['user_id'] !== '', function ($query) use ($where) {
                $query->where('user_id', $where['user_id']);
            })->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
                $query->whereLike('order_sn|user_info|financial_record_sn', "%{$where['keyword']}%");
            })->when(isset($where['date']) && $where['date'] !== '', function ($query) use ($where) {
                getModelTime($query, $where['date'], 'create_time');
            });

        return $query->order('create_time DESC');
    }

}
