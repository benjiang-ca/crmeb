import Layout from '@/layout'
import { roterPre } from '@/settings'
const productRouter =
  {
    path: `${roterPre}/product`,
    name: 'product',
    component: Layout,
    meta: {
      icon: 'dashboard',
      title: '商品管理'
    },
    alwaysShow: true,
    redirect: 'noRedirect',
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
        path: 'attr',
        name: `ProductAttr`,
        meta: {
          title: '商品规格',
          noCache: true
        },
        component: () => import('@/views/product/productAttr')
      },
      {
        path: 'list',
        name: `ProductList`,
        meta: {
          title: '商品列表',
          noCache: true
        },
        component: () => import('@/views/product/productList')
      },
      {
        path: 'list/addProduct/:id?/:edit?',
        component: () => import('@/views/product/addProduct/index'),
        name: 'AddProduct',
        meta: { title: '商品添加', noCache: true, activeMenu: `${roterPre}/product/list` },
        hidden: true
      },
      {
        path: 'reviews',
        name: 'ProductReviews',
        meta: {
          title: '商品评论'
        },
        component: () => import('@/views/product/Reviews/index')
      }
    ]
  }

export default productRouter
