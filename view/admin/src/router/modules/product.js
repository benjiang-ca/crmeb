import Layout from '@/layout'
import { roterPre } from '@/settings'
const productRouter =
  {
    path: `${roterPre}/product`,
    name: 'product',
    meta: {
      icon: 'dashboard',
      title: '商品管理'
    },
    alwaysShow: true,
    component: Layout,
    children: [
      {
        path: 'classify',
        name: 'ProductClassify',
        meta: {
          title: '商品分类',
          noCache: true
        },
        component: () => import('@/views/product/productClassify')
      },
      {
        path: 'examine',
        name: 'ProductExamine',
        meta: {
          title: '商品管理',
          noCache: true
        },
        component: () => import('@/views/product/productExamine/index.vue')
      },
      {
        path: 'comment',
        name: 'ProductComment',
        meta: {
          title: '评论管理',
          noCache: true
        },
        component: () => import('@/views/product/productComment/index.vue')
      },
      {
        path: 'band',
        name: 'ProductBand',
        meta: {
          title: '品牌管理',
          noCache: true
        },
        component: () => import('@/views/product/band/index'),
        children: [
          {
            path: 'brandList',
            name: 'BrandList',
            meta: {
              title: '品牌列表',
              noCache: true
            },
            component: () => import('@/views/product/band/bandList')
          },
          {
            path: 'brandClassify',
            name: 'BrandClassify',
            meta: {
              title: '品牌分类',
              noCache: true
            },
            component: () => import('@/views/product/band/bandClassify')
          }
        ]
      }
    ]
  }

export default productRouter
