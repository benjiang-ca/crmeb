import Element from 'element-ui'
import '@/styles/element-variables.scss'
import newsCategoryFromComponent from './index.vue'
import Vue from 'vue'
import Cookies from 'js-cookie'
Vue.use(Element, {
  size: Cookies.get('size') || 'medium' // set element-ui default size
})
const newsCategoryFrom = {}
newsCategoryFrom.install = function(Vue, options) {
  const ToastConstructor = Vue.extend(newsCategoryFromComponent)
  // 生成一个该子类的实例
  const instance = new ToastConstructor()
  instance.$mount(document.createElement('div'))
  document.body.appendChild(instance.$el)
  Vue.prototype.$modalNewsCategory = function() {
    instance.visible = true
    // instance.callback = callback
  }
}
export default newsCategoryFrom
