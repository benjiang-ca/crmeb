import Layout from '@/layout'
import { roterPre } from '@/settings'
const smsRouter =
  {
    path: `${roterPre}/sms`,
    name: 'sms',
    meta: {
      title: '短信管理'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'config',
        component: () => import('@/views/sms/smsConfig'),
        name: 'SmsConfig',
        meta: { title: '短信账户', noCache: true }
      },
      {
        path: 'template',
        component: () => import('@/views/sms/smsTemplate'),
        name: 'SmsTemplate',
        meta: { title: '短信模板', noCache: true }
      },
      {
        path: 'pay',
        component: () => import('@/views/sms/smsPay'),
        name: 'SmsPay',
        meta: { title: '短信购买', noCache: true }
      }
    ]
  }

export default smsRouter
