<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'menu' => 'app\command\updateMenu',
        'menu:format' => 'app\command\FormatMenuPath',
        'clear:attachment' => 'app\command\ClearCacheAttachment',
    ],
];
