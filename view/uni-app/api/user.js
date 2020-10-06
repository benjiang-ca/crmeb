import request from "@/utils/request.js";

/**
 * 获取用户信息
 * 
*/
export function getUserInfo(){
  return request.get('user');
}

/**
 * 设置用户分享
 * 
*/
export function userShare(){
  return request.post('user/share');
}

/**
 * h5用户登录
 * @param data object 用户账号密码
 */
export function loginH5(data) {
  return request.post("auth/login", data, { noAuth : true });
}

/**
 * h5用户手机号登录
 * @param data object 用户手机号 也只能
 */
export function loginMobile(data) {
  return request.post("auth/smslogin", data, { noAuth : true });
}

/**
 * 验证码key
 */
export function getCodeApi() {
  return request.get("verify_code", {}, { noAuth: true });
}

/**
 * h5用户发送验证码
 * @param data object 用户手机号
 */
export function registerVerify(data) {
  return request.post("auth/verify", data, { noAuth : true });
}

/**
 * h5用户手机号注册
 * @param data object 用户手机号 验证码 密码
 */
export function register(data) {
  return request.post("/register", data, { noAuth : true });
}

/**
 * 用户手机号修改密码
 * @param data object 用户手机号 验证码 密码
 */
export function registerReset(data) {
  return request.post("/register/reset", data, { noAuth: true });
}

/**
 * 获取用户中心菜单
 *
 */
export function getMenuList() {
  return request.get("common/menus");
}

/*
 * 签到用户信息
 * */
export function postSignUser(sign) {
  return request.post("sign/user", sign);
}

/**
 * 获取签到配置
 * 
*/
export function getSignConfig(){
  return request.get('sign/config')
}

/**
 * 获取签到列表
 * @param object data
*/
export function getSignList(data){
  return request.get('sign/list',data);
}

/**
 * 用户签到
*/
export function setSignIntegral(){
  return request.post('sign/integral')
}

/**
 * 签到列表(年月)
 * @param object data
 * 
*/
export function getSignMonthList(data){
  return request.get('sign/month',data)
}

/**
 * 活动状态
 * 
*/
export function userActivity(){
  return request.get('user/activity');
}

/*
 * 资金明细（types|0=全部,1=消费,2=充值,3=返佣）
 * */
export function getCommissionInfo(q, types) {
  return request.get("user/bill", q);
}


/*
 * 提现列表
 * */
export function extractLst(data) {
  return request.get("user/extract/lst", data);
}


/*
 * 积分记录
 * */
export function getIntegralList(q) {
  return request.get("integral/list", q);
}

/**
 * 获取分销海报图片
 * 
*/
export function spreadBanner(){
	//#ifdef H5
	return request.get('user/spread_image',{type:'wechat'});
	//#endif
	//#ifdef MP
	 return request.get('user/spread_image',{type:'routine'});
	//#endif

}

/**
 *
 * 获取推广用户一级和二级
 * @param object data
*/
export function spreadPeople(data){
  return request.get('user/spread_list',data);
}

/**
 * 
 * 推广佣金/提现总和
 * @param int type
*/
export function spreadCount(type){
  return request.get('spread/count/'+type);
}

/*
 * 推广数据
 * */
export function getSpreadInfo() {
  return request.get("/commission");
}


/**
 * 
 * 推广订单
 * @param object data
*/
export function spreadOrder(data){
  return request.get('user/spread_order',data);
}

/*
 * 获取推广人排行
 * */
export function getRankList(data) {
  return request.get("user/spread_top",data);
}

/*
 * 获取佣金排名
 * */
export function getBrokerageRank(q) {
  return request.get("user/brokerage_top", q);
}

/**
 * 提现申请
 * @param object data
*/
export function extractCash(data){
  return request.post('user/extract/create',data)
}

/**
 * 提现银行/提现最低金额
 * 
*/
export function extractBank(){
  return request.get('user/extract/banklst');
}

/**
 * 会员等级列表
 * 
*/
export function userLevelGrade(){
  return request.get('user/level/grade');
}

/**
 * 获取某个等级任务
 * @param int id 任务id
*/
export function userLevelTask(id){
  return request.get('user/level/task/'+id);
}

/**
 * 检查用户是否可以成为会员
 * 
*/
export function userLevelDetection(){
  return request.get('user/level/detection');
}

/**
 * 
 * 地址列表
 * @param object data
*/
export function getAddressList(data){
  return request.get('user/address/lst',data);
}

/**
 * 设置默认地址
 * @param int id
*/
export function setAddressDefault(id){
  return request.post('user/address/update/'+id)
}

/**
 * 修改 添加地址
 * @param object data
*/
export function editAddress(data){
  return request.post('user/address/create',data);
}

/**
 * 删除地址
 * @param int id
 * 
*/
export function delAddress(id){
  return request.post('user/address/delete/'+id)
}

/**
 * 获取单个地址
 * @param int id 
*/
export function getAddressDetail(id){
  return request.get('user/address/detail/'+id);
}

/**
 * 修改用户信息
 * @param object
*/
export function userEdit(data){
  return request.post('user/edit',data);
}

/*
 * 退出登录
 * */
export function getLogout() {
  return request.post("logout");
}

/**
 * 佣金转入
 * 
*/
export function rechargeBrokerage(data){
  return request.post('user/recharge/brokerage',data)
}

/**
 * 小程序充值
 * 
*/
export function rechargeRoutine(data){
  return request.post('recharge/routine',data)
}
/*
 * 公众号充值
 * */
export function rechargeWechat(data) {
  return request.post("user/recharge", data);
}
/**
 * 获取默认地址
 * 
*/
export function getAddressDefault(){
  return request.get('address/default');
}

/**
 * 充值金额选择
 */
export function getRechargeApi() {
  return request.get("common/recharge_quota");
}

/**
 * 登陆记录
 */
export function setVisit(data)
{
  return request.post('user/set_visit', {...data}, { noAuth:true});
}

/**
 * 客服列表
 */
export function serviceList() {
  return request.get("service/list");
}

/**
 * 客服获取客户列表
 */
export function serviceUserList() {
  return request.get("service/user_list");
}

/**
 * 用户获取聊天记录详情
 */
export function getChatRecord(to_uid, data) {
  return request.get("service/history/" + to_uid, data);
}

/**
 * 客服获取聊天记录详情
 */
export function getMerHistory(userid,mer_id, data) {
  return request.get("service/mer_history/" + mer_id+'/'+userid, data);
}




/**
 * 静默绑定推广人
 * @param {Object} puid
 */
export function spread(puid)
{
	return request.post("user/spread",{puid:puid});
}

/**
 * 反馈类型
 */
export function feedbackType() {
  return request.get("common/feedback_type");
}

/**
 * 提交反馈
 */
export function feedback(data) {
  return request.post("user/feedback",{...data});
}

/**
 * 反馈列表
 */
export function feedbackList(data) {
  return request.get("user/feedback/list",data);
}

/**
 * 反馈列表
 */
export function feedbackDetail(id) {
  return request.get("user/feedback/detail/"+id);
}

/**
 * 浏览记录
 */
export function historyList(data) {
  return request.get("user/history",data);
}

/**
 * 删除浏览记录
 */
export function historyDelete(id) {
  return request.post("user/history/delete/"+id);
}

/**
 * 佣金记录
 */
export function brokerage_list(id) {
  return request.get("user/brokerage_list");
}

/**
 * 佣金数据
 */
export function spreadInfo() {
  return request.get("user/spread_info");
}

// 图片验证码
export function getCaptcha() {
  return request.get('captcha', { noAuth: true });
}

// 用户账户列表
export function userAcc() {
  return request.get('user/account', { noAuth: true });
}
