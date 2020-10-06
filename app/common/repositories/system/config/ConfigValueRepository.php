<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020-03-27
 *
 * Copyright (c) http://crmeb.net
 */

namespace app\common\repositories\system\config;


use app\common\dao\system\config\SystemConfigValueDao;
use app\common\repositories\BaseRepository;
use think\exception\ValidateException;
use think\facade\Db;

/**
 * Class ConfigValueRepository
 * @package app\common\repositories\system\config
 * @mixin SystemConfigValueDao
 */
class ConfigValueRepository extends BaseRepository
{
    /**
     * ConfigValueRepository constructor.
     * @param SystemConfigValueDao $dao
     */
    public function __construct(SystemConfigValueDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $keys
     * @param int $merId
     * @return array
     * @author xaboy
     * @day 2020-03-27
     */
    public function more(array $keys, int $merId): array
    {
        $config = $this->dao->fields($keys, $merId);
        foreach ($keys as $key) {
            if (!isset($config[$key])) $config[$key] = '';
        }
        return $config;
    }

    /**
     * @param string $key
     * @param int $merId
     * @return mixed|string|null
     * @author xaboy
     * @day 2020-05-08
     */
    public function get(string $key, int $merId)
    {
        $value = $this->dao->value($key, $merId);
        return $value ?? '';
    }

    /**
     * @param int $cid
     * @param array $formData
     * @param int $merId
     * @author xaboy
     * @day 2020-03-27
     */
    public function save(int $cid, array $formData, int $merId)
    {
        $keys = array_keys($formData);
        $keys = app()->make(ConfigRepository::class)->intersectionKey($cid, $keys);
        if (!count($keys)) return;
        foreach ($keys as $key => $info) {
            if (!isset($formData[$key]))
                unset($formData[$key]);
            else {
                if ($info['config_type'] == 'number') {
                    if ($formData[$key] === '' || $formData[$key] < 0)
                        throw new ValidateException($info['config_name'] . '不能小于0');
                    $formData[$key] = floatval($formData[$key]);
                }
            }
        }
        $this->setFormData($formData, $merId);
    }

    public function setFormData(array $formData, int $merId)
    {
        Db::transaction(function () use ($merId, $formData) {
            foreach ($formData as $key => $value) {
                if ($this->dao->merExists($key, $merId))
                    $this->dao->merUpdate($merId, $key, ['value' => $value]);
                else
                    $this->dao->create([
                        'mer_id' => $merId,
                        'value' => $value,
                        'config_key' => $key
                    ]);
            }
        });
    }
}
