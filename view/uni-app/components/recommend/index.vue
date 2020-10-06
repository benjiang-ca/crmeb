<template>
	<view class='recommend'>
		<!-- <view class='title acea-row row-center-wrapper'>
			<text class='iconfont icon-zhuangshixian'></text>
			<text class='name'>热门推荐</text>
			<text class='iconfont icon-zhuangshixian lefticon'></text>
		</view> -->
		<view class="common-hd">
			<view class="title">为你推荐</view>
		</view>
		<view class='recommendList acea-row row-between-wrapper' :class="indexP?'on':''">
			<view class='item' v-for="(item,index) in hostProduct" :key="index" hover-class='none' @tap="goDetail(item)">
				<view class='pictrue'>
					<image :src='item.image'></image>
					<span class="pictrue_log_big pictrue_log_class" v-if="item.activity && item.activity.type === '1'">秒杀</span>
					<span class="pictrue_log_big pictrue_log_class" v-if="item.activity && item.activity.type === '2'">砍价</span>
					<span class="pictrue_log_big pictrue_log_class" v-if="item.activity && item.activity.type === '3'">拼团</span>
				</view>
				<view class="text">
					<view class='name line1'><text v-if="item.merchant.is_trader" class="font-bg-red">自营</text>{{item.store_name}}</view>
					<view class="acea-row row-middle">
						<view class='money font-color'>￥<text class='num'>{{item.price}}</text></view>
						<text class="coupon font-color-red" v-if="item.issetCoupon">领券</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapGetters} from "vuex";
	import { goShopDetail } from '@/libs/order.js'
	export default {
	computed: mapGetters(['uid']),
		props: {
			hostProduct: {
				type: Array,
				default: function() {
					return [];
				}
			},
			indexP:{
				type: Boolean,
				default: false
			}
		},
		data() {
			return {

			};
		},

		methods: {
			goDetail(item){
				goShopDetail(item,this.uid).then(res=>{
					uni.navigateTo({
						url:`/pages/goods_details/index?id=${item.product_id}`
					})
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	.font-bg-red{
		display: inline-block;
		background: #E93424;
		color: #fff;
		font-size: 20rpx;
		width: 58rpx;
		text-align: center;
		line-height: 34rpx;
		border-radius: 5rpx;
		margin-right: 8rpx;
	}
	.common-hd {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 118rpx;
	
		.title {
			padding: 0 80rpx;
			font-size: 34rpx;
			color: $theme-color;
			font-weight: bold;
			background-image: url("~@/static/images/index-title.png");
			background-repeat: no-repeat;
			background-size: 100% auto;
			background-position: left center;
		}
	}
	// .recommend {
	// 	background-color: #fff;
	// }

	// .recommend .title {
	// 	height: 135rpx;
	// 	font-size: 28rpx;
	// 	color: #282828;
	// }

	// .recommend .title .name {
	// 	margin: 0 28rpx;
	// }

	// .recommend .title .iconfont {
	// 	font-size: 170rpx;
	// 	color: #454545;
	// }

	// .recommend .title .iconfont.lefticon {
	// 	transform: rotate(180deg);
	// }

	.recommend .recommendList {
		padding: 0 20rpx;
		width: 710rpx;
	}
	
	.recommend .recommendList.on{
		padding: 0;
	}

	.recommend .recommendList .item {
		width: 345rpx;
		margin-bottom: 30rpx;
		background-color: #fff;
		border-radius: 16rpx;
		padding-bottom: 20rpx;
	}
	
	.recommend .recommendList .item .text{
		padding: 0 20rpx;
	}
	
	.recommend .recommendList .item .coupon{
		background:rgba(255,248,247,1);
		border:1px solid rgba(233,51,35,1);
		border-radius:4rpx;
		font-size:20rpx;
		margin-left: 18rpx;
		padding: 1rpx 4rpx;
	}

	.recommend .recommendList .item .pictrue {
		position: relative;
		width: 100%;
		height: 345rpx;
	}

	.recommend .recommendList .item .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 16rpx 16rpx 0 0;
	}

	.recommend .recommendList .item .name {
		font-size: 28rpx;
		color: #282828;
		margin: 20rpx 0 10rpx 0;
	}

	.recommend .recommendList .item .money {
		font-size: 20rpx;
		font-weight: bold;
	}

	.recommend .recommendList .item .money .num {
		font-size: 34rpx;
	}
</style>
