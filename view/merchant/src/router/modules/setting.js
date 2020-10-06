import Layout from '@/layout'
import { roterPre } from '@/settings'
const settingRouter =
    {
      path: `${roterPre}/setting`,
      name: 'system_group',
      meta: {
        icon: 'dashboard',
        title: '权限管理'
      },
      component: Layout,
      children: [
        {
          path: 'systemRole',
          name: 'setting_role',
          meta: {
            title: '身份管理'
          },
          component: () => import('@/views/setting/systemRole/index')
        },
        {
          path: 'systemAdmin',
          name: 'setting_systemAdmin',
          meta: {
            title: '管理员管理'
          },
          component: () => import('@/views/setting/systemAdmin/index')
        },
        {
          path: 'systemLog',
          name: 'setting_systemLog',
          meta: {
            title: '操作日志'
          },
          component: () => import('@/views/setting/systemLog/index')
        }
      ]
    }

export default settingRouter
