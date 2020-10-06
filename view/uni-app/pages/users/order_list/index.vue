<template>
	<view>
		<view class='my-order'>
			<view class='header bg-color'>
				<view class='picTxt acea-row row-between-wrapper'>
					<view class='text'>
						<view class='name'>订单信息</view>
						<view>消费订单：{{orderData.orderCount || 0}} 总消费：￥{{orderData.orderPrice || 0}}</view>
					</view>
					<view class='pictrue'>
						<image src='/static/images/orderTime.png'></image>
					</view>
				</view>
			</view>
			<view class='nav acea-row row-around'>
				<view class='item' :class='orderStatus==-1 ? "on": ""' @click="statusClick(-1)">
					<view>全部</view>
					<view class='num'>{{orderData.all || 0}}</view>
				</view>
				<view class='item' :class='orderStatus==0 ? "on": ""' @click="statusClick(0)">
					<view>待付款</view>
					<view class='num'>{{orderData.noPay || 0}}</view>
				</view>
				<view class='item' :class='orderStatus==1 ? "on": ""' @click="statusClick(1)">
					<view>待发货</view>
					<view class='num'>{{orderData.noPostage || 0}}</view>
				</view>
				<view class='item' :class='orderStatus==2 ? "on": ""' @click="statusClick(2)">
					<view>待收货</view>
					<view class='num '>{{orderData.noDeliver || 0}}</view>
				</view>
				<view class='item' :class='orderStatus==3 ? "on": ""' @click="statusClick(3)">
					<view>待评价</view>
					<view class='num'>{{orderData.noComment || 0}}</view>
				</view>
			</view>
			<view class='list'>
				<!-- 代付款 -->
				<block v-if="orderStatus == 0">
					<view class='item' v-for="(item,index) in orderList" :key="index">
						<view @click='goOrderDetails(item.group_order_id)'>
							<view class='title acea-row row-between-wrapper'>
								<view class="acea-row row-middle left-wrapper">
									{{item.group_order_sn}}
								</view>
								<view class='font-color'>待付款</view>
							</view>
							<view class='item-info acea-row row-between row-top' v-for="(order,j) in item.orderList" :key="order.order_id+j">
								<block v-for="(goods,g) in order.orderProduct">
									<view class='pictrue'>
										<image :src='goods.cart_info.product.image'></image>
									</view>
									<view class='text acea-row row-between'>
										<view class='name line2'>{{goods.cart_info.product.store_name}}</view>
										<view class='money'>
											<view>￥{{goods.cart_info.productAttr.price}}</view>
											<view>x{{goods.product_num}}</view>
										</view>
									</view>

								</block>
							</view>
							<view class='bottom acea-row row-right row-middle'>
							<!-- 	<view class='bnt cancelBnt' @click.stop='cancelOrder(index,item.group_order_id)'>取消订单</view> -->
								<view class='bnt bg-color' @click.stop='goPay(item.pay_price,item.group_order_id)'>立即付款</view>
							</view>
						</view>
					</view>
				</block>


				<!-- 待发货 待收货 待评价 已完成 -->
				<block v-else>
					<view class='item' v-for="(item,index) in orderList" :key="index">
						<view @click='goOrderDetails(item.order_id)'>
							<view class='title acea-row row-between-wrapper'>
								<view class="acea-row row-middle left-wrapper">
									<text class="iconfont icon-shangjiadingdan"></text>
									<view class="store-name">{{item.merchant.mer_name}}</view>
									<text class="iconfont icon-xiangyou"></text>
								</view>
								<view v-if="item.status == 0" class='font-color'>待发货</view>
								<view v-if="item.status == 1" class='font-color'>待收货</view>
								<view v-if="item.status == 2" class='font-color'>待评价</view>
								<view v-if="item.status == 3" class='font-color'>已完成</view>
								<view v-if="item.status == -1" class='font-color'>已退款</view>
							</view>
							<view class='item-info acea-row row-between row-top' v-for="(goods,index) in item.orderProduct" :key="index">
								<view class='pictrue'>
									<image :src='goods.cart_info.product.image'></image>
								</view>
								<view class='text acea-row row-between'>
									<view class='name '>
										<view class="line2">{{goods.cart_info.product.store_name}}</view>
										<view style="margin-top: 10rpx; color: red;">{{goods.is_refund==1?'退款中':goods.is_refund==2?'部分退款':goods.is_refund==3?'全部退款':''}}</view>
									</view>
									<view class='money'>
										<view>￥{{goods.cart_info.productAttr.price}}</view>
										<view>x{{goods.product_num}}</view>
									</view>
								</view>
							</view>
							<view class='totalPrice'>共{{item.orderNum || 0}}件商品，总金额
								<text class='money font-color'>￥{{item.pay_price}}</text>
							</view>
						</view>
						<view class='bottom acea-row row-right row-middle'>
							<block v-if="item.status == 0">
							<!-- 	<view class='bnt cancelBnt' @click='goOrderDetails(item.order_id)'>申请退款</view> -->
								<view class='bnt bg-color' @click='goOrderDetails(item.order_id)'>查看详情</view>
							</block>
							<block v-if="item.status == 1">
								<view class='bnt cancelBnt' @click='goOrderDetails(item.order_id)'>查看物流</view>
								<view class='bnt bg-color' @tap='confirmOrder(item,index)'>确认收货</view>
							</block>
							<block v-if="item.status == 2">
								<view class='bnt cancelBnt' @click='goOrderDetails(item.order_id)'>再次购买</view>
								<view class='bnt bg-color' @click='goOrderDetails_Evaluation(item.order_id)'>去评价</view>
							</block>
							<block v-if="item.status == 3">
								<!-- <view class="bnt cancelBnt"  @click="delOrder(item.order_id,index)">删除记录</view> -->
								<view class='bnt bg-color'>再次购买</view>
							</block>

							<!-- <view class='bnt cancelBnt' v-if="item._status._type==0 || item._status._type == 9" @click='cancelOrder(index,item.order_id)'>取消订单</view>
												<view class='bnt bg-color' v-if="item._status._type == 0" @click='goPay(item.pay_price,item.order_id)'>立即付款</view>
												<view class='bnt cancelBnt' v-if="item._status._type == 1" @click='cancelOrder(index,item.order_id)'>申请退款</view>
												<view class='bnt bg-color' v-if="item._status._type == 1 || item._status._type == 9" @click='goOrderDetails(item.order_id)'>查看详情</view>
												<view class='bnt cancelBnt' v-if="item._status._type == 2" @click='goOrderDetails(item.order_id)'>查看物流</view>
												<view class='bnt bg-color' v-if="item._status._type == 2" @click='goOrderDetails(item.order_id)'>确认收货</view>
												<view class='bnt cancelBnt' v-if="item._status._type == 3" @click='goOrderDetails(item.order_id)'>再次购买</view>
												<view class='bnt bg-color' v-if="item._status._type == 3" @click='goOrderDetails(item.order_id)'>去评价</view>
					
												<view class='bnt cancelBnt' v-if="item._status._type == 4" @click='delOrder(item.order_id,index)'>删除订单</view>
												<view class='bnt bg-color' v-if="item._status._type == 4" @click='goOrderDetails(item.order_id)'>再次购买</view> -->
						</view>
					</view>
				</block>
			</view>
			<view class='loadingicon acea-row row-center-wrapper' v-if="orderList.length>5">
				<text class='loading iconfont icon-jiazai' :hidden='loading==false'></text>{{loadTitle}}
			</view>
			<view v-if="orderList.length == 0">
				<emptyPage title="暂无订单~"></emptyPage>
			</view>
		</view>
		<view class='noCart' v-if="orderList.length == 0 && page > 1">
			<view class='pictrue'>
				<image src='/images/noOrder.png'></image>
			</view>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
		<payment :payMode='payMode' :pay_close="pay_close" @onChangeFun='onChangeFun' :order_id="pay_order_id" :totalPrice='totalPrice'></payment>
	</view>
</template>

<script>
	let app = getApp();
	import {
		getOrderList,
		orderData,
		unOrderCancel,
		orderDel,
		orderPay,
		groupOrderList,
		orderTake
	} from '@/api/order.js';
	import {
		getUserInfo
	} from '@/api/user.js';
	import {
		openOrderSubscribe
	} from '@/utils/SubscribeMessage.js';
	import payment from '@/components/payment';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	import emptyPage from '@/components/emptyPage.vue'
	export default {
		components: {
			payment,
			emptyPage,
			// #ifdef MP
			authorize
			// #endif
		},
		data() {
			return {
				loading: false, //是否加载中
				loadend: false, //是否加载完毕
				loadTitle: '加载更多', //提示语
				orderList: [], //订单数组
				orderData: {}, //订单详细统计
				orderStatus: 0, //订单状态
				page: 1,
				limit: 20,
				payMode: [{
						name: "微信支付",
						icon: "icon-weixinzhifu",
						value: 'wechat',
						title: '微信快捷支付',
						payStatus: 1
					},
					{
						name: "余额支付",
						icon: "icon-yuezhifu",
						value: 'balance',
						title: '可用余额:',
						number: 0,
						payStatus: app.globalData.yue_pay_status
					}
				],
				pay_close: false,
				pay_order_id: '',
				totalPrice: '0',
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false //是否隐藏授权
			};
		},
		computed: mapGetters(['isLogin']),
		onShow() {
			if (this.isLogin) {
				this.$set(this, 'orderList', []);
				this.page = 1;
				this.loadend = false;
				this.loading = false;
				this.getOrderData();
				this.getOrderList();
				this.getUserInfo();
			} else {
				// #ifdef H5 || APP-PLUS
				toLogin();
				// #endif 
				// #ifdef MP
				this.isAuto = true;
				this.$set(this, 'isShowAuth', true);
				// #endif
			}
		},
		methods: {
			onLoadFun() {
				this.isShowAuth = false;
				this.getOrderData();
				this.getOrderList();
				this.getUserInfo();
			},
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			/**
			 * 事件回调
			 * 
			 */
			onChangeFun: function(e) {
				let opt = e;
				let action = opt.action || null;
				let value = opt.value != undefined ? opt.value : null;
				(action && this[action]) && this[action](value);
			},
			/**
			 * 获取用户信息
			 * 
			 */
			getUserInfo: function() {
				let that = this;
				getUserInfo().then(res => {
					that.payMode[1].number = res.data.now_money;
					that.$set(that, 'payMode', that.payMode);
				});
			},
			/**
			 * 关闭支付组件
			 * 
			 */
			payClose: function() {
				this.pay_close = false;
			},
			/**
			 * 生命周期函数--监听页面加载
			 */
			onLoad: function(options) {
				if (options.status) this.orderStatus = options.status;
			},
			/**
			 * 获取订单统计数据
			 * 
			 */
			getOrderData: function() {
				let that = this;
				orderData().then(res => {
					that.$set(that, 'orderData', res.data);
				})
			},
			/**
			 * 取消订单
			 * 
			 */
			cancelOrder: function(index, order_id) {
				let that = this;
				if (!order_id) return that.$util.Tips({
					title: '缺少订单号无法取消订单'
				});
				unOrderCancel(order_id).then(res => {
					return that.$util.Tips({
						title: res.message,
						icon: 'success'
					}, function() {
						that.orderList.splice(index, 1);
						that.$set(that, 'orderList', that.orderList);
						that.$set(that.orderData, 'unpaid_count', that.orderData.unpaid_count - 1);
						that.getOrderData();
					});
				}).catch(err => {
					return that.$util.Tips({
						title: err
					});
				});
			},
			/**
			 * 打开支付组件
			 * 
			 */
			goPay: function(pay_price, order_id) {
				console.log(order_id)
				this.$set(this, 'pay_close', true);
				this.order_id = order_id;
				this.pay_order_id = order_id.toString()
				// this.$set(this, 'pay_order_id', );
				this.$set(this, 'totalPrice', pay_price);
			},
			/**
			 * 支付成功回调
			 * 
			 */
			pay_complete: function() {
				this.loadend = false;
				this.page = 1;
				this.$set(this, 'orderList', []);
				this.pay_close = false;
				this.pay_order_id = '';
				this.getOrderData();
				this.getOrderList();
			},
			/**
			 * 支付失败回调
			 * 
			 */
			pay_fail: function() {
				this.pay_close = false;
				this.pay_order_id = '';
				
			},
			/**
			 * 去订单详情
			 */
			goOrderDetails: function(order_id) {
				let self = this
				if (!order_id) return that.$util.Tips({
					title: '缺少订单号无法查看订单详情'
				});
				// #ifdef MP
				uni.showLoading({
					title: '正在加载',
				})
				openOrderSubscribe().then(() => {
					uni.hideLoading();
					if (self.orderStatus == 0) {
						uni.navigateTo({
							url: '/pages/order_details/stay?order_id=' + order_id
						})
					} else {
						uni.navigateTo({
							url: '/pages/order_details/index?order_id=' + order_id
						})
					}
				}).catch(() => {
					uni.hideLoading();
				})
				// #endif  
				// #ifndef MP
				if (self.orderStatus == 0) {
					uni.navigateTo({
						url: '/pages/order_details/stay?order_id=' + order_id
					})
				} else {
					uni.navigateTo({
						url: '/pages/order_details/index?order_id=' + order_id
					})
				}
				// #endif
			},
			/**
			 * 点击去评价
			 */
			goOrderDetails_Evaluation: function(order_id) {
				let self = this
				if (!order_id) return that.$util.Tips({
					title: '缺少订单号无法查看订单详情和评价'
				});
				// #ifdef MP			
					if (self.orderStatus == 0) {
						uni.navigateTo({
							url: '/pages/order_details/stay?order_id=' + order_id
						})
					} else {
						uni.navigateTo({
							url: '/pages/order_details/index?order_id=' + order_id
						})
					}				
				// #endif  
				// #ifndef MP
				if (self.orderStatus == 0) {
					uni.navigateTo({
						url: '/pages/order_details/stay?order_id=' + order_id
					})
				} else {
					uni.navigateTo({
						url: '/pages/order_details/index?order_id=' + order_id
					})
				}
				// #endif
			},
			/**
			 * 切换类型
			 */
			statusClick: function(status) {
				if (status == this.orderStatus) return;
				this.orderStatus = status;
				this.loadend = false;
				this.loading = false;
				this.page = 1;
				this.$set(this, 'orderList', []);
				this.getOrderList();
			},
			/**
			 * 获取订单列表
			 */
			getOrderList: function() {
				let that = this;
				if (that.loadend) return;
				if (that.loading) return;
				that.loading = true;
				that.loadTitle = "加载更多";
				if (that.orderStatus == 0) {
					groupOrderList({
						page: that.page,
						limit: that.limit,
					}).then(res => {
						let list = res.data.list || [];
						let loadend = list.length < that.limit;
						that.orderList = that.$util.SplitArray(list, that.orderList);
						that.$set(that, 'orderList', that.orderList);
						that.getProductCount();
						that.loadend = loadend;
						that.loading = false;
						that.loadTitle = loadend ? "我也是有底线的" : '加载更多';
						that.page = that.page + 1;
					})
				} else {
					getOrderList({
						status: that.orderStatus - 1,
						page: that.page,
						limit: that.limit,
					}).then(res => {
						let list = res.data.list || [];
						let loadend = list.length < that.limit;
						that.orderList = that.$util.SplitArray(list, that.orderList);
						that.$set(that, 'orderList', that.orderList);
						that.getProductCount();
						that.loadend = loadend;
						that.loading = false;
						that.loadTitle = loadend ? "我也是有底线的" : '加载更多';
						that.page = that.page + 1;
					}).catch(err => {
						that.loading = false;
						that.loadTitle = "加载更多";
					})
				}
				
			},
			/**
			 * 获取单个订单商品数量
			 */
			getProductCount: function(){	
				this.orderList.forEach((item,i) => {
					let orderNum = 0
					 item.orderProduct.forEach((val) => {
					      orderNum += val.product_num
					  })
					  this.orderList[i]['orderNum']=orderNum;
				 })				 
			},				
			/**
			 * 删除订单
			 */
			delOrder: function(order_id, index) {
				let that = this;
				orderDel(order_id).then(res => {
					that.orderList.splice(index, 1);
					that.$set(that, 'orderList', that.orderList);
					that.$set(that.orderData, 'unpaid_count', that.orderData.unpaid_count - 1);
					that.getOrderData();
					return that.$util.Tips({
						title: '删除成功',
						icon: 'success'
					});
				}).catch(err => {
					return that.$util.Tips({
						title: err
					});
				})
			},
			// 确认收货
			confirmOrder: function(item, index) {
				let that = this;
				uni.showModal({
					title: '确认收货',
					content: '为保障权益，请收到货确认无误后，再确认收货',
					success: function(res) {
						if (res.confirm) {
							orderTake(item.order_id).then(res => {
								return that.$util.Tips({
									title: '操作成功',
									icon: 'success'
								}, function() {
									that.orderList.splice(index, 1);
									that.getOrderData();
								});
							}).catch(err => {
								return that.$util.Tips({
									title: err
								});
							})
						}
					}
				})
			},
		},
		onReachBottom: function() {
			this.getOrderList();
		}
	}
</script>

<style scoped lang="scss">
	.my-order .header {
		height: 260rpx;
		padding: 0 30rpx;
	}

	.my-order .header .picTxt {
		height: 190rpx;
	}

	.my-order .header .picTxt .text {
		color: rgba(255, 255, 255, 0.8);
		font-size: 26rpx;
		font-family: 'Guildford Pro';
	}

	.my-order .header .picTxt .text .name {
		font-size: 34rpx;
		font-weight: bold;
		color: #fff;
		margin-bottom: 20rpx;
	}

	.my-order .header .picTxt .pictrue {
		width: 122rpx;
		height: 109rpx;
	}

	.my-order .header .picTxt .pictrue image {
		width: 100%;
		height: 100%;
	}

	.my-order .nav {
		background-color: #fff;
		width: 690rpx;
		height: 140rpx;
		border-radius: 6rpx;
		margin: -73rpx auto 0 auto;
	}

	.my-order .nav .item {
		text-align: center;
		font-size: 26rpx;
		color: #282828;
		padding: 29rpx 0;
	}

	.my-order .nav .item.on {
		font-weight: bold;
		border-bottom: 5rpx solid #e93323;
	}

	.my-order .nav .item .num {
		margin-top: 18rpx;
	}

	.my-order .list {
		width: 690rpx;
		margin: 14rpx auto 0 auto;
	}

	.my-order .list .item {
		background-color: #fff;
		border-radius: 6rpx;
		margin-bottom: 14rpx;
	}

	.my-order .list .item .title {
		height: 84rpx;
		padding: 0 30rpx;
		border-bottom: 1rpx solid #eee;
		font-size: 28rpx;
		color: #282828;

		.left-wrapper {
			.iconfont {
				margin-top: 5rpx;
			}

			.store-name {
				margin: 0 10rpx;
			}

			.icon-xiangyou {
				font-size: 20rpx;
			}
		}
	}

	.my-order .list .item .title .sign {
		font-size: 24rpx;
		padding: 0 7rpx;
		height: 36rpx;
		margin-right: 15rpx;
	}

	.my-order .list .item .item-info {
		padding: 0 30rpx;
		margin-top: 22rpx;
	}

	.my-order .list .item .item-info .pictrue {
		width: 120rpx;
		height: 120rpx;
	}

	.my-order .list .item .item-info .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 6rpx;
	}

	.my-order .list .item .item-info .text {
		width: 486rpx;
		font-size: 28rpx;
		color: #999;
		margin-top: 6rpx;
	}

	.my-order .list .item .item-info .text .name {
		width: 306rpx;
		color: #282828;
	}

	.my-order .list .item .item-info .text .money {
		text-align: right;
	}

	.my-order .list .item .totalPrice {
		font-size: 26rpx;
		color: #282828;
		text-align: right;
		margin: 27rpx 0 0 30rpx;
		padding: 0 30rpx 30rpx 0;
	}

	.my-order .list .item .totalPrice .money {
		font-size: 28rpx;
		font-weight: bold;
	}

	.my-order .list .item .bottom {
		height: 107rpx;
		padding: 0 30rpx;
		border-top: 1px solid #f0f0f0;
	}

	.my-order .list .item .bottom .bnt {
		width: 176rpx;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
		color: #fff;
		border-radius: 50rpx;
		font-size: 27rpx;
	}

	.my-order .list .item .bottom .bnt.cancelBnt {
		border: 1rpx solid #ddd;
		color: #aaa;
	}

	.my-order .list .item .bottom .bnt~.bnt {
		margin-left: 17rpx;
	}

	.noCart {
		margin-top: 171rpx;
		padding-top: 0.1rpx;
	}

	.noCart .pictrue {
		width: 414rpx;
		height: 336rpx;
		margin: 78rpx auto 56rpx auto;
	}

	.noCart .pictrue image {
		width: 100%;
		height: 100%;
	}
</style>
