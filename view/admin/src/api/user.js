import request from './request'
/**
 * @description 验证码
 */
export function captchaApi() {
  return request.get(`captcha`)
}
/**
  * @description 登录
  */
export function login(data) {
  return request.post(`login`, data)
}
/**
 * @description 登录页配置
 */
export function loginConfigApi() {
  return request.get(`login_config`)
}
/**
 * @description 退出登录
 */
export function logout() {
  return request.get(`logout`)
}

/**
 * @description 修改密码
 */
export function passwordFormApi() {
  return request.get(`system/admin/edit/password/form`)
}

/**
 * @description 修改自己的信息
 */
export function editFormApi() {
  return request.get(`system/admin/edit/form`)
}

/**
 * @description 菜单
 */
export function getMenusApi() {
  return request.get(`menus`)
}

/**
 * @description 用户分组 -- 编辑表单
 * @param {Object} param params {Object} 传值参数
 */
export function groupEditApi(id) {
  return request.get('user/group/form/' + id)
}
/**
 * @description 用户分组 -- 添加表单
 */
export function groupFormApi() {
  return request.get('user/group/form')
}
/**
 * @description 用户分组 -- 列表
 */
export function groupLstApi(data) {
  return request.get('user/group/lst', data)
}
/**
 * @description 用户分组 -- 删除
 */
export function groupDeleteApi(id) {
  return request.delete(`user/group/${id}`)
}
/**
 * @description 用户标签 -- 编辑表单
 * @param {Object} param params {Object} 传值参数
 */
export function labelEditApi(id) {
  return request.get('user/label/form/' + id)
}
/**
 * @description 用户标签 -- 添加表单
 */
export function labelFormApi() {
  return request.get('user/label/form')
}
/**
 * @description 用户标签 -- 列表
 */
export function labelLstApi(data) {
  return request.get('user/label/lst', data)
}
/**
 * @description 用户标签 -- 删除
 */
export function labelDeleteApi(id) {
  return request.delete(`user/label/${id}`)
}
/**
 * @description 用户列表 -- 列表
 */
export function userLstApi(data) {
  return request.get('user/lst', data)
}
/**
 * @description 用户列表 -- 设置分组
 */
export function changeGroupApi(id) {
  return request.get(`user/change_group/form/${id}`)
}
/**
 * @description 用户列表 -- 设置标签
 */
export function changelabelApi(id) {
  return request.get(`user/change_label/form/${id}`)
}
/**
 * @description 用户列表 -- 修改余额
 */
export function changeNowMoneyApi(id) {
  return request.get(`user/change_now_money/form/${id}`)
}
/**
 * @description 用户列表 -- 批量设置分组
 */
export function batchChangeGroupApi(data) {
  return request.get(`user/batch_change_group/form`, data)
}
/**
 * @description 用户列表 -- 批量设置标签
 */
export function batchChangelabelApi(data) {
  return request.get(`user/batch_change_label/form`, data)
}
/**
 * @description 用户列表 -- 编辑用户
 */
export function userUpdateApi(id) {
  return request.get(`user/update/form/${id}`)
}
/**
 * @description 用户列表 -- 发送图文消息
 */
export function userNewsApi(data) {
  return request.post(`user/news/push`, data)
}
/**
 * @description 用户 -- 详情头部
 */
export function userDetailApi(uid) {
  return request.get(`user/detail/${uid}`)
}
/**
 * @description 用户 -- 详情消费记录
 */
export function userOrderApi(uid, data) {
  return request.get(`user/order/${uid}`, data)
}
/**
 * @description 用户 -- 详情优惠券
 */
export function userCouponApi(uid, data) {
  return request.get(`user/coupon/${uid}`, data)
}
/**
 * @description 用户 -- 余额明细
 */
export function userBillApi(uid, data) {
  return request.get(`user/bill/${uid}`, data)
}
/**
 * @description 用户 -- 城市列表
 */
export function cityListApi(uid) {
  return request.get(`system/city/lst`)
}
