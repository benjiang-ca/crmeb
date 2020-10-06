<template>
	<view>
		<view class='order-details'>
			<!-- 给header上与data上加on为退款订单-->
			<view class='header bg-color acea-row row-middle' :class='isGoodsReturn ? "on":""'>
				<view class='pictrue' v-if="isGoodsReturn==false">
					<image :src="imgUrl+'/static/order_'+(orderInfo.status+2)+'.gif'"></image>
				</view>
				<view class='data' :class='isGoodsReturn ? "on":""'>
					<view class='state'>
						<block v-if="orderInfo.status == 0 && orderInfo.order_type == 0">待发货</block>
						<block v-if="orderInfo.status == 0 && orderInfo.order_type == 1">待核销</block>
						<block v-if="orderInfo.status == 1">待收货</block>
						<block v-if="orderInfo.status == 2">待评价</block>
						<block v-if="orderInfo.status == 3">已完成</block>
						<block v-if="orderInfo.status == -1">已为您退款,感谢您的支持</block>
					</view>
					<view>{{orderInfo.pay_time}}</view>
				</view>
			</view>
			<view>
				<block v-if="isGoodsReturn==false">
					<view class='nav'>
						<view class='navCon acea-row row-between-wrapper'>
							<view>待付款</view>
							<view :class="orderInfo.status == 0 ? 'on':''" v-if="orderInfo.order_type == 0">待发货</view>
							<view :class="orderInfo.status == 0 ? 'on':''" v-if="orderInfo.order_type == 1">待核销</view>
							<view :class="orderInfo.status == 1 ? 'on':''" v-if="orderInfo.order_type == 0">待收货</view>
							<view :class="orderInfo.status == 2 ? 'on':''">待评价</view>
							<view :class="orderInfo.status == 3 ? 'on':''">已完成</view>
						</view>
						<view class='progress acea-row row-between-wrapper'>
							<view class='iconfont icon-yuandianxiao font-color'></view>
							<view class='line bg-color'></view>
							<view class='iconfont' :class='(orderInfo.status == 0 ? "icon-webicon318":"icon-yuandianxiao") + " " + (orderInfo.status >= 0 ? "font-color":"")'></view>
							<view class='line' :class='orderInfo.status > 0 ? "bg-color":""'></view>
							<view class='iconfont' :class='(orderInfo.status == 1 ? "icon-webicon318":"icon-yuandianxiao") + " " +(orderInfo.status >= 1 ? "font-color":"")'  v-if="orderInfo.order_type == 0"></view>
							<view class='line' :class='orderInfo.status > 1 ? "bg-color":""'  v-if="orderInfo.order_type == 0"></view>
							<view class='iconfont' :class='(orderInfo.status == 2 ? "icon-webicon318":"icon-yuandianxiao") + " " + (orderInfo.status >= 2 ? "font-color":"")'></view>
							<view class='line' :class='orderInfo.status > 2 ? "bg-color":""'></view>
							<view class='iconfont' :class='(orderInfo.status == 3 ? "icon-webicon318":"icon-yuandianxiao") + " " + (orderInfo.status >= 3 ? "font-color":"")'></view>
						</view>
					</view>
					<view class='line'>
						<image src='/static/images/line.jpg'></image>
					</view>
				</block>
				<!-- 配送地址 -->
				<view class='address' v-if="orderInfo.order_type == 0">
					<view class='name'>{{orderInfo.real_name}}<text class='phone'>{{orderInfo.user_phone}}</text></view>
					<view>{{orderInfo.user_address}}</view>
				</view>
				<!-- 核销订单 -->
				<view class="writeOff" v-if="orderInfo.order_type == 1 && isGoodsReturn==false && orderInfo.take">
					<view class="title">核销信息</view>
					<view class="grayBg">
						<!-- <view class="written" v-if="orderInfo.status == 2">
							<image src="/static/images/written.png"></image>
						</view> -->
						<view class="pictrue">
							<image :src="codeUrl"></image>
						</view>
					</view>
					<view class="gear">
						<image src="/static/images/writeOff.jpg"></image>
					</view>
					<view class="num">{{orderInfo.verify_code}}</view>
					<view class="rules">
						<view class="item">
							<view class="rulesTitle acea-row row-middle">
								<text class="iconfont icon-shijian"></text>核销时间
							</view>
							<view v-if="orderInfo.take" class="info">
								<text v-if="orderInfo.take.mer_take_day !== undefind && orderInfo.take.mer_take_day.length == 7">每日：</text>
								<block v-else>
									<text v-for="item in orderInfo.take.mer_take_day">{{'周'+ toChinese(item)}},</text>
								</block>
								<text class="time">{{orderInfo.take.mer_take_time[0]}}-{{orderInfo.take.mer_take_time[1]}}</text>
							</view>
						</view>
						<view class="item">
							<view class="rulesTitle acea-row row-middle">
								<text class="iconfont icon-shuoming1"></text>使用说明
							</view>
							<view class="info">可将二维码出示给店员扫描或提供数字核销码</view>
						</view>
					</view>
				</view>
				<!-- 地图 -->
				<view class="map acea-row row-between-wrapper"  v-if="orderInfo.order_type == 1">
					<view>自提地址信息</view>
					<view class="place cart-color acea-row row-center-wrapper" @tap="showMaoLocation">
						<text class="iconfont icon-weizhi"></text>查看位置
					</view>
				</view>
				<view class='address' v-if="orderInfo.order_type == 1" style="margin-top: 0;">
					<view class='name'>
						{{orderInfo.take.mer_take_name}}
						<text class='phone' @click="makePhone">{{orderInfo.take.mer_take_phone}}</text>
						<text class="iconfont icon-tonghua font-color" @click="makePhone"></text>
					</view>
					<view>{{orderInfo.take.mer_take_address}}</view>
				</view>
				
				<view class="merchant" v-if="orderInfo.merchant" @click="goStore(orderInfo.mer_id)">
					{{orderInfo.merchant.mer_name}}
					<text class="iconfont icon-xiangyou"></text>
				</view>
				<block v-if="cartInfo.length>0">
					<orderGoods :evaluate='orderInfo.status' :orderId="order_id" :cartInfo="cartInfo" :jump="true"></orderGoods>
				</block>
				<div class="goodCall" @click="goGoodCall">
					<span class="iconfont icon-kefu"></span><span style="font-size: 28rpx;">联系客服</span>
				</div>
				<!-- 送货 -->
				<view class="wrapper" v-if="orderInfo.delivery_type == 2">
					<view class='item acea-row row-between'>
						<view>配送方式：</view>
						<view class='conter'>送货</view>
					</view>
					<view class='item acea-row row-between'>
						<view>配送员：</view>
						<view class='conter'>{{orderInfo.delivery_name}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>联系电话：</view>
						<view class='conter'>{{orderInfo.delivery_id}}</view>
					</view>

				</view>
				<view class='wrapper'>
					<view class='item acea-row row-between'>
						<view>订单编号：</view>
						<view class='conter acea-row row-middle row-right'>{{orderInfo.order_sn}}
							<!-- #ifndef H5 -->
							<text class='copy' @tap='copy'>复制</text>
							<!-- #endif -->
							<!-- #ifdef H5 -->
							<text class='copy copy-data' :data-clipboard-text="orderInfo.order_sn">复制</text>
							<!-- #endif -->
						</view>
					</view>
					<view class='item acea-row row-between'>
						<view>下单时间：</view>
						<view class='conter'>{{orderInfo.create_time}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>支付状态：</view>
						<view class='conter'>已支付</view>
					</view>
					<view class='item acea-row row-between' v-if="orderInfo.pay_time">
						<view>支付时间：</view>
						<view class='conter'>{{orderInfo.pay_time}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>商品总额：</view>
						<view class='conter'>￥{{orderInfo.total_price}}</view>
					</view>
					<view class='item acea-row row-between'>
						<view>支付方式：</view>
						<view class='conter' v-if="orderInfo.pay_type==0">余额支付</view>
						<view class='conter' v-else>微信支付</view>
					</view>
					<view class='item acea-row row-between' v-if="orderInfo.mark">
						<view>买家留言：</view>
						<view class='conter'>{{orderInfo.mark}}</view>
					</view>
				</view>
			</view>
			<view class='wrapper'>
				<view class='item acea-row row-between' v-if="orderInfo.pay_postage > 0">
					<view>运费：</view>
					<view class='conter'>+￥{{orderInfo.pay_postage}}</view>
				</view>
				<view class='item acea-row row-between' v-if='orderInfo.coupon_id'>
					<view>优惠券抵扣：</view>
					<view class='conter'>-￥{{orderInfo.coupon_price}}</view>
				</view>
				<view class='item acea-row row-between'>
					<view>实付款：</view>
					<view class='conter'>￥{{orderInfo.pay_price}}</view>
				</view>
			</view>
			<view style='height:120rpx;'></view>
			<view class='footer acea-row row-right row-middle' v-if="isGoodsReturn==false">
				<block v-if="orderInfo.status == 0">
					<view class="bnt cancel" @click="allRefund" v-if="refundNum.length != cartInfo.length">批量退款</view>
				</block>
				<block v-if="orderInfo.status == 1">
					<view class="bnt cancel" @click="allRefund" v-if="refundNum.length != cartInfo.length">批量退款</view>
					<navigator v-if="orderInfo.delivery_type == 1" class='bnt cancel' hover-class='none' :url="'/pages/users/goods_logistics/index?orderId='+ orderInfo.order_id">查看物流</navigator>
					<view class='bnt bg-color' @tap='confirmOrder'>确认收货</view>
				</block>
				<block v-if="orderInfo.status == 2">
					<view class="bnt cancel" @click="allRefund" v-if="refundNum.length != cartInfo.length">批量退款</view>
					<navigator v-if="orderInfo.delivery_type == 1" class='bnt cancel' hover-class='none' :url="'/pages/users/goods_logistics/index?orderId='+ orderInfo.order_id">查看物流</navigator>
					<view class='bnt bg-color' @click="goOrderConfirm">再次购买</view>
				</block>
				<block v-if="orderInfo.status == 3">
					<view class='bnt cancel' @click="delOrder">删除订单</view>
					<view class="bnt cancel" @click="allRefund" v-if="refundNum.length != cartInfo.length">批量退款</view>
					<view class='bnt bg-color' @click="goOrderConfirm">再次购买</view>
				</block>
			</view>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
		<payment :payMode='payMode' :pay_close="pay_close" @onChangeFun='onChangeFun' :order_id="pay_order_id" :totalPrice='totalPrice'></payment>
	</view>
</template>
<style scoped lang="scss">
	.merchant {
		width: 100%;
		height: 86rpx;
		padding: 0 30rpx;
		margin-top: 15rpx;
		border-bottom: 2rpx solid #f0f0f0;
		font-size: 30rpx;
		color: #282828;
		line-height: 86rpx;
		box-sizing: border-box;
		background-color: #fff;

		.iconfont {
			margin-top: 6rpx;
			font-size: 22rpx;
		}
	}

	.goodCall {
		text-align: center;
		width: 100%;
		height: 86rpx;
		padding: 0 30rpx;
		border-bottom: 1rpx solid #eee;
		font-size: 30rpx;
		line-height: 86rpx;
		background: #fff;
		color: #282828;

		.icon-kefu {
			font-size: 28rpx;
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
		margin-left: 20rpx;
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
</style>

<script>
	let app = getApp();
	import { HTTP_REQUEST_URL } from '@/config/app';
	import {
		getOrderDetail,
		orderAgain,
		orderTake,
		orderDel,
		orderCancel,
		verifyCode
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
					_status: {},
					take: {}
				}, //订单详情
				system_store: {},
				isGoodsReturn: false, //是否为退款订单
				status: {}, //订单底部按钮状态
				isClose: false,
				payMode: [{
						name: "微信支付",
						icon: "icon-weixinzhifu",
						value: 'weixin',
						title: '微信快捷支付',
						payStatus: 1
					},
					{
						name: "余额支付",
						icon: "icon-yuezhifu",
						value: 'yue',
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
				refundNum: [], //退款个数临时数据
				imgUrl:HTTP_REQUEST_URL,
				codeUrl:''
			};
		},
		computed: mapGetters(['isLogin', 'uid']),
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
			// 数字转汉字
			toChinese(num){
			
					let changeNum = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
					let unit = ["", "十", "百", "千", "万"];
					num = parseInt(num);
					let getWan = (temp) => {
					　　let strArr = temp.toString().split("").reverse();
					　　let newNum = "";
					　　for (var i = 0; i < strArr.length; i++) {
						　　newNum = (i == 0 && strArr[i] == 0 ? "" : (i > 0 && strArr[i] == 0 && strArr[i - 1] == 0 ? "" : changeNum[strArr[i]] + (strArr[i] == 0 ? unit[0] : unit[i]))) + newNum;
					　　}
					 　 return newNum;
				 }
				 let overWan = Math.floor(num / 10000);
				 let noWan = num % 10000;
				 if (noWan.toString().length < 4) {　　　　　　noWan = "0" + noWan;　　　 }
				 return overWan ? getWan(overWan) + "万" + getWan(noWan) : getWan(num);
	
			},
			// 返回店铺首页
			goStore(mer_id){
				uni.navigateTo({
					url: '/pages/store/home/index?id=' + mer_id
				})
			},
			// 批量退款
			allRefund() {
				// #ifdef MP
				openOrderRefundSubscribe().then(() => {
					uni.hideLoading();
					if (this.orderInfo.status == 0) {
						uni.navigateTo({
							url: '/pages/users/refund/index?order_id=' + this.order_id + '&refund_type=1&type=2'
						})
					} else {
						uni.navigateTo({
							url: '/pages/users/refund/select?order_id=' + this.order_id + '&type=2'
						})
					}
				}).catch(() => {
					uni.hideLoading();
				})
				// #endif
				// #ifdef H5
				if (this.orderInfo.status == 0) {
					uni.navigateTo({
						url: '/pages/users/refund/index?order_id=' + this.order_id + '&refund_type=1&type=2'
					})
				} else {
					uni.navigateTo({
						url: '/pages/users/refund/select?order_id=' + this.order_id + '&type=2'
					})
				}
				// #endif
				

			},
			// 联系客服
			goGoodCall() {
				let self = this
				uni.navigateTo({
					url: `/pages/chat/customer_list/chat?mer_id=${self.orderInfo.mer_id}&uid=${this.uid}&order_id=${this.order_id}`
				})
			},
			openSubcribe: function(e) {
				let page = e;
				uni.showLoading({
					title: '正在加载',
				})
				openOrderRefundSubscribe().then(res => {
					uni.hideLoading();
					uni.navigateTo({
						url: page,
					});
				}).catch(() => {
					uni.hideLoading();
				});
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
					phoneNumber: this.orderInfo.take.mer_take_phone
				})
			},
			/**
			 * 打开地图
			 * 
			 */
			showMaoLocation: function() {
				if (!this.orderInfo.take.mer_take_location[0] || !this.orderInfo.take.mer_take_location[1]) return this.$util.Tips({
					title: '缺少经纬度信息无法查看地图！'
				});
				uni.openLocation({
					latitude: parseFloat(this.orderInfo.take.mer_take_location[0]),
					longitude: parseFloat(this.orderInfo.take.mer_take_location[1]),
					scale: 8,
					// name: this.system_store.name,
					// address: this.system_store.address + this.system_store.detailed_address,
					success: function() {

					},
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
			 * 打开支付组件
			 * 
			 */
			pay_open: function() {
				this.pay_close = true;
				this.pay_order_id = this.orderInfo.order_id;
				this.totalPrice = this.orderInfo.pay_price;
			},
			/**
			 * 支付成功回调
			 * 
			 */
			pay_complete: function() {
				this.pay_close = false;
				this.pay_order_id = '';
				this.getOrderInfo();
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
				})
			},
			getOrderCode(){
				verifyCode(this.order_id).then(res=>{
					this.codeUrl = res.data.qrcode
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
				getOrderDetail(this.order_id).then(res => {
					// let _type = res.data._status._type;
					uni.hideLoading();
					that.$set(that, 'orderInfo', res.data);
					that.orderInfo.take = res.data.take ? res.data.take: {}
					that.$set(that, 'cartInfo', res.data.orderProduct);
					if (this.orderInfo.status == '-1') {
						this.isGoodsReturn = true;
					}
					res.data.orderProduct.map(el => {
						if (el.refund_num == 0) {
							this.refundNum.push(el)
						}
					})
					
					if(res.data.order_type == 1){
						// verifyCode
						this.getOrderCode()
					}
					
					
					// that.getOrderStatus();
				}).catch(err => {
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
			/**
			 * 打电话
			 */
			goTel: function() {
				uni.makePhoneCall({
					phoneNumber: this.orderInfo.delivery_id
				})
			},
			/**
			 * 去拼团详情
			 * 
			 */
			goJoinPink: function() {
				uni.navigateTo({
					url: '/pages/activity/goods_combination_status/index?id=' + this.orderInfo.pink_id,
				});
			},
			/**
			 * 再此购买
			 * 
			 */
			goOrderConfirm: function() {
				let that = this;
				let data = []
				this.cartInfo.map((item, index) => {
					let obj = {}
					obj.product_id = item.product_id
					obj.product_attr_unique = item.product_sku
					obj.cart_num = item.product_num
					data.push(obj)
				})

				orderAgain({
					data: data
				}).then(res => {
					let cart_id = res.data.cart_id.join(',')
					console.log(cart_id)
					return uni.navigateTo({
						url: '/pages/users/order_confirm/index?cartId=' + cart_id
					});
				});
			},
			confirmOrder: function() {
				let that = this;
				uni.showModal({
					title: '确认收货',
					content: '为保障权益，请收到货确认无误后，再确认收货',
					success: function(res) {
						if (res.confirm) {
							orderTake(that.order_id).then(res => {
								return that.$util.Tips({
									title: '操作成功',
									icon: 'success'
								}, function() {
									that.getOrderInfo();
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
			/**
			 * 
			 * 删除订单
			 */
			delOrder: function() {
				let that = this;
				orderDel(this.order_id).then(res => {
					return that.$util.Tips({
						title: '删除成功',
						icon: 'success'
					}, {
						tab: 3,
						url: 1
					});
				}).catch(err => {
					return that.$util.Tips({
						title: err
					});
				});
			},
			cancelOrder() {
				let self = this
				uni.showModal({
					title: '提示',
					content: '确认取消该订单?',
					success: function(res) {
						if (res.confirm) {
							orderCancel(self.orderInfo.order_id)
								.then((data) => {
									console.log(data)
									self.$util.Tips({
										title: data.msg
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
