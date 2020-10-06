<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system;


use app\common\dao\system\CacheDao;
use app\common\repositories\BaseRepository;
use think\db\exception\DbException;

/**
 * Class CacheRepository
 * @package app\common\repositories\system
 * @author xaboy
 * @day 2020-04-24
 * @mixin CacheDao
 */
class CacheRepository extends BaseRepository
{
    /**
     * CacheRepository constructor.
     * @param CacheDao $dao
     */
    public function __construct(CacheDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param string $key
     * @param $result
     * @param int $expire_time
     * @throws DbException
     * @author xaboy
     * @day 2020-04-24
     */
    public function save(string $key, $result, int $expire_time = 0)
    {
        if (!$this->dao->fieldExists('key', $key)) {
            $this->dao->create(compact('key', 'result', 'expire_time'));
        } else {
            $this->dao->keyUpdate($key, compact('result', 'expire_time'));
        }
    }
}