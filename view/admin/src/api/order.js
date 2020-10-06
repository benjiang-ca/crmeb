import request from './request'

/**
 * @description 订单 -- 列表
 */
export function orderListApi(data) {
  return request.get('order/lst', data)
}

/**
 * @description 订单 -- 表头
 */
export function chartApi() {
  return request.get('order/chart')
}

/**
 * @description 订单 -- 编辑
 */
export function orderUpdateApi(id) {
  return request.get(`store/order/update/${id}/form`)
}

/**
 * @description 订单 -- 发货
 */
export function orderDeliveryApi(id) {
  return request.get(`store/order/delivery/${id}/form`)
}

/**
 * @description 订单 -- 详情
 */
export function orderDetailApi(id) {
  return request.get(`order/detail/${id}`)
}

/**
 * @description 退款订单 -- 列表
 */
export function refundorderListApi(data) {
  return request.get('order/refund/lst', data)
}

/**
 * @description 获取物流信息
 */
export function getExpress(id) {
  return request.get(`order/express/${id}`)
}
/**
 * @description 导出订单
 */
export function exportOrderApi(data) {
  return request.get(`order/excel`,  data )
}
/**
 * @description 导出文件列表
 */
export function exportFileLstApi(data) {
  return request.get(`excel/lst`, data)
}
/**
 * @description 下载
 */
export function downloadFileApi(id) {
  return request.get(`excel/download/${id}`)
}
/**
 * @description 核销订单 -- 表头
 */
export function takeChartApi() {
  return request.get('order/takechart')
}
/**
 * @description 核销订单 -- 列表
 */
export function takeOrderListApi(data) {
  return request.get('order/takelst', data)
}