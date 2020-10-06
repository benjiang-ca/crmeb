import Layout from '@/layout'
import { roterPre } from '@/settings'
const accountsRouter =
  {
    path: `${roterPre}/accounts`,
    name: 'accounts',
    meta: {
      icon: '',
      title: '财务'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'reconciliation',
        name: 'AccountsReconciliation',
        meta: {
          title: '财务对账',
          noCache: true
        },
        component: () => import('@/views/accounts/reconciliation/index')
      },
      {
        path: 'reconciliation/order/:id',
        name: 'ReconciliationOrder',
        component: () => import('@/views/accounts/reconciliation/record'),
        meta: {
          title: '查看订单',
          noCache: true,
          activeMenu: `${roterPre}/accounts/reconciliation`
        },
        hidden: true
      },
      {
        path: 'capitalFlow',
        name: 'AccountsCapitalFlow',
        meta: {
          title: '资金流水',
          noCache: true
        },
        component: () => import('@/views/accounts/capitalFlow/index')
      }
    ]
  }
export default accountsRouter
