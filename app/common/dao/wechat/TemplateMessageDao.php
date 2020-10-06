<?php
/**
 * @package crmeb_merchant
 *
 * @author Qinii
 * @day 2020-06-18
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\wechat;

use app\common\dao\BaseDao;
use app\common\model\wechat\TemplateMessage;

class TemplateMessageDao extends BaseDao
{
    protected function getModel(): string
    {
        return TemplateMessage::class;
    }


    public function search(array $where)
    {
        return ($this->getModel()::getDB())->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
            $query->where('status', $where['status']);
        })->when(isset($where['type']) && $where['type'] !== '', function ($query) use ($where) {
            $query->where('type', $where['type']);
        })->when(isset($where['keyword']) && $where['keyword'] !== '', function ($query) use ($where) {
            $query->where(function($query)use($where) {
                $query->where('name', 'like', '%' . $where['keyword'] . '%');
                $query->whereOr('tempid', 'like', '%' . $where['keyword'] . '%');
            });
        })->order('create_time DESC');
    }

    public function getTempId($key, $type)
    {
        return TemplateMessage::getDB()->where(['type' => $type, 'tempkey' => $key, 'status' => 1])->value('tempid');
    }
}
