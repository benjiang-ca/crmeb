import Layout from '@/layout'
import { roterPre } from '@/settings'
const systemFormRouter =
    {
      path: `${roterPre}/systemForm`,
      name: 'system',
      meta: {
        icon: 'dashboard',
        title: '商城设置'
      },
      alwaysShow: true,
      component: Layout,
      children: [
        {
          path: 'Basics/:key?',
          component: () => import('@/views/systemForm/setSystem/index'),
          name: 'Basics',
          meta: { title: '基础配置' }
        }
      ]
    }

export default systemFormRouter
