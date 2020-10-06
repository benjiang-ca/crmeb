import request from "@/utils/request.js";
import wechat from "@/libs/wechat.js";

/**
 * 获取微信sdk配置
 * @returns {*}
 */
export function getWechatConfig() {
	return request.get(
		"wechat/config", {
			url: wechat.signLink()
		}, {
			noAuth: true
		}
	);
}

/**
 * 获取微信sdk配置
 * @returns {*}
 */
export function wechatAuth(code, spread, login_type) {
	return request.get(
		"auth/wechat", {
			code,
			spread,
			login_type
		}, {
			noAuth: true
		}
	);
}

/**
 * 获取登录授权login
 * 
 */
export function getLogo() {
	return request.get('wechat/get_logo', {}, {
		noAuth: true
	});
}

/**
 * 小程序用户登录
 * @param data object 小程序用户登陆信息
 */
export function login(data) {
	return request.post("auth/mp", data, {
		noAuth: true
	});
}
/**
 * 分享
 * @returns {*}
 */
export function getShare() {
	return request.get("share", {}, {
		noAuth: true
	});
}

/**
 * 获取关注海报
 * @returns {*}
 */
export function follow() {
	return request.get("wechat/follow", {}, {
		noAuth: true
	});
}

/**
 * 获取图片base64
 * @retins {*}
 * */
export function imageBase64(image, code) {
	return request.post(
		"common/base64", {
			image: image,
			code: code
		}, {
			noAuth: true
		}
	);
}

// 配置
export function getconfig() {
	return request.get("config",{},{noAuth: true});
}

// 浏览记录
export function history(data) {
	return request.post("common/visit",data);
}