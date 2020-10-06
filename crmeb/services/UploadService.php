<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/10/24
 */

namespace crmeb\services;

use crmeb\services\upload\Upload;

/**
 * Class UploadService
 * @package crmeb\services
 */
class UploadService
{

    /**
     * @param $type
     * @return Upload
     */
    public static function create($type = null)
    {
        if (is_null($type)) {
            $type = (int)systemConfig('upload_type') ?: 1;
        }
        $type = (int)$type;
        $config = [];
        switch ($type) {
            case 2://七牛
                $data = systemConfig(['qiniu_accessKey', 'qiniu_secretKey', 'qiniu_uploadUrl', 'qiniu_storage_name', 'qiniu_storage_region']);
                $config = [
                    'accessKey' => $data['qiniu_accessKey'],
                    'secretKey' => $data['qiniu_secretKey'],
                    'uploadUrl' => $data['qiniu_uploadUrl'],
                    'storageName' => $data['qiniu_storage_name'],
                    'storageRegion' => $data['qiniu_storage_region'],
                ];
                break;
            case 3:// oss 阿里云
                $data = systemConfig(['accessKey', 'secretKey', 'uploadUrl', 'storage_name', 'storage_region']);

                $config = [
                    'accessKey' => $data['accessKey'],
                    'secretKey' => $data['secretKey'],
                    'uploadUrl' => $data['uploadUrl'],
                    'storageName' => $data['storage_name'],
                    'storageRegion' => $data['storage_region'],
                ];
                break;
            case 4:// cos 腾讯云
                $data = systemConfig(['tengxun_accessKey', 'tengxun_secretKey', 'tengxun_uploadUrl', 'tengxun_storage_name', 'tengxun_storage_region']);
                $config = [
                    'accessKey' => $data['tengxun_accessKey'],
                    'secretKey' => $data['tengxun_secretKey'],
                    'uploadUrl' => $data['tengxun_uploadUrl'],
                    'storageName' => $data['tengxun_storage_name'],
                    'storageRegion' => $data['tengxun_storage_region'],
                ];
                break;
        }

        return new Upload($type, $config);
    }

}