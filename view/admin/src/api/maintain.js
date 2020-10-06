import request from './request'

/**
 * @description 数据备份 -- 列表
 */
export function fileListApi() {
  return request.get('safety/database/fileList')
}
/**
 * @description 数据库表 -- 列表
 */
export function databaseListApi() {
  return request.get('safety/database/lst')
}
/**
 * @description 数据库表 -- 备份
 */
export function backupsApi(data) {
  return request.post(`safety/database/backups`, data)
}
/**
 * @description 数据库表 -- 修复
 */
export function repairApi(data) {
  return request.post(`safety/database/repair`, data)
}
/**
 * @description 数据库表 -- 优化
 */
export function optimizeApi(data) {
  return request.post(`safety/database/optimize`, data)
}
/**
 * @description 数据库表 -- 详情
 */
export function detailApi(name) {
  return request.get(`safety/database/detail/${name}`)
}
/**
 * @description 数据库 -- 下载
 */
export function downloadApi(feilname) {
  return request.get(`safety/database/download/${feilname}`)
}
/**
 * @description 数据库 -- 导出
 */
// export function downloadApi(feilname) {
//   return request.get(`safety/database/download/${feilname}`)
// }
/**
 * @description 授权 -- 获取授权状态
 */
export function authTypeApi() {
  return request.get(`auth`)
}
/**
 * @description 授权 -- 申请授权
 */
export function authApplyApi(data) {
  return request.post(`auth_apply`, data)
}
/**
 * @description 授权 -- 检查授权状态
 */
export function checkAuthApi() {
  return request.get(`check_auth`)
}
