import Vue from 'vue'
import Router from 'vue-router'
import { roterPre } from '@/settings'
Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/* Router Modules */
import configRouter from './modules/config'
import systemFormRouter from './modules/systemForm'
import settingRouter from './modules/setting'
import merchantRouter from './modules/merchant'
import groupRouter from './modules/group'
import appRouter from './modules/app'
import cmsRouter from './modules/cms'
import productRouter from './modules/product'
import userRouter from './modules/user'
import smsRouter from './modules/sms'
import maintainRouter from './modules/maintain'
import freightRouter from './modules/freight'
import userFeedbackRouter from './modules/userFeedback'
import accountsRouter from './modules/accounts'
import promoterRouter from './modules/promoter'
import orderRouter from './modules/order'
import routineRouter from './modules/routine'
import safeRouter from './modules/safe'
import marketingRouter from './modules/marketing'

export const constantRoutes = [
  configRouter,
  systemFormRouter,
  settingRouter,
  merchantRouter,
  groupRouter,
  appRouter,
  cmsRouter,
  productRouter,
  userRouter,
  smsRouter,
  maintainRouter,
  freightRouter,
  userFeedbackRouter,
  accountsRouter,
  promoterRouter,
  orderRouter,
  routineRouter,
  safeRouter,
  marketingRouter,
  {
    path: roterPre,
    component: Layout,
    redirect: `${roterPre}/dashboard`,
    children: [
      {
        path: `${roterPre}/dashboard`,
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: '控制台', icon: 'dashboard', affix: true }
      }
    ]
  },
  {
    path: '/',
    component: Layout,
    redirect: `${roterPre}/dashboard`,
    children: [
      {
        path: `${roterPre}/dashboard`,
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: '控制台', icon: 'dashboard', affix: true }
      }
    ]
  },
  {
    path: `${roterPre}/login`,
    component: () => import('@/views/login/index'),
    hidden: true
  },
  // {
  //   path: '/auth-redirect',
  //   component: () => import('@/views/login/auth-redirect'),
  //   hidden: true
  // },
  {
    path: '/error',
    component: Layout,
    redirect: 'noRedirect',
    name: 'ErrorPages',
    meta: {
      title: 'Error Pages',
      icon: '404'
    },
    children: [
      {
        path: '401',
        component: () => import('@/views/error-page/401'),
        name: 'Page401',
        meta: { title: '401', noCache: true }
      },
      {
        path: '404',
        component: () => import('@/views/error-page/404'),
        name: 'Page404',
        meta: { title: '404', noCache: true }
      }
    ]
  },
  {
    path: roterPre + '/404',
    component: () => import('@/views/error-page/404'),
    hidden: true
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true
  },
  {
    path: roterPre + '/setting/icons',
    component: () => import('@/components/iconFrom/index'),
    name: 'icons'
  },
  {
    path: roterPre + '/setting/uploadPicture',
    component: () => import('@/components/uploadPicture/index.vue'),
    name: 'uploadPicture'
  },
  {
    path: roterPre + '/setting/storeProduct',
    component: () => import('@/components/goodList/index.vue'),
    name: 'StoreProduct'
  },
  { path: '*', redirect: roterPre + '/404', hidden: true }
]

const createRouter = () => new Router({
  mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
