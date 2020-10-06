import Layout from '@/layout'
import { roterPre } from '@/settings'
const appRouter =
    {
      path: `${roterPre}/app`,
      name: 'app',
      meta: {
        title: '公众号'
      },
      alwaysShow: true,
      component: Layout,
      children: [
        {
          path: 'wechat/menus',
          name: 'wechatMenus',
          meta: {
            title: '微信菜单',
            noCache: true
          },
          component: () => import('@/views/app/wechat/menus/index')
        },
        {
          path: 'wechat/reply',
          name: 'wechatReply',
          meta: {
            title: '自动回复',
            noCache: true
          },
          component: () => import('@/views/app/wechat/reply/index'),
          children: [
            {
              path: 'follow/:key',
              name: 'wechatFollow',
              meta: {
                title: '微信关注回复',
                noCache: true
              },
              component: () => import('@/views/app/wechat/reply/follow')
            },
            {
              path: 'keyword',
              name: 'wechatKeyword',
              meta: {
                title: '关键字回复',
                noCache: true
              },
              component: () => import('@/views/app/wechat/reply/keyword')
            },
            {
              path: 'index/:key',
              name: 'wechatReplyIndex',
              meta: {
                title: '无效关键字回复',
                noCache: true
              },
              component: () => import('@/views/app/wechat/reply/follow')
            },
            {
              path: 'keyword/save/:id?',
              name: 'wechatKeywordAdd',
              meta: {
                title: '关键字添加',
                noCache: true,
                activeMenu: `${roterPre}/app/wechat/reply/keyword`
              },
              component: () => import('@/views/app/wechat/reply/follow')
            }
          ]
        },
        {
          path: 'wechat/newsCategory',
          name: 'newsCategory',
          meta: {
            title: '图文管理',
            noCache: true
          },
          component: () => import('@/views/app/wechat/newsCategory/index')
        },
        {
          path: 'wechat/newsCategory/save/:id?',
          name: 'newsCategorySave',
          meta: {
            title: '图文添加',
            noCache: true,
            activeMenu: `${roterPre}/app/wechat/newsCategory`
          },
          component: () => import('@/views/app/wechat/newsCategory/save')
        },
        {
          path: 'wechat/template',
          name: 'WechatTemplate',
          meta: {
            title: '微信模板消息',
            noCache: true
          },
          component: () => import('@/views/app/wechat/wxTemplate/index')
        }
      ]
    }

export default appRouter
