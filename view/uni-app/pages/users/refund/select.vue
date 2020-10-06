<template>
	<view class="refund-select">
		<view class="select-item" @click="goPage(1)">
			<view class="left">
				<image src="/static/images/select01.png" mode=""></image>
			</view>
			<view class="right-wrapper">
				<view class="title">我要退款(无需退货)</view>
				<view class="txt">未收到货，或与卖家协商同意不退货只退款</view>
			</view>
			<view class="iconfont icon-xiangyou"></view>
		</view>
		<view class="select-item" @click="goPage(2)">
			<view class="left">
				<image src="/static/images/select02.png" mode=""></image>
			</view>
			<view class="right-wrapper">
				<view class="title">我要退货退款</view>
				<view class="txt">已收到货，需要退还收到的货物</view>
			</view>
			<view class="iconfont icon-xiangyou"></view>
		</view>
	</view>
</template>

<script>
	export default{
		data(){
			return {
				order_id:'',
				type:0,
				ids:''
			}
		},
		onLoad(options) {
			this.order_id = options.order_id,
			this.type = options.type
			this.ids = options.ids || ''
		},
		methods:{
			goPage(type){
				if(this.type ==1 && this.ids){
					uni.navigateTo({
						url:`/pages/users/refund/confirm?ids=${this.ids}&refund_type=${type}&type=${this.type}&order_id=${this.order_id}`
					})
				}else{
					uni.navigateTo({
						url:`/pages/users/refund/index?order_id=${this.order_id}&refund_type=${type}&type=${this.type}`
					})
				}
				
			}
		}
	}
</script>

<style lang="scss">
.refund-select{
	.select-item{
		position: relative;
		display: flex;
		align-items: center;
		padding: 25rpx 0;
		background-color: #fff;
		border-bottom: 1px solid #f0f0f0;
	}
	.left{
		display: flex;
		align-items: center;
		justify-content: center;
		width: 105rpx;
		image{
			width:50rpx;
			height: 46rpx;
		}
	}
	.right-wrapper{
		position: relative;
		.title{
			font-size: 26rpx;
			color: #282828;
		}
		.txt{
			margin-top: 10rpx;
			color: #999999;
			font-size: 22rpx;
		}
	}
	.icon-xiangyou{
		position: absolute;
		right: 30rpx;
		top: 50%;
		transform: translateY(-50%);
		color: #AAAAAA;
		font-size: 30rpx;
	}
}
</style>
