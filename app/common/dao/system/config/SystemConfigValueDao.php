<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-24
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\dao\system\config;


use app\common\dao\BaseDao;
use app\common\model\BaseModel;
use app\common\model\system\config\SystemConfigValue;
use think\db\exception\DbException;

/**
 * Class SystemConfigValueDao
 * @package app\common\dao\system\config
 * @author xaboy
 * @day 2020-03-27
 */
class SystemConfigValueDao extends BaseDao
{

    /**
     * @return BaseModel
     * @author xaboy
     * @day 2020-03-30
     */
    protected function getModel(): string
    {
        return SystemConfigValue::class;
    }

    /**
     * @param int $merId
     * @param string $key
     * @param array $data
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-03-27
     */
    public function merUpdate(int $merId, string $key, array $data)
    {
        if (isset($data['value'])) $data['value'] = json_encode($data['value']);
        return SystemConfigValue::getDB()->where('mer_id', $merId)->where('config_key', $key)->update($data);
    }

    /**
     * @param array $keys
     * @param int $merId
     * @return array
     * @author xaboy
     * @day 2020-04-22
     */
    public function fields(array $keys, int $merId)
    {
        $result = SystemConfigValue::getDB()->whereIn('config_key', $keys)->where('mer_id', $merId)->withAttr('value', function ($val, $data) {
            return json_decode($val, true);
        })->column('value', 'config_key');
        foreach ($result as $k => $val) {
            $result[$k] = json_decode($val, true);
        }
        return $result;
    }

    /**
     * @param array $keys
     * @param int $merId
     * @return int
     * @throws DbException
     * @author xaboy
     * @day 2020-05-18
     */
    public function clear(array $keys, int $merId)
    {
        return SystemConfigValue::getDB()->whereIn('config_key', $keys)->where('mer_id', $merId)->delete();
    }


    /**
     * @param string $key
     * @param int $merId
     * @return mixed|null
     * @author xaboy
     * @day 2020-05-08
     */
    public function value(string $key, int $merId)
    {
        $value = SystemConfigValue::getDB()->where('config_key', $key)->where('mer_id', $merId)->value('value');
        $value = is_null($value) ? null : json_decode($value, true);
        return $value;
    }

    /**
     * @param string $key
     * @param int $merId
     * @return bool
     * @author xaboy
     * @day 2020-03-27
     */
    public function merExists(string $key, int $merId): bool
    {
        return SystemConfigValue::getDB()->where('config_key', $key)->where('mer_id', $merId)->count() > 0;
    }
}
