<template>
	<view class="feedback-detail">
		<view class="hd">
			<view class="item">
				<view class="label">反馈类型</view>
				<view class="txt">{{detail.category}}</view>
			</view>
			<view class="item">
				<view class="label">问题分类</view>
				<view class="txt">{{detail.type}}</view>
			</view>
			<view class="item">
				<view class="label">姓名</view>
				<view class="txt">{{detail.realname}}</view>
			</view>
			<view class="item">
				<view class="label">电话/邮箱</view>
				<view class="txt">{{detail.contact}}</view>
			</view>
		</view>
		<view class="content">
			<view class="con">{{detail.content}}</view>
			<view class="img-box" v-if="detail">
				<image v-for="(item,index) in detail.images" :key="index" :src="item" mode="scaleToFill"></image>
			</view>
		</view>
	</view>
</template>

<script>
	import { feedbackDetail } from '@/api/user.js'
	export default{
		data(){
			return {
				con:'等我回家考虑带我去来晚了我开了房让我来看房问了分类和南方乐乐弄了凉快凉快我洛克王国了各类隔离了哪两个纳兰容若了呢林鸿恩个哦给你绕个人纵容和内容呢人挪活人',
				id:'',
				detail:{}
			}
		},
		onLoad(options) {
			this.id = options.id
			feedbackDetail(options.id).then(res=>{
				this.detail = res.data
			})
		}
	}
</script>

<style lang="scss">
.feedback-detail{
	.hd{
		padding: 30rpx;
		background-color: #fff;
		.item{
			display: flex;
			justify-content: space-between;
			margin-bottom: 30rpx;
			font-size: 28rpx;
			color: #282828;
			&:last-child{
				margin-bottom: 0;
			}
			.txt{
				flex: 1;
				margin-left: 50rpx;
				text-align: right;
				color: #868686;
			}
		}
	}
	.content{
		position: relative;
		margin-top: 20rpx;
		padding: 30rpx 40rpx;
		line-height: 1.5;
		background-color: #fff;
	}
	.img-box{
		margin-top: 20rpx;
	}
}	
</style>
