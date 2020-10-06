import request from './request'

/**
 * @description 优惠券模板 -- 列表
 */
export function couponListApi(data) {
  return request.get('store/coupon/lst', data)
}
/**
 * @description 优惠券列表 -- 详情
 */
export function couponDetailApi(coupon_id) {
  return request.get(`store/coupon/detail/${coupon_id}`)
}
/**
 * @description 优惠券模板 -- 新增表单
 */
export function couponCreateApi() {
  return request.get('store/coupon/create/form')
}
/**
 * @description 优惠券模板 -- 编辑表单
 */
export function couponUpdateApi(id) {
  return request.get(`store/coupon/update/form/${id}`)
}
/**
 * @description 优惠券模板 -- 发布优惠券
 */
export function couponIssueApi(id) {
  return request.get(`store/coupon/issue/create/form/${id}`)
}

/**
 * @description 已发布优惠券 -- 列表
 */
export function couponIssueListApi(data) {
  return request.get('store/coupon/lst', data)
}
/**
 * @description 已发布优惠券 -- 修改状态
 */
export function couponIssueStatusApi(id, status) {
  return request.post(`store/coupon/status/${id}`, { status })
}
/**
 * @description 已发布优惠券 -- 添加优惠券
 */
export function couponIssuePushApi() {
  return request.get(`store/coupon/create/form`)
}
/**
 * @description 优惠券列表 -- 删除
 */
export function couponIssueDeleteApi(id) {
  return request.delete(`store/coupon/issue/${id}`)
}
/**
 * @description 优惠券列表 -- 复制
 */
export function couponCloneApi(id) {
  return request.get(`store/coupon/clone/form/${id}`)
}
/**
 * @description 优惠券列表 -- 领取记录
 */
export function issueApi(data) {
  return request.get(`store/coupon/issue`, data)
}
/**
 * @description 赠送优惠券组件列表 -- 列表
 */
export function couponSelectApi(data) {
  return request.get(`store/coupon/select`, data)
}
/**
 * @description 优惠劵 -- 删除
 */
export function couponDeleteApi(coupon_id) {
  return request.delete(`store/coupon/delete/${coupon_id}`)
}
/**
 * @description 直播间 -- 直播间列表
 */
export function broadcastListApi(data) {
  return request.get(`broadcast/room/lst`, data)
}
/**
 * @description 直播间 -- 修改回放状态
 */
export function changeReplayApi(id, data) {
  return request.post(`broadcast/room/live_status/${id}`, data)
}
/**
 * @description 直播间 -- 修改显示状态
 */
export function changeDisplayApi(id, data) {
  return request.post(`broadcast/room/status/${id}`, data)
}
/**
 * @description 直播间 -- 删除
 */
export function broadcastDeleteApi(id) {
  return request.delete(`broadcast/room/delete/${id}`)
}
/**
 * @description 直播间 -- 审核
 */
export function broadcastAuditApi(id) {
  return request.get(`broadcast/room/apply/form/${id}`)
}
/**
 * @description 直播间 -- 直播间详情
 */
export function broadcastDetailApi(id) {
  return request.get(`broadcast/room/detail/${id}`)
}
/**
 * @description 直播间 -- 备注
 */
export function broadcastRemarksApi(id, mark) {
  return request.post(`broadcast/room/mark/${id}`, { mark })
}
/**
 * @description 直播商品 -- 列表
 */
export function broadcastProListApi(data) {
  return request.get(`broadcast/goods/lst`, data)
}
/**
 * @description 直播间商品 -- 直播间商品详情
 */
export function broadcastProDetailApi(id) {
  return request.get(`broadcast/goods/detail/${id}`)
}
/**
 * @description 直播间商品 -- 修改显示状态
 */
export function changeProDisplayApi(id, data) {
  return request.post(`broadcast/goods/status/${id}`, data)
}
/**
 * @description 直播间商品 -- 审核
 */
export function applyBroadcastProApi(id) {
  return request.get(`broadcast/goods/apply/form/${id}`)
}
/**
 * @description 直播间商品 -- 秒杀配置
 */
export function spikeConfigurationApi() {
  return request.get(`seckill/config/create/form`)
}
/**
 * @description 直播间商品 -- 秒杀配置列表
 */
export function spikeConfigLstApi(data) {
  return request.get(`seckill/config/lst`, data)
}
/**
 * @description 直播间商品 -- 秒杀配置编辑
 */
export function spikeConfigUpdateApi(id) {
  return request.get(`seckill/config/update/${id}/form`)
}
/**
 * @description 直播间商品 -- 秒杀配置删除
 */
export function spikeConfigDeleteApi(id) {
  return request.delete(`seckill/config/delete/${id}`)
}
/**
 * @description 直播间商品 -- 秒杀配置编辑
 */
export function spikeConfigStatusApi(id, status) {
  return request.post(`seckill/config/status/${id}`, { status })
}
/**
 * @description 直播间 -- 直播间商品
 */
export function studioProList(id,data) {
  return request.get(`broadcast/room/goods/${id}`, data)
}
/**
 * @description 直播间商品 -- 删除
 */
export function broadcastProDeleteApi(broadcast_goods_id) {
  return request.delete(`broadcast/goods/delete/${broadcast_goods_id}`)
}
/**
 * @description 直播间 -- 编辑-排序
 */
export function broadcastRoomSortApi(broadcast_room_id,data) {
  return request.post(`broadcast/room/sort/${broadcast_room_id}`,data)
}
/**
 * @description 直播间商品 -- 编辑-排序
 */
export function broadcastProSortApi(broadcast_goods_id,data) {
  return request.post(`broadcast/goods/sort/${broadcast_goods_id}`,data)
}
