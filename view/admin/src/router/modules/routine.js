import Layout from '@/layout'
import { roterPre } from '@/settings'
const routineRouter =
  {
    path: `${roterPre}/app/routine`,
    name: 'routine',
    meta: {
      title: '小程序'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'template',
        name: 'RoutineTemplate',
        meta: {
          title: '小程序订阅消息',
          noCache: true
        },
        component: () => import('@/views/app/wechat/wxTemplate/index')
      }
    ]
  }

export default routineRouter
