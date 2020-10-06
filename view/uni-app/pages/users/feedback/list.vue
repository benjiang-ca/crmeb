<template>
	<view class="feedback-list">
		<block v-for="(item,index) in list" :key="index">
			<view class="item" @click="goDetail(item)">
				<view class="info">
					<text class="tips">{{item.type.cate_name}}</text>
					<view class="title line1">{{item.content}}</view>
				</view>
				<view class="time">{{item.create_time}}</view>
				<view class="iconfont icon-xiangyou"></view>
			</view>
		</block>
		<block v-if="list.length == 0">
			<emptyPage title="暂无数据"></emptyPage>
		</block>
	</view>
</template>

<script>
	import emptyPage from '@/components/emptyPage.vue'
	import { feedbackList } from '@/api/user.js'
	export default {
		components:{
			emptyPage
		},
		data(){
			return {
				list:[],
				page:1,
				limit:10,
				isScroll:true
			}
		},
		onLoad() {
			this.getList()
		},
		methods:{
			getList(){
				if(!this.isScroll) return
				feedbackList({
					page:this.page,
					limit:this.limit
				}).then(({data})=>{
					this.isScroll = data.list.length>=this.limit
					this.list = this.list.concat(data.list)
					this.page+=1
				})
			},
			// 详情
			goDetail(item){
				uni.navigateTo({
					url:'/pages/users/feedback/detail?id='+item.feedback_id
				})
			}
		},
		onReachBottom() {
			this.getList()
		}
	}
</script>

<style lang="scss">
.feedback-list{
	.item{
		position: relative;
		padding: 20rpx 30rpx;
		background-color: #fff;
		border-bottom: 1px solid #f5f5f5;
		.info{
			display: flex;
			align-items: center;
			font-size: 28rpx;
			color: #666;
			.title{
				flex: 1;
				padding-right: 30rpx;
			}
			.tips{
				margin-right: 10rpx;
				color: $theme-color;
			}
		}
		.time{
			margin-top: 5rpx;
			color: #BBBBBB;
			font-size: 26rpx;
		}
		.iconfont{
			position: absolute;
			right: 20rpx;
			top: 50%;
			transform: translateY(-50%);
			font-size: 26rpx;
			color: #707070;
		}
	}
}
</style>
