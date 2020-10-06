import request from './request'
/**
 * @description 身份管理 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function menuRoleApi(data) {
  return request.get(`system/role/lst`, data)
}

/**
 * @description 身份管理 -- 新增
 * @param {Object} param params {Object} 传值参数
 */
export function roleCreateApi() {
  return request.get(`system/role/create/form`)
}

/**
 * @description 身份管理 -- 编辑
 * @param {Object} param params {Object} 传值参数
 */
export function roleUpdateApi(id) {
  return request.get(`system/role/update/form/${id}`)
}

/**
 * @description 身份管理 -- 删除
 * @param {Object} param params {Object} 传值参数
 */
export function roleDeleteApi(id) {
  return request.delete(`system/role/delete/${id}`)
}

/**
 * @description 身份管理 -- 修改状态
 * @param {Object} param params {Object} 传值参数
 */
export function roleStatusApi(id, status) {
  return request.post(`system/role/status/${id}`, { status })
}
/**
 * @description 管理员 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function adminListApi(data) {
  return request.get(`system/admin/lst`, data)
}

/**
 * @description 管理员 -- 新增
 * @param {Object} param params {Object} 传值参数
 */
export function adminCreateApi() {
  return request.get(`/system/admin/create/form`)
}

/**
 * @description 管理员 -- 编辑
 * @param {Object} param params {Object} 传值参数
 */
export function adminUpdateApi(id) {
  return request.get(`system/admin/update/form/${id}`)
}

/**
 * @description 管理员 -- 删除
 * @param {Object} param params {Object} 传值参数
 */
export function adminDeleteApi(id) {
  return request.delete(`system/admin/delete/${id}`)
}

/**
 * @description 管理员 -- 修改状态
 * @param {Object} param params {Object} 传值参数
 */
export function adminStatusApi(id, status) {
  return request.post(`system/admin/status/${id}`, { status })
}
/**
 * @description 管理员 -- 修改密码表单
 * @param {Object} param params {Object} 传值参数
 */
export function adminPasswordApi(id) {
  return request.get(`system/admin/password/form/${id}`)
}
/**
 * @description 操作日志 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function adminLogApi(data) {
  return request.get(`system/admin/log`, data)
}
