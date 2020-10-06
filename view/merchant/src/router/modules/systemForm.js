import Layout from '@/layout'
import { roterPre } from '@/settings'
const systemFormRouter =
    {
      path: `${roterPre}/systemForm`,
      name: 'system',
      meta: {
        icon: 'dashboard',
        title: '设置'
      },
      alwaysShow: true,
      component: Layout,
      children: [
        {
          path: 'Basics/:key?',
          component: () => import('@/views/systemForm/setSystem/index'),
          name: 'Basics',
          meta: { title: '店铺配置', noCache: true }
        },
        {
          path: 'modifyStoreInfo',
          component: () => import('@/views/systemForm/setSystem/modifyStoreInfo'),
          name: 'ModifyStoreInfo',
          meta: { title: ' 基础配置', noCache: true }
        },
        {
          path: 'systemStore',
          name: 'setting_systemStore',
          meta: {
            title: '提货点设置'
          },
          component: () => import('@/views/setting/systemStore/index')
        }
      ]
    }

export default systemFormRouter
