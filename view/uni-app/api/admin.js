import request from "@/utils/request.js";

/**
 * 统计数据
 */
export function getStatisticsInfo() {
  return request.get("admin/order/statistics", {}, { login: true });
}
/**
 * 订单月统计
 */
export function getStatisticsMonth(where) {
  return request.get("admin/order/data", where, { login: true });
}
/**
 * 订单月统计
 */
export function getAdminOrderList(where) {
  return request.get("admin/order/list", where, { login: true });
}
/**
 * 订单改价
 */
export function setAdminOrderPrice(id,data) {
  return request.post("admin/price/"+id, data, { login: true });
}
/**
 * 订单备注
 */
export function setAdminOrderRemark(id,data) {
  return request.post("admin/mark/" +id, data, { login: true });
}
/**
 * 订单详情
 */
export function getAdminOrderDetail(orderId) {
  return request.get("admin/order/" + orderId, {}, { login: true });
}
/**
 * 订单发货信息获取
 */
export function getAdminOrderDelivery(orderId) {
  return request.get( "admin/order/delivery/gain/" + orderId,{},{ login: true });
}

/**
 * 订单发货保存
 */
export function setAdminOrderDelivery(id,data) {
  return request.post("admin/delivery/"+ id, data, { login: true });
}
/**
 * 订单统计图
 */
export function getStatisticsTime(data) {
  return request.get("admin/order/time", data, { login: true });
}
/**
 * 线下付款订单确认付款
 */
export function setOfflinePay(data) {
  return request.post("admin/order/offline", data, { login: true });
}
/**
 * 订单确认退款
 */
export function setOrderRefund(data) {
  return request.post("admin/order/refund", data, { login: true });
}

/**
 * 获取快递公司
 * @returns {*}
 */
export function getLogistics() {
  return request.get("logistics", {}, { login: false });
}

/**
 * 订单核销
 * @returns {*}
 */
export function orderVerific(code) {
  return request.post("verifier/"+code);
}

/**
 * 核销订单详情
 * @returns {*}
 */
export function verifierOrder(code) {
  return request.get("verifier/order/"+code);
}

/**
 * 订单统计数
 * @returns {*}
 */
export function orderStatistics() {
  return request.get("admin/statistics");
}
/**
 * 每日成交额
 * @returns {*}
 */
export function orderPrice(where) {
  return request.get("admin/order_price", where, { login: true });
}
/**
 * 订单列表
 * @returns {*}
 */
export function getOrderList(where) {
  return request.get("admin/order_list", where, { login: true });
}
/**
 * 营业额统计
 * @returns {*}
 */
export function turnoverStatistics(where) {
  return request.get("admin/pay_price", where, { login: true });
}
/**
 * 订单统计
 * @returns {*}
 */
export function orderNumberStatistics(where) {
  return request.get("admin/pay_number", where, { login: true });
}

