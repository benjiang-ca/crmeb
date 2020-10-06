import Vue from 'vue'

import Cookies from 'js-cookie'

import 'normalize.css/normalize.css' // a modern alternative to CSS resets

// 懒加载
import VueLazyload from 'vue-lazyload'

import Element from 'element-ui'
import cascader from 'element-ui/lib/cascader';
import './styles/element-variables.scss'

import '@/styles/index.scss' // global css

import App from './App'
import store from './store'
import router from './router'
import FormCreate from '@form-create/element-ui'
import uploadPicture from './components/uploadPicture/uploadFrom'
import VueUeditorWrap from 'vue-ueditor-wrap'
import newsCategory from './components/newsCategory/newsCategoryFrom'
// import { getToken } from './utils/auth'

import './icons' // icon
import './permission' // permission control
// import './utils/error-log' // error log
import modalForm from '@/libs/modal-form'
import { modalSure } from '@/libs/public'
import { modalSureDelete} from '@/libs/public'
import * as filters from './filters'
// import notice from '@/libs/notice'
// swiper
import VueAwesomeSwiper from 'vue-awesome-swiper'
import 'swiper/dist/css/swiper.css'

Vue.use(uploadPicture)
Vue.use(FormCreate)
Vue.use(newsCategory)
Vue.component('vue-ueditor-wrap', VueUeditorWrap)
Vue.use(VueAwesomeSwiper)
Vue.use(VueLazyload, {
  preLoad: 1.3,
  error: require('./assets/images/no.png'),
  loading: require('./assets/images/moren.jpg'),
  attempt: 1,
  listenEvents: ['scroll', 'wheel', 'mousewheel', 'resize', 'animationend', 'transitionend', 'touchmove']
})

Vue.prototype.$modalForm = modalForm
Vue.prototype.$modalSure = modalSure
Vue.prototype.$modalSureDelete = modalSureDelete
/**
 * If you don't want to use mock-server
 * you want to use MockJs for mock api
 * you can execute: mockXHR()
 *
 * Currently MockJs will be used in the production environment,
 * please remove it before going online ! ! !
 */
if (process.env.NODE_ENV === 'production') {
  const { mockXHR } = require('../mock')
  mockXHR()
}

Vue.use(Element, {
  size: Cookies.get('size') || 'medium' // set element-ui default size
})
Vue.use(cascader);

// register global utility filters
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key])
})

// const token = getToken()
// let _notice
// if (token) {
//   _notice = notice(token)
// }
router.beforeEach((to, from, next) => {
  /* 路由发生变化修改页面title */
  if (to.meta.title) {
    document.title = to.meta.title + '-' + JSON.parse(Cookies.get('MerInfo')).login_title
  }
  next();
})

Vue.config.productionTip = false
export default new Vue({
  el: '#app',
  router,
  data: {
    notice: null
  },
  methods: {
    closeNotice() {
      this.notice && this.notice()
    }
  },
  store,
  render: h => h(App)
})
