<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-07-18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\merchant;

use app\common\dao\BaseDao;
use app\common\model\system\merchant\MerchantIntention;

class MerchantIntentionDao extends BaseDao
{
    protected function getModel(): string
    {
        return MerchantIntention::class;
    }

    public function search(array $where)
    {
        $query = $this->getModel()::getDB()->when(isset($where['mer_id']) && $where['mer_id'] !== '', function ($query) use ($where) {
            $query->where('mer_id', $where['mer_id']);
        })->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
            $query->where('mer_name|phone|mark', 'like', '%' . $where['keyword'] . '%');
        })->when(isset($where['date']) && $where['date'] !== '', function ($query) use ($where) {
            getModelTime($query, $where['date']);
        })->where('is_del', 0);

        return $query;
    }

    public function form($id, $data)
    {
        $this->getModel()::getDB()->where($this->getPk(), $id)->update(['status' => $data['status'], 'mark' => $data['mark']]);
    }
}
