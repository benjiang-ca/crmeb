<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    'default'     => 'redis',
    'connections' => [
        'sync'     => [
            'type' => 'sync',
        ],
        'database' => [
            'type'  => 'database',
            'queue' => env('queue_name', 'default'),
            'table' => 'jobs',
        ],
        'redis'    => [
            'type'       => 'redis',
            'queue'      => env('queue_name', 'default'),
            'host'       => env('redis.redis_hostname','127.0.0.1'),
            'port'       => env('redis.port', '6379'),
            'password'   => env('redis.redis_password', ''),
            'select'     => env('redis.select', 0),
            'timeout'    => 0,
            'persistent' => false,
        ],
    ],
    'failed'      => [
        'type'  => 'none',
        'table' => 'failed_jobs',
    ],
];
