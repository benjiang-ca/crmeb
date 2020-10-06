import request from './request'
/**
 * @description 短信账户 -- 列表
 */
export function smsLstApi(data) {
  return request.get('sms/record', data)
}
/**
 * @description 短信账户 -- 登录
 */
export function configApi(data) {
  return request.post('sms/config', data)
}
/**
 * @description 短信账户 -- 修改密码
 */
export function changePsdApi(data) {
  return request.post('sms/change_password', data)
}
/**
 * @description 短信账户 -- 修改签名
 */
export function changeSignApi(data) {
  return request.post('sms/change_sign', data)
}
/**
 * @description 短信账户 -- 获取验证码
 */
export function captchaApi(data) {
  return request.post('sms/captcha', data)
}
/**
 * @description 短信账户 -- 注册
 */
export function registerApi(data) {
  return request.post('sms/register', data)
}
/**
 * @description 短信账户 -- 是否登录
 */
export function isLoginApi() {
  return request.get('sms/is_login')
}
/**
 * @description 短信账户 -- 退出登录
 */
export function logoutApi() {
  return request.get('sms/logout')
}
/**
 * @description 短信账户 -- 剩余条数
 */
export function smsNumberApi() {
  return request.get('sms/number')
}
/**
 * @description 短信模板 -- 列表
 */
export function smsTempLstApi(data) {
  return request.get('sms/temp', data)
}
/**
 * @description 短信购买 -- 支付套餐
 */
export function smsPriceApi() {
  return request.get('sms/price')
}
/**
 * @description 短信购买 -- 支付码
 */
export function payCodeApi(data) {
  return request.post('sms/pay_code', data)
}
/**
 * @description 短信模板 -- 添加表单
 */
export function tempCreateApi(data) {
  return request.post('sms/temp',data)
}
