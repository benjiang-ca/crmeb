<?php
// +----------------------------------------------------------------------
// | 短信配置
// +----------------------------------------------------------------------

return [
    //默认支付模式
    'default' => 'yunxin',
    //单个手机每日发送上限
    'maxPhoneCount' => 10,
    //验证码每分钟发送上线
    'maxMinuteCount' => 20,
    //单个IP每日发送上限
    'maxIpCount' => 50,
    //短信模板id
    'template_id' => [
        //验证码
        'VERIFICATION_CODE' => 538393,
        //发货提醒
        'DELIVER_GOODS_CODE' => 520269,
        //确认收货提醒
        'TAKE_DELIVERY_CODE' => 520271,
        //支付成功
        'PAY_SUCCESS_CODE' => 520268,
        //改价提醒
        'PRICE_REVISION_CODE' => 528288,
        //订单未支付
        'ORDER_PAY_FALSE' => 528116,
        //商家同意退款提醒
        'REFUND_SUCCESS_CODE' => 536113,
        //商家拒绝退款提醒
        'REFUND_FAIL_CODE' => 536112,
        //退款确认提醒
        'REFUND_CONFORM_CODE' => 536111,
        //管理员支付成功提醒
        'ADMIN_PAY_SUCCESS_CODE' => 520273,
        //管理员退货提醒
        'ADMIN_RETURN_GOODS_CODE' => 520274,
        //管理员确认收货
        'ADMIN_TAKE_DELIVERY_CODE' => 520422,
        //退货信息提醒
        'ADMIN_DELIVERY_CODE' => 536114,
        //直播通过通知
        'BROADCAST_ROOM_CODE' => 538726,
    ],
];