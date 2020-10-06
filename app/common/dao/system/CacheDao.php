<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-04-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\Cache;
use think\db\exception\DbException;

/**
 * Class CacheDao
 * @package app\common\dao\system
 * @author xaboy
 * @day 2020-04-24
 */
class CacheDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return Cache::class;
    }

    /**
     * @param $key
     * @return mixed
     * @author xaboy
     * @day 2020-04-24
     */
    public function getResult($key)
    {
        $val = Cache::getDB()->where('key', $key)->value('result');
        return $val ? json_decode($val, true) : null;
    }

    /**
     * @param string $key
     * @param $data
     * @throws DbException
     * @author xaboy
     * @day 2020-04-24
     */
    public function keyUpdate(string $key, $data)
    {
        if (isset($data['result']))
            $data['result'] = json_encode($data['result']);
        Cache::getDB()->where('key', $key)->update($data);
    }
}