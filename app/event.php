<?php
// 事件定义文件
return [
    'bind' => [
    ],

    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'swoole.task' => [\crmeb\listens\SwooleTaskListen::class],
        'swoole.init' => [\crmeb\listens\InitSwooleLockListen::class],
        'swoole.workerStart' => [\app\webscoket\SwooleWorkerStart::class],
        'swoole.start' => env('INSTALLED', false) ? [
            \crmeb\listens\AuthTakeOrderListen::class,
            \crmeb\listens\AutoCancelGroupOrderListen::class,
            \crmeb\listens\AutoUnLockBrokerageListen::class,
            \crmeb\listens\AutoSendPayOrderSmsListen::class,
            \crmeb\listens\SyncSmsResultCodeListen::class,
            \crmeb\listens\SyncBroadcastStatusListen::class,
            \crmeb\listens\ExcelFileDelListen::class,
            \crmeb\listens\RefundOrderAgreeListen::class,
            \crmeb\listens\SeckillTImeCheckListen::class,
            \crmeb\listens\AutoOrderReplyListen::class,
        ] : [],
        'pay_success_user_recharge' => [\crmeb\listens\pay\UserRechargeSuccessListen::class],
        'pay_success_order' => [\crmeb\listens\pay\OrderPaySuccessListen::class],
    ],

    'subscribe' => [
    ],
];
