<template>
	<view>
		<view class='payment-status'>
			<!--失败时： 用icon-iconfontguanbi fail替换icon-duihao2 bg-color-->
			<view class='iconfont icon-duihao2 bg-color icon' v-if="order_pay_info.paid !=0"></view>
			<view class='iconfont icon-iconfontguanbi bg-color icon' v-else></view>
			<!-- 失败时：订单支付失败 -->
			<view class='status'>{{order_pay_info.paid?'支付成功':'订单未支付'}}</view>
			<!-- <view class='status' v-else>订单创建成功</view> -->
			<view class='wrapper'>
				<view class='item acea-row row-between-wrapper'>
					<view>下单时间</view>
					<view class='itemCom'>{{order_pay_info.create_time}}</view>
				</view>
				<view class='item acea-row row-between-wrapper'>
					<view>支付方式</view>
					<view class='itemCom'>{{order_pay_info.pay_type==0?'余额':'微信'}}</view>
				</view>
				<view class='item acea-row row-between-wrapper'>
					<view>支付金额</view>
					<view class='itemCom'>{{order_pay_info.pay_price}}</view>
				</view>
				<!--失败时加上这个  -->
				<view class='item acea-row row-between-wrapper' v-if="order_pay_info.paid==0 && order_pay_info.pay_type != 'offline'">
					<view>失败原因</view>
					<view class='itemCom'>{{order_pay_info.pay_type==0 ? '余额不足':msg}}</view>
				</view>
			</view>
			<!--失败时： 重新购买 -->
			<view @tap="goOrderDetails">
				<button formType="submit" class='returnBnt bg-color' hover-class='none'>查看订单</button>
			</view>
			<!-- <view @tap="goOrderDetails" v-if="order_pay_info.paid==0 && status==1">
				<button class='returnBnt bg-color' hover-class='none'>重新购买</button>
			</view> -->
			<!-- <view @tap="goOrderDetails" v-if="order_pay_info.paid==0 && status==2">
				<button class='returnBnt bg-color' hover-class='none'>重新支付</button>
			</view> -->
			<!-- <button @click="goPink(order_pay_info.pink_id)" class='returnBnt cart-color' formType="submit" hover-class='none' v-if="order_pay_info.pink_id && order_pay_info.paid!=0 && status!=2 && status!=1">邀请好友参团</button> -->
			<button @click="goIndex" class='returnBnt cart-color' formType="submit" hover-class='none'>返回首页</button>
			<view class="coupon-wrapper" v-if="couponList.length>0 && order_pay_info.paid">
				<view class="hd">
					<view class="line"></view>
					<view class="txt">赠送优惠券</view>
					<view class="line"></view>
				</view>
				<view class="coupon-box" :class="{on:isOpen}">
					<block v-for="(item,index) in couponList" :key="index">
						<view class="coupon-item flex">
							<view class="left-bg"><text>￥</text>{{item.coupon_price}}</view>
							<view class="info">
								<view class="title">{{item.title}}</view>
								<view class="des">满{{item.use_min_price}}元可用</view>
								<block v-if="item.coupon_type == 1">
									<view class="des">有效期:{{ item.use_start_time |timeYMD }}-{{ item.use_end_time |timeYMD}}</view>
								</block>
								<block v-if="item.coupon_type == 0">
									<view class="des">领取后{{ item.coupon_time}}天内可用</view>
								</block>
							</view>
						</view>
					</block>
				</view>
				<view class="more" v-if="couponList.length>2" @click="bindMore">
					{{ text }}
					<text v-if="!isOpen" class="iconfont icon-xiangxia"></text>
					<text v-else class="iconfont icon-xiangshang"></text>
				</view>
			</view>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		getPayOrder
	} from '@/api/order.js';
	import {
		openOrderSubscribe
	} from '@/utils/SubscribeMessage.js';
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
			// #ifdef MP
			authorize
			// #endif
		},
		filters: {
		  timeYMD: function (value) {
				if(value){
					var newDate=/\d{4}-\d{1,2}-\d{1,2}/g.exec(value)
					return newDate[0]
				}
		  }
		},
		data() {
			return {
				orderId: '',
				order_pay_info: {},
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				status: 0,
				msg: '',
				couponList:[], //优惠券列表
				isOpen:false ,//展开
				text: '展开更多'
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad: function(options) {
			if (!options.order_id) return this.$util.Tips({
				title: '缺少参数无法查看订单支付状态'
			}, {
				tab: 3,
				url: 1
			});
			this.orderId = options.order_id;
			this.status = options.status || 0;
			this.msg = options.msg || '';
			if (this.isLogin) {
				this.getOrderPayInfo();
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
			// 优惠券展开
			bindMore(){
				this.isOpen = !this.isOpen
				this.text = this.text == '展开更多' ? '收起' : '展开更多';
			},
			onLoadFun: function() {
				this.isShowAuth = false;
				this.getOrderPayInfo();
			},
			/**
			 * 
			 * 支付完成查询支付状态
			 * 
			 */
			getOrderPayInfo: function() {
				let that = this;
				uni.showLoading({
					title: '正在加载中'
				});
				getPayOrder(that.orderId).then(res => {
					uni.hideLoading();
					that.$set(that, 'order_pay_info', res.data);
					that.couponList = res.data.give_coupon
					uni.setNavigationBarTitle({
						title: res.data.paid ? '支付成功' : '支付失败'
					});
				}).catch(err => {
					uni.hideLoading();
				});
			},
			/**
			 * 去首页关闭当前所有页面
			 */
			goIndex: function(e) {
				uni.switchTab({
					url: '/pages/index/index'
				});
			},
			// 去参团页面；
			goPink: function(id) {
				uni.navigateTo({
					url: '/pages/activity/goods_combination_status/index?id=' + id
				});
			},
			/**
			 * 
			 * 去订单详情页面
			 */
			goOrderDetails: function(e) {
				let that = this;
				if (this.order_pay_info.paid == 0) {
					uni.redirectTo({
						url: '/pages/users/order_list/index'
					})
				} else {
					// // #ifdef MP
					// uni.showLoading({
					// 	title: '正在加载',
					// })
					// openOrderSubscribe().then(res => {
					// 	uni.hideLoading();
					// 	uni.redirectTo({
					// 		url: '/pages/order_details/index?order_id=' + that.orderId
					// 	});
					// }).catch(() => {
					// 	nui.hideLoading();
					// });
					// // #endif
					// // #ifndef MP
					
					// // #endif
					uni.redirectTo({
						url: '/pages/users/order_list/index?status=1'
					})
				}

			}
		}
	}
</script>

<style lang="scss">
	.payment-status {
		background-color: #fff;
		margin: 92rpx 30rpx 30rpx;
		border-radius: 10rpx;
		padding: 1rpx 0 28rpx 0;
	}

	.payment-status .icon {
		font-size: 70rpx;
		width: 140rpx;
		height: 140rpx;
		border-radius: 50%;
		color: #fff;
		text-align: center;
		line-height: 140rpx;
		text-shadow: 0px 4px 0px #df1e14;
		border: 6rpx solid #f5f5f5;
		margin: -76rpx auto 0 auto;
		background-color: #999;
	}

	.payment-status .icon.fail {
		text-shadow: 0px 4px 0px #7a7a7a;
	}

	.payment-status .status {
		font-size: 32rpx;
		font-weight: bold;
		text-align: center;
		margin: 25rpx 0 37rpx 0;
	}

	.payment-status .wrapper {
		border: 1rpx solid #eee;
		margin: 0 30rpx 47rpx 30rpx;
		padding: 35rpx 0;
		border-left: 0;
		border-right: 0;
	}

	.payment-status .wrapper .item {
		font-size: 28rpx;
		color: #282828;
	}

	.payment-status .wrapper .item~.item {
		margin-top: 20rpx;
	}

	.payment-status .wrapper .item .itemCom {
		color: #666;
	}

	.payment-status .returnBnt {
		width: 630rpx;
		height: 86rpx;
		border-radius: 50rpx;
		color: #fff;
		font-size: 30rpx;
		text-align: center;
		line-height: 86rpx;
		margin: 0 auto 20rpx auto;
	}
	.coupon-wrapper{
		.hd{
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 30rpx 0;
			color: #999999;
			font-size: 24rpx;
			.line{
				width: 70rpx;
				height: 1px;
				background: #DCDCDC;
			}
			.txt{
				margin: 0 20rpx;
			}
		}
		.coupon-box{
			height: 356rpx;
			padding: 0 20rpx;
			overflow: hidden;
			&.on{
				height: auto;
			}
			.coupon-item{
				width: 100%;
				margin-bottom: 20rpx;
				box-shadow:0px 2px 10px 0px rgba(0, 0, 0, 0.06);
				.left-bg{
					display: flex;
					align-items: center;
					justify-content: center;
					width: 236rpx;
					height: 160rpx;
					color: #fff;
					font-size: 64rpx;
					background-image:url('data:image/png;base64,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');background-repeat:no-repeat;background-size:100% 100%;
					text{
						margin-top: 26rpx;
						font-size: 36rpx;
					}
				}
				.info{
					display: flex;
					flex-direction: column;
					justify-content: space-between;
					margin-left: 20rpx;
					padding: 20rpx 0;
					.title{
						color: #282828;
						font-size: 30rpx;
					}
					.des{
						font-size: 24rpx;
						color: #999999;
					}
				}
			}
		}
		.more{
			display: flex;
			align-items: center;
			justify-content: center;
			padding-top: 24rpx;
			font-size: 24rpx;
			color: #999999;
			.iconfont{
				margin-top: 6rpx;
				margin-left: 10rpx;
				font-size: 20rpx;
			}
		}
	}
</style>
