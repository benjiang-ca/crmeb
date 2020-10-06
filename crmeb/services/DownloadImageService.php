<?php
/**
 * @package crmeb_merchant
 *
 * @author xaboy
 * @day 2020/8/1
 *
 * Copyright (c) http://crmeb.net
 */

namespace crmeb\services;


use think\exception\ValidateException;

class DownloadImageService
{
    /**
     * 获取即将要下载的图片扩展名
     * @param string $url
     * @param string $ex
     * @return array|string[]
     */
    public function getImageExtname($url = '', $ex = 'jpg')
    {
        $_empty = ['file_name' => '', 'ext_name' => $ex];
        if (!$url) return $_empty;
        if (strpos($url, '?')) {
            $_tarr = explode('?', $url);
            $url = trim($_tarr[0]);
        }
        $arr = explode('.', $url);
        if (!is_array($arr) || count($arr) <= 1) return $_empty;
        $ext_name = trim($arr[count($arr) - 1]);
        $ext_name = !$ext_name ? $ex : $ext_name;
        return ['file_name' => md5($url) . '.' . $ext_name, 'ext_name' => $ext_name];
    }

    /**
     * @param $url
     * @param string $name
     * @param int $upload_type
     * @return mixed
     * @author xaboy
     * @day 2020/8/1
     */
    public function downloadImage($url, $name = '', $upload_type = 1)
    {
        if (!$name) {
            //TODO 获取要下载的文件名称
            $downloadImageInfo = $this->getImageExtname($url);
            $name = $downloadImageInfo['file_name'];
            if (!$name) throw new ValidateException('上传图片不存在');
        }

        ob_start();
        readfile($url);
        $content = ob_get_contents();
        ob_end_clean();

        $size = strlen(trim($content));
        if (!$content || $size <= 2) throw new ValidateException('图片流获取失败');
        $date_dir = date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d');
        $upload = UploadService::create($upload_type);
        if ($upload->to('attach/' . $date_dir)->stream($content, $name) === false) {
            throw new ValidateException('图片下载失败');
        }
        $imageInfo = $upload->getUploadInfo();
        $date['path'] = $imageInfo['dir'];
        $date['name'] = $imageInfo['name'];
        $date['size'] = $imageInfo['size'];
        $date['mime'] = $imageInfo['type'];
        $date['image_type'] = $upload_type;
        $date['is_exists'] = false;
        return $date;
    }
}