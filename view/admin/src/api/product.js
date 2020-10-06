import request from './request'

/**
 * @description 商品分类 -- 列表
 */
export function storeCategoryListApi() {
  return request.get('store/category/lst')
}
/**
 * @description 商品分类 -- 新增表单
 */
export function storeCategoryCreateApi() {
  return request.get('store/category/create/form')
}
/**
 * @description 商品分类 -- 编辑表单
 */
export function storeCategoryUpdateApi(id) {
  return request.get(`store/category/update/form/${id}`)
}
/**
 * @description 商品分类 -- 删除
 */
export function storeCategoryDeleteApi(id) {
  return request.delete(`store/category/delete/${id}`)
}
/**
 * @description 商品分类 -- 修改状态
 */
export function storeCategoryStatusApi(id, status) {
  return request.post(`store/category/status/${id}`, { status })
}
/**
 * @description 品牌分类 -- 列表
 */
export function brandCategoryListApi(data) {
  return request.get('store/brand/category/lst', data)
}
/**
 * @description 品牌分类 -- 新增表单
 */
export function brandCategoryCreateApi() {
  return request.get('store/brand/category/create/form')
}
/**
 * @description 品牌分类 -- 编辑表单
 */
export function brandCategoryUpdateApi(id) {
  return request.get(`store/brand/category/update/form/${id}`)
}
/**
 * @description 品牌分类 -- 删除
 */
export function brandCategoryDeleteApi(id) {
  return request.delete(`store/brand/category/delete/${id}`)
}
/**
 * @description 品牌分类 -- 修改状态
 */
export function brandCategoryStatusApi(id, status) {
  return request.post(`store/brand/category/status/${id}`, { status })
}
/**
 * @description 品牌 -- 列表
 */
export function brandListApi(data) {
  return request.get('store/brand/lst', data)
}
/**
 * @description 品牌 -- 新增表单
 */
export function brandCreateApi() {
  return request.get('store/brand/create/form')
}
/**
 * @description 品牌 -- 编辑表单
 */
export function brandUpdateApi(id) {
  return request.get(`store/brand/update/form/${id}`)
}
/**
 * @description 品牌 -- 删除
 */
export function brandDeleteApi(id) {
  return request.delete(`store/brand/delete/${id}`)
}
/**
 * @description 品牌列表 -- 修改状态
 */
export function brandStatusApi(id, status) {
  return request.post(`store/brand/status/${id}`, { status })
}
/**
 * @description 商品列表 -- 列表
 */
export function productLstApi(data) {
  return request.get(`store/product/lst`, data)
}
/**
 * @description 秒杀商品列表 -- 列表
 */
export function seckillProductLstApi(data) {
  return request.get(`seckill/product/lst`, data)
}
/**
 * @description 商品列表 -- 平台分类
 */
export function categoryListApi() {
  return request.get(`store/category/list`)
}
/**
 * @description 商品审核 -- 详情
 */
export function productDetailApi(id) {
  return request.get(`store/product/detail/${id}`)
}
/**
 * @description 秒杀商品审核 -- 详情
 */
export function seckillProductDetailApi(id) {
  return request.get(`seckill/product/detail/${id}`)
}
/**
 * @description 商品审核 -- 表单提交
 */
export function productStatusApi(data) {
  return request.post(`store/product/status`, data)
}
/**
 * @description 秒杀商品审核 -- 表单提交
 */
export function seckillProductStatusApi(data) {
  return request.post(`seckill/product/status`, data)
}
/**
 * @description 商品列表 -- 列表表头
 */
export function lstFilterApi() {
  return request.get(`store/product/lst_filter`)
}
/**
 * @description 秒杀商品列表 -- 列表表头
 */
export function seckillLstFilterApi() {
  return request.get(`seckill/product/lst_filter`)
}
/**
 * @description 商品评论 -- 列表
 */
export function replyListApi(data) {
  return request.get(`store/reply/lst`, data)
}
/**
 * @description 商品评论 -- 添加
 */
export function replyCreateApi(id) {
  return request.get(id ? `store/reply/create/form/${id}` : `store/reply/create/form`)
}
/**
 * @description 商品评论 -- 删除
 */
export function replyDeleteApi(id) {
  return request.delete(`store/reply/delete/${id}`)
}
/**
 * @description 商品评论商品列表 -- 列表
 */
export function goodLstApi(data) {
  return request.get(`store/product/list`, data)
}
/**
 * @description 商户总
 */
export function merSelectApi() {
  return request.get(`store/product/mer_select`)
}
/**
 * @description 秒杀商户总
 */
export function seckillMerSelectApi() {
  return request.get(`seckill/product/mer_select`)
}
/**
 * @description 商品下架
 */
export function productOffApi(data) {
  return request.post(`store/product/status`, data)
}
/**
 * @description 秒杀商品下架
 */
export function seckillProductOffApi(data) {
  return request.post(`seckill/product/status`, data)
}
/**
 * @description 商品编辑
 */
export function productUpdateApi(id, data) {
  return request.post(`store/product/update/${id}`, data)
}
/**
 * @description 秒杀商品编辑
 */
export function seckillProductUpdateApi(id, data) {
  return request.post(`seckill/product/update/${id}`, data)
}
/**
 * @description 商品列表 -- 显示隐藏
 */
export function changeApi(id, status) {
  return request.post(`store/product/change/${id}`, { status })
}
/**
 * @description 秒杀商品列表 -- 显示隐藏
 */
export function seckillChangeApi(id, status) {
  return request.post(`seckill/product/change/${id}`, { status })
}
