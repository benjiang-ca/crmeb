<template>
	<view>
		<view class='coupon-list-window animated' :class='coupon.coupon==true?"slideInUp":""'>
			<view class='title'>
				<view class="item" :class="{'on':tabIndex == index}" v-for="(item,index) in tabList" :key="index" @click="bindTab(item,index)">{{item}}</view>
			</view>
			<view class='coupon-list' v-if="couponArr.length">
				<view class='item acea-row row-center-wrapper' v-for="(item,index) in couponArr" @click="getCouponUser(index,item.coupon_id)"
				 :key='index'>
					<view class='money acea-row row-column row-center-wrapper' :class='item.issue?"moneyGray":""'>
						<view>￥<text class='num'>{{item.coupon_price}}</text></view>
						<view class="pic-num">满{{item.use_min_price}}元可用</view>
					</view>
					<view class='text'>
						<view class='condition line1'>
							<span class='line-title' :class='item.issue?"gray":""' v-if='item.type===0'>店铺券</span>
							<span class='line-title' :class='item.issue?"gray":""' v-else-if='item.type===1'>商品券</span>
							<span>{{item.title}}</span>
						</view>
						<view class='data acea-row row-between-wrapper'>
							<block v-if="item.coupon_type == 1">
								<view>{{ item.use_start_time |timeYMD }}-{{ item.use_end_time |timeYMD}}</view>
							</block>
							<block v-if="item.coupon_type == 0">
								<view>领取后{{ item.coupon_time}}天内可用</view>
							</block>
							<view class='bnt gray' v-if="item.issue">{{item.use_title || '已领取'}}</view>
							<view class='bnt bg-color' v-else>{{coupon.statusTile || '立即领取'}}</view>
						</view>
					</view>
				</view>
			</view>
			<!-- 无优惠券 -->
			<view class='pictrue' v-else>
				<image src='../../static/images/noCoupon.png'></image>
			</view>
		</view>
		<view class='mask' catchtouchmove="true" :hidden='coupon.coupon==false' @click='close'></view>
	</view>
</template>

<script>
	import {
		setCouponReceive
	} from '@/api/api.js';
	export default {
		props: {
			//打开状态 0=领取优惠券,1=使用优惠券
			openType: {
				type: Number,
				default: 0,
			},
			coupon: {
				type: Object,
				default: function() {
					return {};
				}
			}
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
				tabList:['商品券','店铺券'],
				tabIndex:0,
				couponArr:[]
			};
		},
		mounted() {
			this.$nextTick(function(){
				this.couponArr = this.coupon.list
				this.filterArray();
			})
		},
		methods: {
			close: function() {
				this.$emit('ChangCouponsClone');
			},
			getCouponUser: function(index, id) {
				let that = this;
				let list = this.couponArr;
				if (list[index].issue) return true;
				switch (this.openType) {
					case 0:
						//领取优惠券
						setCouponReceive(id).then(res => {
							that.$emit('ChangCouponsUseState', index);
							that.$util.Tips({
								title: "领取成功"
							});
							that.$emit('ChangCoupons', list[index]);
						})
						break;
					case 1:
						that.$emit('ChangCoupons', index);
						break;
				}
			},
			// 过滤优惠券
			filterArray(){
				if(this.tabIndex == 0){
					this.couponArr = this.coupon.list.filter(item=>{
						return item.type == 1
					})
				}else{
					this.couponArr = this.coupon.list.filter(item=>{
						return item.type == 0
					})
				}
			},
			bindTab(item,index){
				this.tabIndex = index
				this.filterArray()
			}
		}
	}
</script>

<style scoped lang="scss">
	.animated{
		animation-duration:.3s
	}
	.title{
		display: flex;
		.item{
			position: relative;
			flex: 1;
			font-size: 28rpx;
			color: #999999;
			&::after{
				content: ' ';
				position: absolute;
				left: 50%;
				bottom: 18rpx;
				width:50rpx;
				height:5rpx;
				background:transparent;
				border-radius:3px;
				transform: translateX(-50%);
			}
			&.on{
				color: #282828;
				&::after{
					background:$theme-color;
				}
			}
		}
	}
	.coupon-list{
		padding: 30rpx;
		.item{
			box-shadow:0px 2px 10px 0px rgba(0, 0, 0, 0.06);
		}
	}
	.coupon-list-window {
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100%;
		background-color: #fff;
		border-radius: 16rpx 16rpx 0 0;
		z-index: 555;
		transform: translate3d(0, 100%, 0);
		transition: all .3s cubic-bezier(.25, .5, .5, .9);
	}

	.coupon-list-window.on {
		// transform: translate3d(0, 0, 0);
		animation: aminup ;
	}

	.coupon-list-window .title {
		height: 106rpx;
		width: 100%;
		text-align: center;
		line-height: 106rpx;
		font-size: 32rpx;
		font-weight: bold;
		position: relative;
		border: 1px solid #f5f5f5;
	}

	.coupon-list-window .title .iconfont {
		position: absolute;
		right: 30rpx;
		top: 50%;
		transform: translateY(-50%);
		font-size: 35rpx;
		color: #8a8a8a;
		font-weight: normal;
	}

	.coupon-list-window .coupon-list {
		margin: 0 0 50rpx 0;
		height: 550rpx;
		overflow: auto;
	}

	.coupon-list-window .pictrue {
		width: 414rpx;
		height: 336rpx;
		margin: 0 auto 50rpx auto;
	}

	.coupon-list-window .pictrue image {
		width: 100%;
		height: 100%;
	}

	.pic-num {
		color: #fff;
		font-size: 24rpx;
	}

	.line-title {
		width: 90rpx;
		padding: 0 10rpx;
		box-sizing: border-box;
		background: rgba(255, 247, 247, 1);
		border: 1px solid rgba(232, 51, 35, 1);
		opacity: 1;
		border-radius: 20rpx;
		font-size: 20rpx;
		color: #E83323;
		margin-right: 12rpx;
	}

	.line-title.gray {
		border-color: #BBB;
		color: #bbb;
		background-color: #F5F5F5;
	}
</style>
