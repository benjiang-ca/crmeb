<template>
	<view>
		<view class="navTabBox">
		<!-- 	<view class="longTab">
				<view class="tab-item" :class='isUsed == 0 ? "" : "on"' @click="getUseCoupons()"><text>未使用</text></view>
				<view class="tab-item" :class='isUsed !== 0 ? "" : "on"' @click="getUseCoupons()"><text>已使用/已过期</text></view>
			</view> -->
			<view class="longTab">
				<view class="tab-item" v-for="(item,index) in tabList" :key="index" :class="{'on':index == tabIndex-1}" @click="bindTab(index)"><text>{{item.title}}</text></view>
			</view>
		</view>
		<view class='coupon-list' v-if="couponsList.length">
			<view class='item acea-row row-center-wrapper' v-for='(item,index) in couponsList' :key="index">
				<view class='money' :class='item.status == 0 ? "" : "moneyGray"'>
					<view>￥<text class='num'>{{item.coupon_price}}</text></view>
					<view class="pic-num">满{{ item.use_min_price }}元可用</view>
				</view>
				<view class='text'>
					<view class='condition line1'>
						<view class="line-title" :class="item.status === 0 ? 'bg-color-huic' : 'bg-color-check'" v-if="item.coupon.type === 0">店铺券</view>
						<view class="line-title" :class="item._type === 0 ? 'bg-color-huic' : 'bg-color-check'" v-else-if="item.applicable_type === 1">品类券</view>
						<view class="line-title" :class="item._type === 0 ? 'bg-color-huic' : 'bg-color-check'" v-else>商品券</view>
						<view>{{item.coupon_title}}</view>
					</view>
					<view class='data acea-row row-between-wrapper'>
						<view>{{item.start_time | timeYMD}}-{{item.end_time | timeYMD}}</view>
						<view class='bnt gray bg-color' v-if="item.status==0">可使用</view>
						<view class='bnt gray' v-if="item.status==1">已使用</view>
						<view class='bnt gray' v-if="item.status==2">已过期</view>
					</view>
				</view>
			</view>
		</view>
		<view class='noCommodity' v-if="!couponsList.length && loading==true">
			<view class='pictrue'>
				<image src='/static/images/noCoupon.png'></image>
			</view>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		getUserCoupons
	} from '@/api/api.js';
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
		data() {
			return {
				tabIndex:1,
				couponsList: [],
				loading: false,
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false ,//是否隐藏授权
				isUsed: 0,
				tabList:[
					{
						title:'未使用'
					},
					{
						title:'已使用/已过期'
					}
				],
			};
		},
		computed: mapGetters(['isLogin']),
		filters: {
		  timeYMD: function (value) {
				if(value){
					var newDate=/\d{4}-\d{1,2}-\d{1,2}/g.exec(value)
					return newDate[0]
				}
		  }
		},
		onLoad() {
			if (this.isLogin) {
				this.getUseCoupons();
			} else {
				// #ifdef H5 || APP-PLUS
				toLogin();
				// #endif 
				// #ifdef MP
				this.isAuto = true;
				this.$set(this, 'isShowAuth', true)
				// #endif
			}
		},
		methods: {
			/**
			 * 授权回调
			 */
			onLoadFun: function() {
				this.isShowAuth = false;
				this.getUseCoupons();
			},
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			bindTab(index){
				this.tabIndex = index+1
				// this.page =1
				// this.isScroll = true
				// this.goodsList = []
				this.getUseCoupons()
			},
			/**
			 * 获取领取优惠券列表
			 */
			getUseCoupons: function() {
				let that = this;
				getUserCoupons({statusTag: this.tabIndex}).then(res => {
					that.loading = true;
					that.$set(that, 'couponsList', res.data.list);
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	.navTabBox{
		background: #fff;
		height: 90rpx;
	}
	.longTab{
		display: -webkit-flex; /* Safari */
		display: flex;
		text-align: center;
	}
	.tab-item{
		width: 50%;
		display: inline-block;
		line-height: 90rpx;	
		text{
			position: relative;
			display: inline-block;
		}
	}
	.tab-item.on{
		color: #EA3424;
		text{
			&:after{
				content: "";
				display: inline-block;
				width: 90%;
				height: 3rpx;
				background: #EA3424;
				position: absolute;
				left: 5%;
				bottom: 0;
			}
		}
	}
	.money {
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	.pic-num {
		color: #ffffff;
		font-size: 20rpx;
	}

	.coupon-list .item .text .condition {
		display: flex;
		align-items: center;
	}

	.condition .line-title {
		width: 90rpx;
		height: 40rpx !important;
		line-height: 1.5 !important;
		padding: 0 10rpx;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		background: rgba(255, 247, 247, 1);
		border: 1px solid rgba(232, 51, 35, 1);
		opacity: 1;
		border-radius: 22rpx;
		font-size: 20rpx !important;
		color: #e83323;
		margin-right: 12rpx;
	}
</style>
