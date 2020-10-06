<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\attachment;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\attachment\Attachment;
use think\db\BaseQuery;
use think\db\exception\DbException;

/**
 * Class AttachmentDao
 * @package app\common\dao\system\attachment
 * @author xaboy
 * @day 2020-04-16
 */
class AttachmentDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return Attachment::class;
    }

    /**
     * @param array $where
     * @return BaseQuery
     * @author xaboy
     * @day 2020-04-15
     */
    public function search(array $where)
    {
        $query = Attachment::getDB()->order('create_time DESC');
        if (isset($where['user_type'])) $query->where('user_type', (int)$where['user_type']);
        if (isset($where['upload_type'])) $query->where('upload_type', (int)$where['upload_type']);
        if (isset($where['attachment_category_id']) && $where['attachment_category_id']) $query->where('attachment_category_id', (int)$where['attachment_category_id']);
        return $query;
    }

    /**
     * @param int $id
     * @param int $userType
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-04-16
     */
    public function delete(int $id, $userType = 0)
    {
        return ($this->getModel())::getDB()->where('user_type', $userType)->where($this->getPk(), $id)->delete();
    }

    /**
     * @param array $ids
     * @param int $userType
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-04-15
     */
    public function batchDelete(array $ids, $userType = 0)
    {
        return ($this->getModel())::getDB()->where('user_type', $userType)->whereIn($this->getPk(), $ids)->delete();
    }

    /**
     * @param int $id
     * @param int $userType
     * @return bool
     * @author xaboy
     * @day 2020-04-16
     */
    public function exists(int $id, $userType = 0)
    {
        return ($this->getModel())::getDB()->where($this->getPk(), $id)->count() > 0;
    }

    /**
     * @param array $ids
     * @param array $data
     * @param int $user_type
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-04-16
     */
    public function batchChange(array $ids, array $data, int $user_type = 0)
    {
        return ($this->getModel())::getDB()->where('user_type', $user_type)->whereIn($this->getPk(), $ids)->update($data);
    }

    public function clearCache()
    {
        return Attachment::getDB()->where('user_type', -1)->delete();
    }
}

