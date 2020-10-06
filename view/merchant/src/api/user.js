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

export function getInfo(token) {
  return request({
    url: '/vue-element-admin/user/info',
    method: 'get',
    params: { token }
  })
}
/**
 * @description 标签
 */
export function getBaseInfo() {
  return request.get(`info`)
}

