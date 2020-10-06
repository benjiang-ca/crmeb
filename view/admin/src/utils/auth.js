import Cookies from 'js-cookie'
import vm from '@/main'

const TokenKey = 'Token'

export function getToken() {
  return Cookies.get(TokenKey)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token)
}

export function removeToken() {
  vm && vm.closeNotice()
  return Cookies.remove(TokenKey)
}
