import Layout from '@/layout'
import { roterPre } from '@/settings'
const configRouter =
    {
      path: `${roterPre}/config`,
      name: 'system_config',
      meta: {
        icon: 'dashboard',
        title: '系统配置'
      },
      alwaysShow: true, // 一直显示根路由
      component: Layout,
      children: [
        {
          path: 'picture',
          name: 'system_config_picture',
          meta: {
            title: '素材管理'
          },
          component: () => import('@/views/system/config/picture')
        },
        {
          path: 'service',
          name: 'Service',
          meta: {
            title: '客服管理'
          },
          component: () => import('@/views/system/service/index')
        },
        {
          path: 'freight',
          name: 'Freight',
          meta: {
            title: '物流设置'
          },
          component: () => import('@/views/system/freight/index'),
          children: [
            {
              path: 'shippingTemplates',
              name: 'ShippingTemplates',
              meta: {
                title: '运费模板',
                noCache: true
              },
              component: () => import('@/views/system/freight/shippingTemplates')
            }
          ]
        }
      ]
    }
export default configRouter
