import SettingMer from '@/libs/settingMer'
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
  return new WebSocket(`${SettingMer.wsSocketUrl}?type=admin&token=${token}`)
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
    console.log(res)
  }
  ws.onclose = function(e) {
    vm.$emit('close', e)
    console.log('on close')
    clearInterval(ping)
  }

  bindEvent(vm)

  return function() {
    ws.close()
  }
}

export default notice
