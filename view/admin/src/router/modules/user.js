import Layout from '@/layout'
import { roterPre } from '@/settings'
const userRouter =
  {
    path: `${roterPre}/user`,
    name: 'user',
    meta: {
      title: '用户管理'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'group',
        component: () => import('@/views/user/group'),
        name: 'UserGroup',
        meta: { title: '用户分组', noCache: true }
      },
      {
        path: 'label',
        component: () => import('@/views/user/group'),
        name: 'UserLabel',
        meta: { title: '用户标签', noCache: true }
      },
      {
        path: 'list',
        component: () => import('@/views/user/list'),
        name: 'UserList',
        meta: { title: '用户列表', noCache: true }
      }
    ]
  }

export default userRouter
