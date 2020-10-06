<template>
	<view>
		<view class="payment" :class="pay_close ? 'on' : ''">
			<view class="title acea-row row-center-wrapper">
				选择付款方式<text class="iconfont icon-guanbi" @click='close'></text>
			</view>
			<view class="item acea-row row-between-wrapper" @click='goPay(item.number || 0 , item.value)' v-for="(item,index) in payMode"
			 :key="index" v-if="item.payStatus == 1">
				<view class="left acea-row row-between-wrapper">
					<view class="iconfont" :class="item.icon"></view>
					<view class="text">
						<view class="name">{{item.name}}</view>
						<view class="info" v-if="item.number">
							{{item.title}} <span class="money">￥{{ item.number }}</span>
						</view>
						<view class="info" v-else>{{item.title}}</view>
					</view>
				</view>
				<view class="iconfont icon-xiangyou"></view>
			</view>
		</view>
		<view class="mask" ref="close" @click='close' v-if="pay_close"></view>
	</view>
</template>

<script>
	import {
		orderPay
	} from '@/api/order.js';
	export default {
		props: {
			payMode: {
				type: Array,
				default: function() {
					return [];
				}
			},
			pay_close: {
				type: Boolean,
				default: false,
			},
			order_id: {
				type: String,
				default: ''
			},
			totalPrice: {
				type: String,
				default: '0'
			}
		},
		data() {
			return {

			};
		},
		methods: {
			close: function() {
				this.$emit('onChangeFun', {
					action: 'payClose'
				});
			},
			goPay: function(number, paytype) {
				let that = this;
				let type = ''
				if (paytype == 'wechat') {
					// #ifdef H5
					type = this.$wechat.isWeixin() ? 'weixin' : 'h5';
					// #endif

					// #ifdef MP
					type = 'routine';
					// #endif
				} else if (paytype == 'balance') {
					type = 'balance';
				}
				if (!that.order_id) return that.$util.Tips({
					title: '请选择要支付的订单'
				});
				if (paytype == 'balance' && parseFloat(number) < parseFloat(that.totalPrice)) return that.$util.Tips({
					title: '余额不足！'
				});
				uni.showLoading({
					title: '支付中'
				});
				orderPay(that.order_id, {
					type: type,
				}).then(res => {
					let status = res.data.status,
						orderId = res.data.result.order_id,
						jsConfig = res.data.result.config,
						goPages = '/pages/users/order_list/index';
					switch (status) {			
						case 'ORDER_EXIST':
						case 'EXTEND_ORDER':
						case 'PAY_ERROR':
						case 'error':
							uni.hideLoading();
							this.$emit('onChangeFun', {
								action: 'payClose'
							});
							return that.$util.Tips({
								title: res.message
							});							
							break;
						case 'success':
							uni.hideLoading();
							this.$emit('onChangeFun', {
								action: 'payClose'
							});
							if (that.BargainId || that.combinationId || that.pinkId || that.seckillId)					
								return that.$util.Tips({
									title: res.message,
									icon: 'success'
								}, {
									url: goPages + '?status=1'
								});								
							return that.$util.Tips({
								title: res.message,
								icon: 'success'
							}, {
								url: goPages + '?status=1'
							});
							break;
						// #ifndef MP
						case "wechat":
						case "weixin":
							jsConfig.timeStamp = jsConfig.timestamp;
							that.$wechat.pay(jsConfig).then(res => {	
								console.log('测试支付数据无效的success：'+res.data)
								this.$emit('onChangeFun', {
									action: 'payClose'
								});
								return that.$util.Tips({
									title: res.message,
									icon: 'success'
								}, {				
									url: goPages + 'status=1'
								});
							}).catch(res => {
								console.log('测试支付数据无效的catch：'+res.data)
								if (res.errMsg == 'chooseWXPay:cancel') return that.$util.Tips({
									title: '取消支付'
								}, {									
									url: goPages + '?status=0'
								});
							})
							break;
						// #endif
						// #ifdef MP
						case "routine":
							jsConfig.timeStamp = jsConfig.timestamp;
							that.toPay = true;
							uni.requestPayment({
								...jsConfig,
								success: function(res) {
									uni.hideLoading();
									this.$emit('onChangeFun', {
										action: 'payClose'
									});
									if (that.BargainId || that.combinationId || that.pinkId || that.seckillId)
										return that.$util.Tips({
											title: '支付成功',
											icon: 'success'
										}, {											
											url: goPages + '?status=1'
										});
									
									return that.$util.Tips({
										title: '支付成功',
										icon: 'success'
									}, {										
										url: goPages + '?status=1'
									});
								},
								fail: function(e) {
									uni.hideLoading();
									this.$emit('onChangeFun', {
										action: 'payClose'
									});
									return that.$util.Tips({
										title: '取消支付'
									});
								},
								complete: function(e) {
									uni.hideLoading();									
									//关闭当前页面跳转至订单状态
									if (res.errMsg == 'requestPayment:cancel') return that.$util.Tips({
										title: '取消支付'
									});
									this.$emit('onChangeFun', {
										action: 'payClose'
									});
								},
							})
							break;
						// #endif
						case "balance":
							uni.hideLoading();
							this.$emit('onChangeFun', {
								action: 'payClose'
							});
							//余额不足
							return that.$util.Tips({
								title: res.message
							});
							break;
						// #ifdef H5
						case 'h5':
							let host = window.location.protocol+"//"+window.location.host;
							let url = `${host}/pages/order_pay_status/index?order_id=${orderId}`
							let eUrl = encodeURIComponent(url)
							let locations = `${jsConfig.mweb_url}&redirect_url=${eUrl}` 
							setTimeout(() => {
								location.href = locations;
							}, 100);
							break;
						// #endif		
					}
				}).catch(err => {
					uni.hideLoading();
					return that.$util.Tips({
						title: err
					});
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	.payment {
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100%;
		border-radius: 16rpx 16rpx 0 0;
		background-color: #fff;
		padding-bottom: 60rpx;
		z-index: 99;
		transition: all 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
		transform: translate3d(0, 100%, 0);
	}

	.payment.on {
		transform: translate3d(0, 0, 0);
	}

	.payment .title {
		text-align: center;
		height: 123rpx;
		font-size: 32rpx;
		color: #282828;
		font-weight: bold;
		padding-right: 30rpx;
		margin-left: 30rpx;
		position: relative;
		border-bottom: 1rpx solid #eee;
	}

	.payment .title .iconfont {
		position: absolute;
		right: 30rpx;
		top: 50%;
		transform: translateY(-50%);
		font-size: 43rpx;
		color: #8a8a8a;
		font-weight: normal;
	}

	.payment .item {
		border-bottom: 1rpx solid #eee;
		height: 130rpx;
		margin-left: 30rpx;
		padding-right: 30rpx;
	}

	.payment .item .left {
		width: 610rpx;
	}

	.payment .item .left .text {
		width: 540rpx;
	}

	.payment .item .left .text .name {
		font-size: 32rpx;
		color: #282828;
	}

	.payment .item .left .text .info {
		font-size: 24rpx;
		color: #999;
	}

	.payment .item .left .text .info .money {
		color: #ff9900;
	}

	.payment .item .left .iconfont {
		font-size: 45rpx;
		color: #09bb07;
	}

	.payment .item .left .iconfont.icon-zhifubao {
		color: #00aaea;
	}

	.payment .item .left .iconfont.icon-yuezhifu {
		color: #ff9900;
	}

	.payment .item .left .iconfont.icon-yuezhifu1 {
		color: #eb6623;
	}

	.payment .item .iconfont {
		font-size: 0.3rpx;
		color: #999;
	}
</style>
