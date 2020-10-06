<template>
	<view class="refund-detail">
		<view class="head">
			<!-- status 0审核中 1待发货 2待收货 3已退款 -1已拒绝 -->
			<block v-if="type == -1">
				<view class="txt">退款失败</view>
				<view class="time">申请时间：{{detail.status_time}}</view>
			</block>
			<block v-if="type == 0">
				<view class="txt">审核中</view>
				<view class="time">申请时间：{{detail.status_time}}</view>
			</block>
			<block v-if="type == 1">
				<view class="txt">请退货并填写物流信息</view>
				<view class="time">申请时间：{{detail.status_time}}</view>
			</block>
			<block v-if="type == 2">
				<view class="txt">请等待商家收货并退款</view>
				<view class="time">还剩：				
				<countDown :is-day="true" :tip-text="' '" :day-text="' '" :hour-text="':'" :minute-text="':'" :second-text="' '"
				 :datatime="datatime"></countDown>
				</view>
			</block>
			<block v-if="type == 3">
				<view class="txt">退款成功，金额 ¥{{detail.refund_price}}</view>
				<view class="time">申请时间：{{detail.status_time}}</view>
			</block>
		</view>
		<!-- 拒绝 -->
		<view class="box" v-if="type == -1">
			<view class="txt">拒绝退款原因</view>
			<view class="des">{{detail.fail_message}}</view>
		</view>
		<!-- 待退货 -->
		<view class="info-box" v-if="type == 1">
			<view class="title">商家已同意您的退货申请，请尽早退货</view>
			<view class="store-info">
				<view class="text">收货人姓名：<text class="info">{{detail.mer_delivery_user}}</text></view>
				<view class="text">收货人联系方式：<text class="info">{{detail.phone}}</text></view>
				<view class="text">收货人地址：<text class="des">{{detail.mer_delivery_address}}</text></view>
				<view class="red-txt">
					<text class="iconfont icon-zhuyi-copy"></text>请按以上收货信息将商品退回
				</view>
			</view>
		</view>
		<!-- 待收货 -->
		<view class="info-box" v-if="type == 2">
			<view class="title" style="color:#E93323">商家收货并验货无误，将操作退款给您</view>
			<view class="store-info">
				<view class="text">收货人姓名：<text class="info">{{detail.mer_delivery_user}}</text></view>
				<view class="text">收货人联系方式：<text class="info">{{detail.phone}}</text></view>
				<view class="text">收货人地址：<text class="des">{{detail.mer_delivery_address}}</text></view>
			</view>
		</view>
		<view class="info-box">
			<view class="title">退款信息</view>
			<view class="product-box">
				<view class="product-item" v-for="(item,index) in detail.refundProduct" :key="index">
					<image class="img-box" :src="item.product.cart_info.product.image" mode=""></image>
					<view class="msg">
						<view class="name line1">{{item.product.cart_info.product.store_name}}</view>
						<view class="des">{{item.product.cart_info.productAttr.sku}}</view>
						<view class="price">￥{{item.product.cart_info.productAttr.price}}</view>
						<view class="num">x {{item.refund_num}}</view>
					</view>
				</view>
			</view>
		</view>
		<view class="content">
			<view class="item">
				<view class="label">订单编号：</view>
				<view class="txt flex">
					<text>{{detail.refund_order_sn}}</text>
					<!-- #ifdef H5 -->
						<text class='copy copy-data' :data-clipboard-text="detail.refund_order_sn">复制</text>
					<!-- #endif -->
					<!-- #ifdef MP -->
						<text class='copy' @tap='copy'>复制</text>
					<!-- #endif -->
				</view>
			</view>
			<view class="item">
				<view class="label">退款金额：</view>
				<view class="txt flex">
					<text>¥ {{detail.refund_price}}</text>
				</view>
			</view>
			<view class="item">
				<view class="label">申请件数：</view>
				<view class="txt flex">
					<text>{{detail.refund_num}}</text>
				</view>
			</view>
			<view class="item">
				<view class="label">申请时间：</view>
				<view class="txt flex">
					<text>{{detail.create_time}}</text>
				</view>
			</view>
			<view class="item">
				<view class="label">备注说明：</view>
				<view class="txt flex">
					<text>{{detail.mark}}</text>
				</view>
			</view>
			<view class="btn-wrapper">
				<block v-if="type==-1">
					<view class="btn gray" @click="goService">联系商家</view>
					<view class="btn" @click="applyAgain(detail)">再次申请</view>
				</block>
				<block v-else-if="type==1">
					<view class="btn gray" @click="goService">联系商家</view>
					<view class="btn" @click="goPage">退回商品</view>
				</block>
				<block v-else-if="type==2">
					<view class="btn gray" @click="goService">联系商家</view>
					<view class="btn" @click="go">查看物流</view>
				</block>
				<block v-else>
					<view class="btn" @click="goService">联系商家</view>
				</block>
			</view>
		</view>
	</view>
</template>
<script>
	import { refundDetail } from '@/api/order.js'
	import ClipboardJS from "@/plugin/clipboard/clipboard.js";
	import countDown from '@/components/countDown'
	export default{
		components: {
			countDown,	
		},
		data(){
			return{
				type:0,
				refund_order_id:0,
				detail:'',
				// countDownHour: "00",
				// countDownMinute: "00",
				// countDownSecond: "00",
				datatime: 0
			}
		},
		onLoad(optios) {
			uni.setNavigationBarTitle({
			　　title:'退款详情'
			})
			this.refund_order_id = optios.id
			this.getDetail()
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
		methods:{
			getDetail(){
				refundDetail(this.refund_order_id).then(res=>{
					// status 0审核中 1待发货 2待收货 3已退款 -1已拒绝 
					this.type = res.data.status
					this.detail = res.data
					this.datatime = res.data.auto_refund_time;
					
				})
			},
			goPage(){
				uni.navigateTo({
					url:'/pages/users/refund/goods/index?id='+this.detail.refund_order_id
				})
			},
			applyAgain(item){
				uni.navigateTo({
					url:`/pages/order_details/index?order_id=${item.refundProduct[0].product.order_id}`
				})
			},
			go(){
				uni.navigateTo({
					url:`/pages/users/refund/logistics?orderId=${this.detail.refund_order_id}`
				})
			},
			/**
			 * 
			 * 剪切订单号
			 */
			// #ifndef H5
			copy: function() {
				let that = this;
				uni.setClipboardData({
					data: this.detail.refund_order_sn
				});
			},
			// #endif
			// 客服
			goService(){
				uni.navigateTo({
					url:`/pages/chat/customer_list/chat?mer_id=${this.detail.mer_id}&uid=${this.detail.uid}&refund_order_id=${this.detail.refund_order_id}`
				})
			}
		}
	}
</script>

<style lang="scss">
.refund-detail{
	.head{
		display: flex;
		flex-direction: column;
		justify-content: center;
		height: 150rpx;
		padding: 0 30rpx;
		color: #fff;
		background-color: #666666;
		font-size: 30rpx;
		.txt{
			font-weight: bold;
		}
		.time{
			margin-top: 10rpx;
			font-size: 24rpx;
			opacity: .8;
			.time{
				display: inline-block;
				width: 600rpx;
				/deep/ .red{
					color: #fff;
				}
			}
		}
	}

	.info-box{
		margin-top: 12rpx;
		background-color: #fff;
		.title{
			padding: 0 32rpx;
			line-height: 86rpx;
			border-bottom: 1px solid #F0F0F0;
			color: #282828;
		}
		.product-box{
			.product-item{
				display: flex;
				padding: 25rpx 30rpx;
				.img-box{
					width:130rpx;
					height:130rpx;
					border-radius:16rpx;
				}
				.msg{
					position: relative;
					display: flex;
					flex-direction: column;
					justify-content: space-between;
					width: 440rpx;
					margin-left: 26rpx;
					.name{
						font-size: 28rpx;
						color: #282828;
					}
					.des{
						font-size: 20rpx;
						color: #868686;
					}
					.price{
						font-size: 26rpx;
						color: $theme-color;
					}
					.num{
						position: absolute;
						right: -80rpx;
						top: 4rpx;
						color: #868686;
						font-size: 26rpx;
					}
				}
			}
		}
		.store-info{
			padding: 30rpx;
			.des{
				margin-top: 10rpx;
				font-size: 26rpx;
				color: #868686;
			}
			.red-txt{
				display: flex;
				align-items: center;
				margin-top: 10rpx;
				color: $theme-color;
				font-size: 24rpx;
				.iconfont{
					font-size: 30rpx;
					margin-right: 5rpx;
					margin-top: 6rpx;
				}
			}
		}
	}
	.content{
		margin-top: 12rpx;
		padding: 30rpx 30rpx 0;
		background-color: #FFFFFF;
		.item{
			display: flex;
			justify-content: space-between;
			margin-bottom: 30rpx;
			.txt{
				justify-content: flex-end;
				align-items: center;
				width: 420rpx;
				color: #868686;
				text-align: right;
				.copy{
					display: flex;
					align-items: center;
					justify-content: center;
					width:76rpx;
					height:34rpx;
					margin-left: 20rpx;
					border:1px solid #666666;
					border-radius:17rpx;
					font-size: 20rpx;
					color: #333;
				}
			}
			&:last-child{
				margin-bottom: 0;
			}
		}	
		.btn-wrapper{
			position: relative;
			display: flex;
			justify-content: flex-end;
			align-items: center;
			height: 100rpx;
			button{
				font-size: 28rpx;
			}
			.btn{
				width:176rpx;
				height:60rpx;
				margin-left: 20rpx;
				line-height: 60rpx;
				background:$theme-color;
				border-radius:30rpx;
				text-align: center;
				color: #fff;
			}
			.gray{
				background: transparent;
				border: 1px solid #eee;
				color: #AAAAAA;
			}
			&:after{
				content:' ';
				position: absolute;
				top: 0;
				left: 50%;
				width: 690rpx;
				height:1px;
				margin-left: -345rpx;
				background-color: #f0f0f0;
			}
		}
	}
	.box{
		margin-top: 12rpx;
		padding:25rpx 30rpx;
		background-color: #fff;
		font-size: 30rpx;
		color: #282828;
		.des{
			margin-top: 5rpx;
			font-size: 26rpx;
			color: #868686;
		}
	}
}
</style>
