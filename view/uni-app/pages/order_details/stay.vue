<template>
	<view>
		<view class='order-details'>
			<!-- 给header上与data上加on为退款订单-->
			<view class='header bg-color acea-row row-middle' >
				<view class='pictrue'>
					<image :src="imgUrl+'/static/order_1.gif'"></image>
				</view>
				<view class='data'>
					<view class='state'>请在{{orderInfo.cancel_time}}前完成支付!</view>
					<view>{{orderInfo.add_time_y}}<text class='time'>{{orderInfo.create_time}}</text></view>
				</view>
			</view>
			<view>
				<view class='nav'>
					<view class='navCon acea-row row-between-wrapper'>
						<view class="on">待付款</view>
						<view>待发货</view>
						<view>待收货</view>
						<view>待评价</view>
						<view>已完成</view>
					</view>
					<view class='progress acea-row row-between-wrapper'>
						<view class='iconfont icon-webicon318 font-color'></view>
						<view class='line'></view>
						<view class='iconfont icon-yuandianxiao'></view>
						<view class='line'></view>
						<view class='iconfont icon-yuandianxiao'></view>
						<view class='line'></view>
						<view class='iconfont icon-yuandianxiao'></view>
						<view class='line'></view>
						<view class='iconfont icon-yuandianxiao'></view>
					</view>
				</view>
				
				<view class='address'>
					<view class='name'>{{orderInfo.real_name}}<text class='phone'>{{orderInfo.user_phone}}</text></view>
					<view>{{orderInfo.user_address}}</view>
				</view>
				<view class='line'>
					<image src='../../static/images/line.jpg'></image>
				</view>
			</view>
			<!-- 店铺商品列表 -->
			<view class="order-wrapper" v-for="(item,index) in orderInfo.orderList" :key="index">
				<view class="title" @click="goStore(item)">{{item.merchant.mer_name}}<text class="iconfont icon-xiangyou"></text>
				</view>
				<view class="goods-box">
					<view class="item" v-for="(goods,j) in item.orderProduct" :key="goods.order_product_id" @click="goProduct(goods)">
						<image :src="goods.cart_info.product.image"></image>
						<view class="info-box">
							<view class="name line1">{{goods.cart_info.product.store_name}}</view>
							<view class="msg">{{goods.cart_info.productAttr.sku}}</view>
							<view class="price">￥{{goods.cart_info.productAttr.price}}</view>
						</view>
						<view class="num">x{{goods.product_num}}</view>
					</view>
				</view>
				<div class="goodCall" @click="goGoodCall">
					<span class="iconfont icon-kefu"></span><span style="font-size: 28rpx;">联系客服</span>
				</div>
			</view>
			
			<view class='wrapper'>
				<view class='item acea-row row-between'>
					<view>订单编号：</view>
					<view class='conter acea-row row-middle row-right'>{{orderInfo.group_order_sn}}
						<!-- #ifndef H5 -->
						<text class='copy' @tap='copy'>复制</text>
						<!-- #endif -->
						<!-- #ifdef H5 -->
						<text class='copy copy-data' :data-clipboard-text="orderInfo.group_order_sn">复制</text>
						<!-- #endif -->
					</view>
				</view>
				<view class='item acea-row row-between'>
					<view>下单时间：</view>
					<view class='conter'>{{orderInfo.create_time || 0}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>支付状态：</view>
					<view class='conter'>未支付</view>
				</view>
				<view class='item acea-row row-between'>
					<view>商品总额：</view>
					<view class='conter'>￥{{orderInfo.total_price}}</view>
				</view>
				<!-- <view class='item acea-row row-between'>
					<view>支付方式：</view>
					<view class='conter'>{{orderInfo._status._payType}}</view>
				</view> -->
			</view>
			<view class='wrapper'>
				<view class='item acea-row row-between' v-if="orderInfo.pay_postage > 0">
					<view>运费：</view>
					<view class='conter'>+￥{{orderInfo.pay_postage}}</view>
				</view>
				<view class='item acea-row row-between' v-if='orderInfo.coupon_price>0'>
					<view>优惠券抵扣：</view>
					<view class='conter'>-￥{{orderInfo.coupon_price}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>实付款：</view>
					<view class='conter'>￥{{orderInfo.pay_price}}</view>
				</view>
				<!-- <view class='actualPay acea-row row-right'>实付款：<text class='money font-color'>￥{{orderInfo.pay_price}}</text></view> -->
			</view>
			<view style='height:120rpx;'></view>
			<view class='footer acea-row row-right row-middle'>
				<view class="bnt cancel" @click.stop="cancelOrder">取消订单</view>
				<view class='bnt bg-color' @tap='pay_open(orderInfo.order_id)'>立即付款</view>
			</view>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
		<payment :payMode='payMode' :pay_close="pay_close" @onChangeFun='onChangeFun' :order_id="pay_order_id" :totalPrice='totalPrice'></payment>
	</view>
</template>
<style scoped lang="scss">
	.goodCall {
		text-align: center;
		width: 100%;
		height: 86rpx;
		padding: 0 30rpx;
		border-top: 1rpx solid #f0f0f0;
		font-size: 30rpx;
		line-height: 86rpx;
		background: #fff;
		color: #282828;
		
		.icon-kefu {
			font-size: 32rpx;
			margin-right: 15rpx;		
		}

		/* #ifdef MP */
		button {
			display: flex;
			align-items: center;
			justify-content: center;
			height: 86rpx;
			font-size: 30rpx;
			color: #e93323;
		}

		/* #endif */
	}

	.order-details .header {
		padding: 0 30rpx;
		height: 150rpx;
	}

	.order-details .header.on {
		background-color: #666 !important;
	}

	.order-details .header .pictrue {
		width: 110rpx;
		height: 110rpx;
	}

	.order-details .header .pictrue image {
		width: 100%;
		height: 100%;
	}

	.order-details .header .data {
		color: rgba(255, 255, 255, 0.8);
		font-size: 24rpx;
		margin-left: 27rpx;
	}

	.order-details .header .data.on {
		margin-left: 0;
	}

	.order-details .header .data .state {
		font-size: 30rpx;
		font-weight: bold;
		color: #fff;
		margin-bottom: 7rpx;
	}

	.order-details .header .data .time {
		margin-left: 20rpx;
	}

	.order-details .nav {
		background-color: #fff;
		font-size: 26rpx;
		color: #282828;
		padding: 25rpx 0;
	}

	.order-details .nav .navCon {
		padding: 0 40rpx;
	}

	.order-details .nav .on {
		color: #e93323;
	}

	.order-details .nav .progress {
		padding: 0 65rpx;
		margin-top: 10rpx;
	}

	.order-details .nav .progress .line {
		width: 100rpx;
		height: 2rpx;
		background-color: #939390;
	}

	.order-details .nav .progress .iconfont {
		font-size: 25rpx;
		color: #939390;
		margin-top: -2rpx;
	}

	.order-details .address {
		font-size: 26rpx;
		color: #868686;
		background-color: #fff;
		margin-top: 13rpx;
		padding: 35rpx 30rpx;
	}

	.order-details .address .name {
		font-size: 30rpx;
		color: #282828;
		margin-bottom: 15rpx;
	}

	.order-details .address .name .phone {
		margin-left: 40rpx;
	}

	.order-details .line {
		width: 100%;
		height: 3rpx;
	}

	.order-details .line image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-details .wrapper {
		background-color: #fff;
		margin-top: 12rpx;
		padding: 30rpx;
	}

	.order-details .wrapper .item {
		font-size: 28rpx;
		color: #282828;
	}

	.order-details .wrapper .item~.item {
		margin-top: 20rpx;
	}

	.order-details .wrapper .item .conter {
		color: #868686;
		width: 460rpx;
		text-align: right;
	}

	.order-details .wrapper .item .conter .copy {
		font-size: 20rpx;
		color: #333;
		border-radius: 17rpx;
		border: 1rpx solid #666;
		padding: 3rpx 15rpx;
		margin-left: 24rpx;
		
	}

	.order-details .wrapper .actualPay {
		border-top: 1rpx solid #eee;
		margin-top: 30rpx;
		padding-top: 30rpx;
	}

	.order-details .wrapper .actualPay .money {
		font-weight: bold;
		font-size: 30rpx;
	}

	.order-details .footer {
		width: 100%;
		height: 100rpx;
		position: fixed;
		bottom: 0;
		left: 0;
		background-color: #fff;
		padding: 0 30rpx;
		box-sizing: border-box;
	}

	.order-details .footer .bnt {
		width: 176rpx;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
		border-radius: 50rpx;
		color: #fff;
		font-size: 27rpx;
	}

	.order-details .footer .bnt.cancel {
		color: #aaa;
		border: 1rpx solid #ddd;
	}

	.order-details .footer .bnt~.bnt {
		margin-left: 18rpx;
	}

	.order-details .writeOff {
		background-color: #fff;
		margin-top: 13rpx;
		padding-bottom: 30rpx;
	}

	.order-details .writeOff .title {
		font-size: 30rpx;
		color: #282828;
		height: 87rpx;
		border-bottom: 1px solid #f0f0f0;
		padding: 0 30rpx;
		line-height: 87rpx;
	}

	.order-details .writeOff .grayBg {
		background-color: #f2f5f7;
		width: 590rpx;
		height: 384rpx;
		border-radius: 20rpx 20rpx 0 0;
		margin: 50rpx auto 0 auto;
		padding-top: 55rpx;
	}

	.order-details .writeOff .grayBg .pictrue {
		width: 290rpx;
		height: 290rpx;
		margin: 0 auto;
	}

	.order-details .writeOff .grayBg .pictrue image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-details .writeOff .gear {
		width: 590rpx;
		height: 30rpx;
		margin: 0 auto;
	}

	.order-details .writeOff .gear image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-details .writeOff .num {
		background-color: #f0c34c;
		width: 590rpx;
		height: 84rpx;
		color: #282828;
		font-size: 48rpx;
		margin: 0 auto;
		border-radius: 0 0 20rpx 20rpx;
		text-align: center;
		padding-top: 4rpx;
	}

	.order-details .writeOff .rules {
		margin: 46rpx 30rpx 0 30rpx;
		border-top: 1px solid #f0f0f0;
		padding-top: 10rpx;
	}

	.order-details .writeOff .rules .item {
		margin-top: 20rpx;
	}

	.order-details .writeOff .rules .item .rulesTitle {
		font-size: 28rpx;
		color: #282828;
	}

	.order-details .writeOff .rules .item .rulesTitle .iconfont {
		font-size: 30rpx;
		color: #333;
		margin-right: 8rpx;
		margin-top: 5rpx;
	}

	.order-details .writeOff .rules .item .info {
		font-size: 28rpx;
		color: #999;
		margin-top: 7rpx;
	}

	.order-details .writeOff .rules .item .info .time {
		margin-left: 20rpx;
	}

	.order-details .map {
		height: 86rpx;
		font-size: 30rpx;
		color: #282828;
		line-height: 86rpx;
		border-bottom: 1px solid #f0f0f0;
		margin-top: 13rpx;
		background-color: #fff;
		padding: 0 30rpx;
	}

	.order-details .map .place {
		font-size: 26rpx;
		width: 176rpx;
		height: 50rpx;
		border-radius: 25rpx;
		line-height: 50rpx;
		text-align: center;
	}

	.order-details .map .place .iconfont {
		font-size: 27rpx;
		height: 27rpx;
		line-height: 27rpx;
		margin: 2rpx 3rpx 0 0;
	}

	.order-details .address .name .iconfont {
		font-size: 34rpx;
		margin-left: 10rpx;
	}

	.refund {
		padding: 0 30rpx 30rpx;
		margin-top: 24rpx;
		background-color: #fff;

		.title {
			display: flex;
			align-items: center;
			font-size: 30rpx;
			color: #333;
			height: 86rpx;
			border-bottom: 1px solid #f5f5f5;

			image {
				width: 32rpx;
				height: 32rpx;
				margin-right: 10rpx;
			}
		}

		.con {
			padding-top: 25rpx;
			font-size: 28rpx;
			color: #868686;
		}
	}
	.order-wrapper{
		margin-top: 15rpx;
		background-color: #fff;
		.title{
			display: flex;
			align-items: center;
			height: 86rpx;
			padding:0 30rpx;
			border-bottom: 1px solid #f0f0f0;
			.iconfont{
				font-size: 24rpx;
				color: #666;
				margin-top: 6rpx;
				margin-left: 5rpx;
			}
		}
		.goods-box{
			padding-left: 30rpx;
			.item{
				display: flex;
				padding: 25rpx 30rpx 25rpx 0;
				image{
					width: 130rpx;
					height: 130rpx;
					border-radius: 16rpx;
				}
				.info-box{
					display: flex;
					flex-direction: column;
					justify-content: space-between;
					margin-left: 25rpx;
					width: 450rpx;
					.msg{
						color: #868686;
						font-size: 20rpx;
					}
					.price{
						font-size: 26rpx;
						color: $theme-color;
					}
				}
				.num{
					flex: 1;
					text-align: right;
					font-size: 26rpx;
					color: #868686;
				}
			}
		}
	}
</style>

<script>
	let app = getApp();
	import { HTTP_REQUEST_URL } from '@/config/app';
	import {
		groupOrderDetail,
		orderAgain,
		orderTake,
		orderDel,
		unOrderCancel
	} from '@/api/order.js';
	import {
		openOrderRefundSubscribe
	} from '@/utils/SubscribeMessage.js';
	import {
		getUserInfo
	} from '@/api/user.js';
	import payment from '@/components/payment';
	import orderGoods from "@/components/orderGoods";
	import ClipboardJS from "@/plugin/clipboard/clipboard.js";
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	export default {
		components: {
			payment,
			orderGoods,
			// #ifdef MP
			authorize
			// #endif
		},
		data() {
			return {
				order_id: '',
				evaluate: 0,
				cartInfo: [], //购物车产品
				orderInfo: {
					system_store: {},
					_status: {}
				}, //订单详情
				system_store: {},
				isGoodsReturn: false, //是否为退款订单
				status: {}, //订单底部按钮状态
				isClose: false,
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
					},
				],
				pay_close: false,
				pay_order_id: '',
				totalPrice: '0',
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				imgUrl:HTTP_REQUEST_URL
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad: function(options) {
			if (options.order_id) {
				this.$set(this, 'order_id', options.order_id);
			}
		},
		onShow() {
			if (this.isLogin) {
				this.getOrderInfo();
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
		onHide: function() {
			this.isClose = true;
		},
		onReady: function() {
			// #ifdef H5
			this.$nextTick(function() {
				const clipboard = new ClipboardJS(".copy-data");
				clipboard.on("success", () => {
					this.$util.Tips({
						title: '复制成功'
					});
				});
			});
			// #endif
		},
		methods: {
			// 去店铺
			goStore(item){
				uni.navigateTo({
					url:`/pages/store/home/index?id=${item.merchant.mer_id}`
				})
			},
			// 商品详情
			goProduct(goods){
				uni.navigateTo({
					url:`/pages/goods_details/index?id=${goods.product_id}`
				})
			},
			goGoodCall() {
				let self = this
				uni.navigateTo({
					url: `/pages/chat/customer_list/index?orderId=${self.order_id}`
				})
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
			 * 拨打电话
			 */
			makePhone: function() {
				uni.makePhoneCall({
					phoneNumber: this.system_store.phone
				})
			},
			
			/**
			 * 关闭支付组件
			 * 
			 */
			payClose: function() {
				this.pay_close = false;
			},
			/**
			 * 打开支付组件
			 * 
			 */
			pay_open: function() {
				this.pay_close = true;
				this.pay_order_id = this.orderInfo.group_order_id.toString();
				this.totalPrice = this.orderInfo.pay_price;
			},
			/**
			 * 支付成功回调
			 * 
			 */
			pay_complete: function() {
				this.pay_close = false;
				this.pay_order_id = '';
				uni.redirectTo({
					url:'/pages/users/order_list/index?status=1'
				})
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
			 * 登录授权回调
			 * 
			 */
			onLoadFun: function() {
				this.isShowAuth = false;
				this.getOrderInfo();
				this.getUserInfo();
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
				})
			},
			/**
			 * 获取订单详细信息
			 * 
			 */
			getOrderInfo: function() {
				let that = this;
				uni.showLoading({
					title: "正在加载中"
				});
				groupOrderDetail(this.order_id).then(res => {
					let _type = 0;
					uni.hideLoading();
					that.$set(that, 'orderInfo', res.data);
				}).catch(err => {
					console.log(err)
					uni.hideLoading();
					that.$util.Tips({
						title: err
					}, '/pages/users/order_list/index');
				});
			},
			/**
			 * 
			 * 剪切订单号
			 */
			// #ifndef H5
			copy: function() {
				let that = this;
				uni.setClipboardData({
					data: this.orderInfo.order_id
				});
			},
			// #endif
			// 取消订单
			cancelOrder() {
				let self = this
				uni.showModal({
					title: '提示',
					content: '确认取消该订单?',
					success: function(res) {
						if (res.confirm) {
							unOrderCancel(self.order_id)
								.then((data) => {
									console.log(data)
									self.$util.Tips({
										title: data.message
									}, {
										tab: 3
									})
								})
								.catch(() => {
									self.getDetail();
								});
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
		}
	}
</script>

<style>
	.qs-btn {
		width: auto;
		height: 60rpx;
		text-align: center;
		line-height: 60rpx;
		border-radius: 50rpx;
		color: #fff;
		font-size: 27rpx;
		padding: 0 3%;
		color: #aaa;
		border: 1px solid #ddd;
		margin-right: 20rpx;
	}
</style>
