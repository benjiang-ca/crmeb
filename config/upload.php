<?php
// +----------------------------------------------------------------------
// | 上传配置
// +----------------------------------------------------------------------

return [
    //默认上传模式
    'default' => 'local',
    //上传文件大小
    'filesize' => 2097152,
    //上传文件后缀类型
    'fileExt' => ['jpg', 'jpeg', 'png', 'gif', 'pem', 'mp3', 'wma', 'wav', 'amr', 'mp4', 'key'],
    //上传文件类型
    'fileMime' => ['image/jpeg', 'image/gif', 'image/png', 'text/plain', 'audio/mpeg'],
    //驱动模式
    'stores' => [
        //本地上传配置
        'local' => [],
        //七牛云上传配置
        'qiniu' => [],
        //oss上传配置
        'oss' => [],
        //cos上传配置
        'cos' => [],
    ]
];