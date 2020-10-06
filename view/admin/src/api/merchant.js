import request from './request'

/**
 * @description 商户权限管理 -- 列表
 */
export function merchantMenuListApi(data) {
  return request.get('merchant/menu/lst', data)
}
/**
 * @description 商户权限管理 -- 新增表单
 */
export function merchantMenuCreateApi() {
  return request.get('merchant/menu/create/form')
}
/**
 * @description 商户权限管理 -- 编辑表单
 */
export function merchantMenuUpdateApi(id) {
  return request.get(`merchant/menu/update/form/${id}`)
}
/**
 * @description 商户权限管理 -- 删除
 */
export function merchantMenuDeleteApi(id) {
  return request.delete(`merchant/menu/delete/${id}`)
}

/**
 * @description 商户列表 -- 列表
 */
export function merchantListApi(data) {
  return request.get('system/merchant/lst', data)
}
/**
 * @description 商户列表 -- 新增表单
 */
export function merchantCreateApi() {
  return request.get('system/merchant/create/form')
}
/**
 * @description 商户列表 -- 编辑表单
 */
export function merchantUpdateApi(id) {
  return request.get(`system/merchant/update/form/${id}`)
}
/**
 * @description 商户列表 -- 删除
 */
export function merchantDeleteApi(id) {
  return request.delete(`system/merchant/delete/${id}`)
}
/**
 * @description 商户列表 -- 修改开启状态
 */
export function merchantStatuseApi(id, status) {
  return request.post(`system/merchant/status/${id}`, { status })
}
/**
 * @description 商户列表 -- 修改密码
 */
export function merchantPasswordApi(id) {
  return request.get(`system/merchant/password/form/${id}`)
}
/**
 * @description 商户分类 -- 列表
 */
export function categoryListApi(data) {
  return request.get('system/merchant/category/lst', data)
}
/**
 * @description 商户分类 -- 新增表单
 */
export function categoryCreateApi() {
  return request.get('system/merchant/category/form')
}
/**
 * @description 商户分类 -- 编辑表单
 */
export function categoryUpdateApi(id) {
  return request.get(`system/merchant/category/form/${id}`)
}
/**
 * @description 商户分类 -- 删除
 */
export function categoryDeleteApi(id) {
  return request.delete(`system/merchant/category/${id}`)
}
/**
 * @description 商户对账 -- 订单列表
 */
export function merOrderListApi(id, data) {
  return request.get(`merchant/order/lst/${id}`, data)
}
/**
 * @description 商户对账 -- 订单备注
 */
export function orderMarkApi(id) {
  return request.get(`merchant/order/mark/${id}/form`)
}
/**
 * @description 商户对账 -- 退款订单列表
 */
export function refundOrderListApi(id, data) {
  return request.get(`merchant/order/refund/lst/${id}`, data)
}
/**
 * @description 退款订单 -- 订单备注
 */
export function refundMarkApi(id) {
  return request.get(`merchant/order/refund/mark/${id}/form`)
}
/**
 * @description 对账订单 -- 发起对账单
 */
export function reconciliationApi(id, data) {
  return request.post(`merchant/order/reconciliation/create/${id}`, data)
}
/**
 * @description 对账订单 -- 发起对账单
 */
export function merchantLoginApi(mer_id) {
  return request.post(`system/merchant/login/${mer_id}`)
}
/**
 * @description 申请管理 -- 列表
 */
export function intentionLstApi(data) {
  return request.get('merchant/intention/lst', data)
}
/**
 * @description 申请管理 -- 备注
 */
export function auditApi(mer_id) {
  return request.get(`merchant/intention/mark/${mer_id}/form`)
}
/**
 * @description 申请管理 -- 删除
 */
export function intentionDelte(mer_id) {
  return request.delete(`merchant/intention/delete/${mer_id}`)
}
/**
 * @description 申请管理 -- 修改状态
 */
export function intentionStatusApi(mer_id) {
  return request.get(`merchant/intention/status/${mer_id}/form`)
}
/**
 * @description 申请管理 -- 编辑复制次数
 */
export function changeCopyApi(mer_id) {
  return request.get(`system/merchant/changecopy/${mer_id}/form`)
}
/**
 * @description 申请管理 -- 入驻协议详情
 */
export function intentionAgreeInfo() {
  return request.get(`merchant/intention/agree`)
}
/**
 * @description 申请管理 -- 入驻协议保存
 */
export function intentionAgreeUpdate(data) {
  return request.post(`merchant/intention/agree`,data)
}
/**
 * @description 商户列表 -- 开启关闭
 */
export function merchantIsCloseApi(id, status) {
  return request.post(`system/merchant/close/${id}`, { status })
}
/**
 * @description 商户列表 -- 开启商户数
 */
export function merchantCountApi() {
  return request.get(`system/merchant/count`)
}
