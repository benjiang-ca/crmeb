<template>
	<view class="orderGoods">
		<view class='goodWrapper'>
			<view class='item acea-row row-between-wrapper' v-for="(item,index) in cartInfo" :key="index">
				<view class='pictrue' @click="jumpCon(item.product_id)">
					<image :src='item.cart_info.product.image' ></image>
				</view>
				<view class='text'>
					<view class='acea-row row-between-wrapper'>
						<view class='name line1'>{{item.cart_info.product.store_name}}</view>
						<view class='num'>x {{item.product_num}}</view>
					</view>
					<view class='attr line1' v-if="item.cart_info.productAttr.sku">{{item.cart_info.productAttr.sku}}</view>
					<view class='money font-color' >￥{{item.cart_info.productAttr.price}}</view>
					<view class="right-btn-box">
						<view class="btn-item" v-if="item.is_refund ==0" @click.stop="refund(item)">申请退款</view>
						<view class="btn-item err" v-if="item.is_refund ==1">退款中</view>
						<view class='btn-item err' v-if="item.is_refund >1">已退款</view>
						<view class='btn-item' v-if='item.is_reply==0 && evaluate==2' @click.stop="evaluateTap(item.order_product_id,orderId)">去评价</view>
						<view class='btn-item on' v-else-if="item.is_reply==1 && evaluate==2">已评价</view>
						
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		openOrderRefundSubscribe
	} from '@/utils/SubscribeMessage.js';
	export default {
		props: {
			evaluate: {
				type: Number,
				default: 0,
			},
			cartInfo: {
				type: Array,
				default: function() {
					return [];
				}
			},
			orderId: {
				type: String,
				default: '',
			},
			jump: {
				type: Boolean,
				default: false,
			}
		},
		data() {
			return {
				totalNmu:''
			};
		},
		watch:{
			cartInfo:function(nVal,oVal){
				let num = 0
				nVal.forEach((item,index)=>{
					num += item.cart_num
				})
				this.totalNmu = num
			}
		},
		mounted() {
			console.log(this.cartInfo,'cartInfo')
		},
		methods: {
			evaluateTap:function(unique,orderId){
				uni.navigateTo({
					url:`/pages/users/goods_comment_con/index?uni=${unique}&order_id=${orderId}`
				})
			},
			jumpCon:function(id){
				if(this.jump){
					uni.navigateTo({
						url: `/pages/goods_details/index?id=${id}`
					})
				}
			},
			// 退款
			refund(item){
				// #ifdef MP
				openOrderRefundSubscribe().then(() => {
					uni.hideLoading();
					if(this.evaluate == 0){
						
						uni.navigateTo({
							url:'/pages/users/refund/confirm?order_id='+this.orderId+'&type=1'+'&ids='+item.order_product_id+'&refund_type=1'
						})
					}else{
						uni.navigateTo({
							url:'/pages/users/refund/select?order_id='+this.orderId+'&type=1'+'&ids='+item.order_product_id
						})
					}
				}).catch(() => {
					uni.hideLoading();
				})
				// #endif
				// #ifdef H5
				if(this.evaluate == 0){
					
					uni.navigateTo({
						url:'/pages/users/refund/confirm?order_id='+this.orderId+'&type=1'+'&ids='+item.order_product_id+'&refund_type=1'
					})
				}else{
					uni.navigateTo({
						url:'/pages/users/refund/select?order_id='+this.orderId+'&type=1'+'&ids='+item.order_product_id
					})
				}
				// #endif
			}
		}
	}
</script>

<style scoped lang="scss">
	.orderGoods {
		background-color: #fff;
	}

	.right-btn-box{
		position: absolute;
		right: 0;
		bottom: 0;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		.btn-item{
			display: flex;
			align-items: center;
			justify-content: center;
			width:140rpx;
			height:46rpx;
			margin-left: 20rpx;
			border:1px solid rgba(187,187,187,1);
			border-radius:23rpx;
			font-size: 24rpx;
			color: #282828;
			&.on{
				background:rgba(220,220,220,1);
				border-color: rgba(220,220,220,1);
			}
			&.err{
				background:rgba(247,247,247,1);
				border-color: rgba(247,247,247,1);
				color: #AAAAAA;
			}
		}
	}
</style>
