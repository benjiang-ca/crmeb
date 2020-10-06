import Layout from '@/layout'
import { roterPre } from '@/settings'
const promoterRouter =
  {
    path: `${roterPre}/promoter`,
    name: 'promoter',
    meta: {
      icon: '',
      title: '设置'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'config',
        name: 'PromoterConfig',
        meta: {
          title: '分销配置',
          noCache: true
        },
        component: () => import('@/views/promoter/config/index')
      },
      {
        path: 'user',
        name: 'AccountsUser',
        meta: {
          title: '分销员列表',
          noCache: true
        },
        component: () => import('@/views/promoter/user/index')
      },
      {
        path: 'bank/:id?',
        name: 'PromoterBank',
        meta: {
          title: '页面设置',
          noCache: true
        },
        component: () => import('@/views/system/groupData/data')
      },
      {
        path: 'gift',
        name: 'AccountsGift',
        meta: {
          title: '分销礼包',
          noCache: true
        },
        component: () => import('@/views/promoter/gift/index')
      }
    ]
  }

export default promoterRouter
