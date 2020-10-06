import request from './request'
/**
 * @description 列表
 */
export function feedbackListApi(data) {
  return request.get(`user/feedback/lst`, data)
}

/**
 * @description 备注
 */
export function feedbackReplyApi(id) {
  return request.post(`user/feedback/reply/${id}`)
}

/**
 * @description 删除
 */
export function feedbackDeleteApi(id) {
  return request.delete(`user/feedback/delete/${id}`)
}

/**
 * @description 分类列表
 */
export function feedbackCategoryListApi(data) {
  return request.get(`user/feedback/category/lst`, data)
}

/**
 * @description 分类添加
 */
export function feedbackCategoryCreateApi() {
  return request.get(`user/feedback/category/create/form`)
}

/**
 * @description 分类编辑
 */
export function feedbackCategoryUpdateApi(id) {
  return request.get(`user/feedback/category/update/${id}/form`)
}

/**
 * @description 分类删除
 */
export function feedbackCategoryDeleteApi(id) {
  return request.delete(`user/feedback/category/delete/${id}`)
}

/**
 * @description 修改状态
 */
export function feedbackCategoryStatusApi(id, status) {
  return request.post(`user/feedback/category/status/${id}`, { status })
}
