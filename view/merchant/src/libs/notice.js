import SettingMer from '@/libs/settingMer'
import ElementUI from 'element-ui'
import router from '../router'
import { roterPre } from '@/settings'
import Vue from 'vue'

function bindEvent(vm) {
  vm.$on('notice', function(data) {
    this.$notify.info({
      title: data.title || '消息',
      message: data.message,
      onClick() {
        console.log('click')
      }
    })
  })
}

function createWebScoket(token) {
  return new WebSocket(`${SettingMer.wsSocketUrl}?type=mer&token=${token}`)
}

function notice(token) {
  const ws = createWebScoket(token)
  const vm = new Vue()
  let ping

  function send(type, data) {
    ws.send(JSON.stringify({ type, data }))
  }

  ws.onopen = function() {
    vm.$emit('open')
    ping = setInterval(function() {
      send('ping')
    }, 10000)
  }

  ws.onmessage = function(res) {
    vm.$emit('message', res)
    const data = JSON.parse(res.data)
    if (data.status === 200) {
      vm.$emit(data.data.status, data.data.result)
    }
    if (data.type === 'notice') {
      const h = vm.$createElement
      ElementUI.Notification({
        title: data.data.data.title,
        message: h('a', { style: 'color: teal' }, data.data.data.message),
        onClick() {
          if (data.data.type === 'min_stock' || data.data.type === 'reply') {
            router.push({ path: `${roterPre}/product/list` })
          } else if (data.data.type === 'new_order') {
            router.push({ path: `${roterPre}/order/list` })
          } else if (data.data.type === 'new_refund_order') {
            router.push({ path: `${roterPre}/order/refund` })
          }
        }
      })
    }
  }
  ws.onclose = function(e) {
    vm.$emit('close', e)
    clearInterval(ping)
  }

  bindEvent(vm)

  return function() {
    ws.close()
  }
}

export default notice
