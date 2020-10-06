<?php
// +----------------------------------------------------------------------
// | 模板消息配置
// +----------------------------------------------------------------------

return [
    //默认驱动模式
    'default' => 'wechat',
    //记录发送日志
    'isLog' => true,
    //驱动模式
    'stores' => [
        //微信
        'wechat' => [
            //短信模板id
            'template_id' => [
                //订单生成通知
                'ORDER_CREATE' => 'OPENTM205213550',
                //支付成功
                'ORDER_PAY_SUCCESS' => 'OPENTM207791277',
                //订单发货提醒(快递)
                'ORDER_POSTAGE_SUCCESS' => 'OPENTM200565259',
                //订单发货提醒(送货)
                'ORDER_DELIVER_SUCCESS' => 'OPENTM207707249',
                //提现结果
                //'EXTRACT_NOTICE' => 'OPENTM207601150',
                //订单收货通知
                'ORDER_TAKE_SUCCESS' => 'OPENTM413386489',
                //帐户资金变动提醒
                'USER_BALANCE_CHANGE' => 'OPENTM415437052',
                //退款申请通知
                'ORDER_REFUND_STATUS' => 'OPENTM407277862',
                //退款进度提醒
                'ORDER_REFUND_NOTICE' => 'OPENTM401479948',
                //退货确认提醒
                'ORDER_REFUND_END' => 'OPENTM406292353',
            ],
        ],
        //订阅消息
        'subscribe' => [
            'template_id' => [
                //订单发货提醒(送货)
                'ORDER_POSTAGE_SUCCESS' => 1128,
                //提现成功通知
                'USER_EXTRACT' => 1470,
                //订单发货提醒(快递)
                'ORDER_DELIVER_SUCCESS' => 1458,
                //退款通知
                'ORDER_REFUND_NOTICE' => 1451,
                //充值成功
                'RECHARGE_SUCCESS' => 755,
                //订单支付成功
                'ORDER_PAY_SUCCESS' => 1927,
            ],
        ],
    ]
];
