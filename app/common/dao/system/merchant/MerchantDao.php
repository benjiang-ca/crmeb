<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-16
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\merchant;


use app\common\dao\BaseDao;
use app\common\model\system\merchant\Merchant;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

class MerchantDao extends BaseDao
{

    /**
     * @return string
     * @author xaboy
     * @day 2020-04-16
     */
    protected function getModel(): string
    {
        return Merchant::class;
    }

    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-04-16
     */
    public function search(array $where, $is_del = 0)
    {
        $query = Merchant::getDB()
            ->when($is_del !== null, function ($query) use ($is_del) {
                $query->where('is_del', $is_del);
            })
            ->when(isset($where['is_trader']) && $where['is_trader'] !== '', function ($query) use ($where) {
                $query->where('is_trader', $where['is_trader']);
            })
            ->when(isset($where['date']) && $where['date'] !== '', function ($query) use ($where) {
                getModelTime($query, $where['date']);
            })
            ->when(isset($where['mer_state']) && $where['mer_state'] !== '', function ($query) use ($where) {
                $query->where('mer_state', $where['mer_state']);
            });

        if (isset($where['keyword']) && $where['keyword'])
            $query->whereLike('mer_name|mer_keyword|mer_phone', "%{$where['keyword']}%");
        if (isset($where['status']) && $where['status'] !== '')
            $query->where('status', $where['status']);
        $order = $where['order'] ?? '';
        $query->when($order, function ($query) use ($order) {
            if (in_array($order, ['rate', 'sales']) && $order == 'rate')
                $query->order('is_best DESC, product_score DESC,service_score DESC,postage_score DESC');
            else
                $query->order('is_best DESC, sales DESC,sort DESC');
        }, function ($query) use ($order) {
            $query->order('is_best DESC, sort DESC,sales DESC');
        });
        return $query;
    }

    /**
     * @param int $id
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author xaboy
     * @day 2020-04-17
     */
    public function get($id)
    {
        return Merchant::getInstance()->where('is_del', 0)->find($id);
    }

    /**
     * @param $id
     * @author Qinii
     */
    public function apiGetOne($id)
    {
        return Merchant::getInstance()->where(['is_del' => 0, 'status' => 1, 'mer_state' => 1])->find($id);
    }

    /**
     * @param int $merId
     * @author Qinii
     */
    public function incCareCount(int $merId)
    {
        ($this->getModel()::getDB())->where($this->getPk(), $merId)->inc('care_count', 1)->update();
    }

    /**
     * @param int $merId
     * @param int $inc
     * @author xaboy
     * @day 2020/9/25
     */
    public function incSales($merId, $inc)
    {
        ($this->getModel()::getDB())->where($this->getPk(), $merId)->inc('sales', $inc)->update();
    }

    /**
     * @param int $merId
     * @author Qinii
     */
    public function decCareCount(int $merId)
    {
        ($this->getModel()::getDB())->where($this->getPk(), $merId)->where('care_count', '>', 0)->dec('care_count', 1)->update();
    }

    public function dateMerchantNum($date)
    {
        return Merchant::getDB()->where('is_del', 0)->when($date, function ($query, $date) {
            getModelTime($query, $date);
        })->count();
    }

    /**
     * TODO 获取复制商品次数
     * @param int $merId
     * @return mixed
     * @author Qinii
     * @day 2020-08-06
     */
    public function getCopyNum(int $merId)
    {
        return Merchant::getDB()->where('mer_id', $merId)->value('copy_product_num');
    }

    /**
     * TODO 变更复制次数
     * @param int $merId
     * @param int $num 正负数
     * @return mixed
     * @author Qinii
     * @day 2020-08-06
     */
    public function changeCopyNum(int $merId, int $num)
    {
        return $this->getModel()::where('mer_id', $merId)->inc('copy_product_num', $num)->update();
    }
}
