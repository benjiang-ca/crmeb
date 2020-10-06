import Layout from '@/layout'
import { roterPre } from '@/settings'
const marketingRouter =
{
  path: `${roterPre}/marketing`,
  name: 'marketing',
  meta: {
    title: '营销'
  },
  alwaysShow: true,
  component: Layout,
  redirect: 'noRedirect',
  children: [
    {
      path: 'coupon',
      name: 'Coupon',
      meta: {
        title: '优惠券',
        noCache: true
      },
      redirect: 'noRedirect',
      component: () => import('@/views/marketing/coupon/index'),
      children: [
        {
          path: 'list',
          name: 'CouponList',
          meta: {
            title: '优惠劵列表',
            noCache: true
          },
          component: () => import('@/views/marketing/coupon/couponList/index')
        },
        {
          path: 'user',
          name: 'CouponUser',
          meta: {
            title: '会员领取记录',
            noCache: true
          },
          component: () => import('@/views/marketing/coupon/couponUser/index')
        },
        {
          path: 'creatCoupon/:id?',
          name: 'CreatCoupon',
          meta: {
            title: '添加优惠劵',
            noCache: true,
            activeMenu: `${roterPre}/marketing/coupon/list`
          },
          component: () => import('@/views/marketing/coupon/couponList/creatCoupon')
        }
      ]
    },
    {
      path: 'studio',
      name: 'Studio',
      meta: {
        title: '直播间',
        noCache: true
      },
      redirect: 'noRedirect',
      component: () => import('@/views/marketing/studio/index'),
      children: [
        {
          path: 'list',
          name: 'StudioList',
          meta: {
            title: '直播间列表',
            noCache: true
          },
          component: () => import('@/views/marketing/studio/studioList/index')
        },
        {
          path: 'creatStudio',
          name: 'CreatStudio',
          meta: {
            title: '创建直播间',
            noCache: true
          },
          component: () => import('@/views/marketing/studio/studioList/creatStudio')
        }
      ]
    },
    {
      path: 'broadcast',
      name: 'Broadcast',
      meta: {
        title: '直播',
        noCache: true
      },
      redirect: 'noRedirect',
      component: () => import('@/views/marketing/broadcast/index'),
      children: [
        {
          path: 'list',
          name: 'BroadcastList',
          meta: {
            title: '直播商品列表',
            noCache: true
          },
          component: () => import('@/views/marketing/broadcast/broadcastList/index')
        },
        {
          path: 'addProduct',
          name: 'BroadcastProduct',
          meta: {
            title: '创建直播商品',
            noCache: true
          },
          component: () => import('@/views/marketing/broadcast/broadcastList/addProduct')
        }
      ]
    },
    {
      path: 'seckill',
      name: 'Seckill',
      meta: {
        title: '秒杀管理',
        noCache: true
      },
      redirect: 'noRedirect',
      component: () => import('@/views/marketing/seckill/index'),
      children: [
        {
          path: 'list',
          name: 'SpikeGoods',
          meta: {
            title: '秒杀商品',
            noCache: true
          },
          component: () => import('@/views/marketing/seckill/seckillGoods/index')
        },
        {
          path: 'createGoods/:id?/:edit?',
          name: 'CreateSpikeGoods',
          meta: {
            title: '添加秒杀商品',
            noCache: true
          },
          component: () => import('@/views/marketing/seckill/seckillGoods/createGoods')
        }
      ]
    }
  ]
}

export default marketingRouter
