import request from './request'
/**
 * @description 运费模板 -- 城市
 */
export function cityList() {
  return request.get('system/city/lst')
}
/**
 * @description 运费模板 -- 列表
 */
export function templateListApi(data) {
  return request.get('store/shipping/lst', data)
}
/**
 * @description 运费模板 -- 新增
 */
export function templateCreateApi(data) {
  return request.post('store/shipping/create', data)
}
/**
 * @description 运费模板 -- 编辑
 */
export function templateUpdateApi(id, data) {
  return request.post(`store/shipping/update/${id}`, data)
}
/**
 * @description 运费模板 -- 详情
 */
export function templateDetailApi(id) {
  return request.get(`/store/shipping/detail/${id}`)
}
/**
 * @description 运费模板 -- 删除
 */
export function templateDeleteApi(id) {
  return request.delete(`store/shipping/delete/${id}`)
}
