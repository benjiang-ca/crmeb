import request from './request'

/**
 * @description 分销设置 -- 详情
 */
export function configApi() {
  return request.get('config/others/lst')
}

/**
 * @description 分销设置 -- 表单提交
 */
export function configUpdateApi(data) {
  return request.post('config/others/update', data)
}

/**
 * @description 分销设置 -- 表单提交
 */
export function productCheckApi() {
  return request.post('store/product/check')
}

/**
 * @description 分销员 -- 列表
 */
export function promoterListApi(data) {
  return request.get('user/promoter/lst', data)
}

/**
 * @description 推广人 -- 列表
 */
export function spreadListApi(uid, data) {
  return request.get(`user/spread/lst/${uid}`, data)
}

/**
 * @description 推广人订单 -- 列表
 */
export function spreadOrderListApi(uid, data) {
  return request.get(`user/spread/order/${uid}`, data)
}

/**
 * @description 推广人 -- 清除上级推广人
 */
export function spreadClearApi(uid) {
  return request.post(`user/spread/clear/${uid}`)
}

/**
 * @description 商品列表 -- 列表
 */
export function productLstApi(data) {
  return request.get(`store/bag/lst`, data)
}

/**
 * @description 商品列表 -- 平台分类
 */
export function categoryListApi() {
  return request.get(`store/category/list`)
}

/**
 * @description 商品审核 -- 详情
 */
export function productDetailApi(id) {
  return request.get(`store/bag/detail/${id}`)
}

/**
 * @description 商品审核/下架
 */
export function productStatusApi(data) {
  return request.post(`store/bag/status`, data)
}

/**
 * @description 商品列表 -- 列表表头
 */
export function lstFilterApi() {
  return request.get(`store/bag/lst_filter`)
}

/**
 * @description 商品列表 -- 显示隐藏
 */
export function changeApi(id, status) {
  return request.post(`store/bag/change/${id}`, { status })
}
/**
 * @description 商户总
 */
export function merSelectApi() {
  return request.get(`store/product/mer_select`)
}
/**
 * @description 商品下架
 */
export function productOffApi(data) {
  return request.post(`store/bag/status`, data)
}
/**
 * @description 商品编辑
 */
export function productUpdateApi(id, data) {
  return request.post(`store/bag/update/${id}`, data)
}
