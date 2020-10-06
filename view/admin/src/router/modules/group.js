import Layout from '@/layout'
import { roterPre } from '@/settings'
const groupRouter =
    {
      path: `${roterPre}/group`,
      name: 'SystemGroup',
      meta: {
        icon: 'dashboard',
        title: '组合数据'
      },
      alwaysShow: true,
      component: Layout,
      children: [
        {
          path: 'list',
          name: 'SystemGroupList',
          meta: {
            title: '组合数据'
          },
          component: () => import('@/views/system/groupData/list')
        },
        {
          path: 'data/:id?',
          name: 'SystemGroupData',
          meta: {
            title: '组合数据列表',
            activeMenu: `${roterPre}/group/list`
          },
          component: () => import('@/views/system/groupData/data')
        }
      ]
    }

export default groupRouter
