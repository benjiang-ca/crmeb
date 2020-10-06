import Layout from '@/layout'
import { roterPre } from '@/settings'
const merchantRouter =
    {
      path: `${roterPre}/merchant`,
      name: 'merchant',
      meta: {
        icon: 'dashboard',
        title: '商户管理'
      },
      alwaysShow: true,
      component: Layout,
      children: [
        {
          path: 'system',
          name: 'MerchantSystem',
          meta: {
            title: '商户权限管理',
            noCache: true
          },
          component: () => import('@/views/merchant/system/index')
        },
        {
          path: 'list',
          name: 'MerchantList',
          meta: {
            title: '商户列表',
            noCache: true
          },
          component: () => import('@/views/merchant/list/index')
        },
        // type,1显示操作，0不显示操作
        {
          path: 'list/reconciliation/:id/:type?',
          name: 'MerchantRecord',
          component: () => import('@/views/merchant/list/record'),
          meta: {
            title: '商户对账',
            noCache: true,
            activeMenu: `${roterPre}/merchant/list`
          },
          hidden: true
        },
        {
          path: 'classify',
          name: 'MerchantClassify',
          meta: {
            title: '商户分类',
            noCache: true
          },
          component: () => import('@/views/merchant/classify')
        },
        {
          path: 'application',
          name: 'MerchantApplication',
          meta: {
            title: '商户申请',
            noCache: true
          },
          component: () => import('@/views/merchant/application')
        },
        {
          path: 'agree',
          name: 'MerchantAgreement',
          meta: {
            title: '入驻协议',
            noCache: true
          },
          component: () => import('@/views/merchant/agreement')
        }
      ]
    }

export default merchantRouter
