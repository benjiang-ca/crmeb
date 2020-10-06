import Layout from '@/layout'
import { roterPre } from '@/settings'
const groupRouter =
    {
      path: `${roterPre}/group`,
      name: 'system_group',
      meta: {
        icon: 'dashboard',
        title: '组合数据'
      },
      alwaysShow: true,
      component: Layout,
      children: [
        {
          path: 'list',
          name: 'system_group_lst',
          meta: {
            title: '组合数据'
          },
          component: () => import('@/views/system/groupData/list')
        },
        {
          path: 'data/:id?',
          name: 'system_group_data',
          meta: {
            title: '组合数据列表'
          },
          component: () => import('@/views/system/groupData/data')
        }
      ]
    }

export default groupRouter
