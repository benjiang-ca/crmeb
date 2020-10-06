import request from './request'

/**
 * @description 订单 -- 列表
 */
export function orderListApi(data) {
  return request.get('store/order/lst', data)
}

/**
 * @description 订单 -- 表头
 */
export function chartApi() {
  return request.get('store/order/chart')
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
  return request.get(`store/order/detail/${id}`)
}

/**
 * @description 订单 -- 记录
 */
export function orderLogApi(id, data) {
  return request.get(`store/order/log/${id}`, data)
}

/**
 * @description 订单 -- 备注from
 */
export function orderRemarkApi(id) {
  return request.get(`store/order/remark/${id}/form`)
}

/**
 * @description 订单 -- 删除
 */
export function orderDeleteApi(id) {
  return request.post(`store/order/delete/${id}`)
}
/**
 * @description 订单 -- 打印
 */
export function orderPrintApi(id) {
  return request.get(`store/order/printer/${id}`)
}

/**
 * @description 退款订单 -- 列表
 */
export function refundorderListApi(data) {
  return request.get('store/refundorder/lst', data)
}

/**
 * @description 退款订单 -- 详情
 */
export function refundorderDetailApi(id) {
  return request.get(`store/refundorder/detail/${id}`)
}

/**
 * @description 退款订单 -- 审核from
 */
export function refundorderStatusApi(id) {
  return request.get(`store/refundorder/status/${id}/form`)
}

/**
 * @description 退款订单 -- 备注from
 */
export function refundorderMarkApi(id) {
  return request.get(`store/refundorder/mark/${id}/form`)
}

/**
 * @description 退款订单 -- 记录from
 */
export function refundorderLogApi(id) {
  return request.get(`store/refundorder/log/${id}`)
}

/**
 * @description 退款订单 -- 删除
 */
export function refundorderDeleteApi(id) {
  return request.get(`store/refundorder/delete/${id}`)
}
/**
 * @description 退款订单 -- 确认收货
 */
export function confirmReceiptApi(id) {
  return request.post(`store/refundorder/refund/${id}`)
}


/**
 * @description 获取物流信息
 */
export function getExpress(id) {
  return request.get(`store/order/express/${id}`)
}

/**
 * @description 退款单获取物流信息
 */
export function refundorderExpressApi(id) {
  return request.get(`store/refundorder/express/${id}`)
}
/**
 * @description 导出订单
 */
export function exportOrderApi(data) {
  return request.get(`store/order/excel`, data)
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
 * @description 订单核销
 */
export function orderCancellationApi(code) {
  return request.post(`store/order/verify/${code}`)
}
/**
 * @description 订单 -- 头部
 */
export function orderHeadListApi() {
  return request.get(`store/order/filtter`)
}
/**
 * @description 核销订单 -- 表头
 */
export function takeChartApi() {
  return request.get('store/order/takechart')
}
/**
 * @description 核销订单 -- 列表
 */
export function takeOrderListApi(data) {
  return request.get('store/order/takelst', data)
}
