<template>
	<view class="user_store_attention">
		<view class="item" v-for="(item,index) in storeList" :key="index" @click="goStore(item)">
			<image :src="item.merchant.mer_avatar" mode=""></image>
			<view class="info">
				<view class="name line1">{{item.merchant.mer_name}}<text v-if="item.merchant.is_trader" class="font-bg-red ml8">自营</text></view>
				<view class="des">{{item.merchant.care_count || 0}}人关注</view>
				<view class="btn" @click.stop="bindDetele(item,index)">取消关注</view>
			</view>
		</view>
		<view class="empty-txt" v-if="storeList.length == 0">暂无数据</view>
	</view>
</template>

<script>
	import { getMerchantLst,collectDel } from '@/api/store.js'
	export default{
		data(){
			return {
				storeList:[],
				isScroll:true,
				page:1,
				limit:20
			}
		},
		onLoad() {
			this.getList()
		},
		methods:{
			goStore(item){
				uni.navigateTo({
					url:`/pages/store/home/index?id=${item.merchant.mer_id}`
				})
			},
			getList(){
				if(!this.isScroll) return
				getMerchantLst({
					page:this.page,
					limit:this.limit
				}).then(res=>{
					this.isScroll = res.data.list.length>=this.limit
					this.storeList = this.storeList.concat(res.data.list)
					this.page+=1
				})
			},
			// 删除收藏
			bindDetele(item,index){
				collectDel({
					type:10,
					type_id:item.type_id
				}).then(res=>{
					uni.showToast({
						title:'已取消',
						icon:'none'
					})
					this.storeList.splice(index,1)
				})
			}
		},
		onReachBottom() {
			this.getList()
		}
	}
</script>

<style lang="scss">
.user_store_attention{
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
		&.ml8{
			margin-left: 8rpx;
			margin-right: 0;
		}
	}
	.item{
		position: relative;
		display: flex;
		padding: 30rpx 20rpx;
		background-color: #fff;
		&::after{
			content: ' ';
			position: absolute;
			bottom: 0;
			left: 30rpx;
			right: 0;
			height: 1px;
			background: #f0f0f0;
		}
		image{
			width: 88rpx;
			height: 88rpx;
			border-radius: 50%;
		}
		.info{
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			margin-left: 20rpx;
			position: relative;
			.name{
				width: 410rpx;
				font-weight: bold;
			}
			.des{
				color: #666666;
				font-size: 22rpx;
			}
			.btn{
				display: flex;
				align-items: center;
				justify-content: center;
				position: absolute;
				right: 0;
				top: 50%;
				width:150rpx;
				height:50rpx;
				transform: translateY(-50%);
				border:1px solid #BBBBBB;
				border-radius:25rpx;
				font-size: 26rpx;
			}
		}
	}
}

</style>
