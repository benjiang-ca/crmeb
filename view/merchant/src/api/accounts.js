import request from './request'

/**
 * @description 财务对账 -- 对账单列表
 */
export function reconciliationListApi(data) {
  return request.get(`store/order/reconciliation/lst`, data)
}

/**
 * @description 财务对账 -- 确认打款
 */
export function reconciliationStatusApi(id, data) {
  return request.post(`store/order/reconciliation/status/${id}`, data)
}

/**
 * @description 财务对账 -- 查看订单
 */
export function reconciliationOrderApi(id, data) {
  return request.get(`store/order/reconciliation/${id}/order`, data)
}

/**
 * @description 财务对账 -- 退款订单
 */
export function reconciliationRefundApi(id, data) {
  return request.get(`store/order/reconciliation/${id}/refund`, data)
}

/**
 * @description 财务对账 -- 备注
 */
export function reconciliationMarkApi(id) {
  return request.get(`store/order/reconciliation/mark/${id}/form`)
}
/**
 * @description 资金流水 -- 列表
 */
export function capitalFlowLstApi(data) {
  return request.get(`financial_record/list`, data)
}
/**
 * @description 资金流水 -- 导出
 */
export function capitalFlowExportApi() {
  return request.get(`financial_record/export`)
}

