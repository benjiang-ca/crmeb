import {
	SUBSCRIBE_MESSAGE
} from '../config/cache.js';

export function auth() {
	let tmplIds = {};
	let messageTmplIds = uni.getStorageSync(SUBSCRIBE_MESSAGE);
	console.log(messageTmplIds,'messageTmplIds')
	tmplIds = messageTmplIds ? messageTmplIds : {};
	return tmplIds;
}

/**
 * 支付成功后订阅消息id
 * 订阅  订单支付成功 订单发货提醒(快递)  订单发货提醒(送货) 
 */
export function openPaySubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.ORDER_POSTAGE_SUCCESS ,
		tmplIds.ORDER_DELIVER_SUCCESS,
		tmplIds.ORDER_PAY_SUCCESS,
	]); 
}

/**
 * 订单相关订阅消息
 * 送货 发货
 */
export function openOrderSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.ORDER_DELIVER_SUCCESS,
		tmplIds.ORDER_POSTAGE_SUCCESS,
	]);
}

/**
 * 提现消息订阅
 * 成功 和 失败 消息
 */
export function openExtrctSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.user_extract
	]);
}

/**
 * 拼团成功
 */
export function openPinkSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.pink_true
	]);
}

/**
 * 砍价成功
 */
export function openBargainSubscribe() {
	let tmplIds = auth();
	return subscribe([
		tmplIds.bargain_success
	]);
}

/**
 * 订单退款
 */
export function openOrderRefundSubscribe() {
	let tmplIds = auth();
	return subscribe([tmplIds.ORDER_REFUND_NOTICE]);
}

/**
 * 充值成功
 */
export function openRechargeSubscribe() {
	let tmplIds = auth();
	return subscribe([tmplIds.RECHARGE_SUCCESS]);
}

/**
 * 提现
 */
export function openEextractSubscribe() {
	let tmplIds = auth();
	return subscribe([tmplIds.USER_EXTRACT]);
}

/**
 * 调起订阅界面
 * array tmplIds 模板id
 */
export function subscribe(tmplIds) {
	 let wecaht = wx;
	return new Promise((reslove, reject) => {
		uni.requestSubscribeMessage({
			tmplIds: tmplIds,
			success(res) {
				console.log(res,'requestSubscribeMessage')
				return reslove(res);
			},
			fail(res) {
				console.log(res,'fail')
				return reslove(res);
			},
			complete(res){
				console.log(res,'complete')
			}
		})
	});
}
