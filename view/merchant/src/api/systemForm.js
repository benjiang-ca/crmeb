import request from './request'

/**
 * @description 验证码
 */
export function configApi(key) {
  return request.get(`config/${key}`)
}
export function modifyStoreApi() {
  return request.get(`update/form`)
}
