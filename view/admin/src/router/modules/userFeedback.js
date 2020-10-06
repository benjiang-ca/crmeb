import Layout from '@/layout'
import { roterPre } from '@/settings'
const userFeedbackRouter =
  {
    path: `${roterPre}/feedback`,
    name: 'Feedback',
    meta: {
      icon: 'dashboard',
      title: '用户反馈管理'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'classify',
        name: 'FeedbackClassify',
        meta: {
          title: '反馈分类',
          noCache: true
        },
        component: () => import('@/views/userFeedback/classify/index')
      },
      {
        path: 'list',
        name: 'FeedbackList',
        meta: {
          title: '反馈列表',
          noCache: true
        },
        component: () => import('@/views/userFeedback/list/index')
      }
    ]
  }

export default userFeedbackRouter
