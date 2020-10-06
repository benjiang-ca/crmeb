<template>
	<view class="page-wrapper">
		<block v-if="list.length>0">
			<view class="item" :class="{gary :item.product.is_show == 1 &&item.product.status ==1 }" v-for="(item,index) in list" @click="goPage(item)" v-if="item.product">
				<image :src="item.product.image" mode=""></image>
				<view class="info">
					<view class="title line2">{{item.product.store_name}}</view>
					<view class="msg">
						<block v-if="item.product.is_show == 1 && item.product.status == 1">
							<view class="price"><text>￥</text>{{item.product.price}}</view>
							<view class="num">已售{{item.product.sales}}件</view>
						</block>
						<block v-else>
							<view class="tips">该商品已下架</view>
							<view class="btn" @click.stop="bindDelete(item,index)">删除</view>
						</block>
					</view>
				</view>
			</view>
		</block>
		<block v-else>
			<emptyPage title="暂无浏览记录~"></emptyPage>
		</block>
	</view>
</template>

<script>
	import emptyPage from '@/components/emptyPage.vue'
	import { historyList,historyDelete } from '@/api/user.js'
	export default{
		components:{
			emptyPage
		},
		data(){
			return {
				list:[],
				isScroll:true,
				page:1,
				limit:10,
			}
		},
		onShow() {
			this.list = [];
			this.isScroll = true;
			this.page = 1;
			this.getList();
		},
		methods:{
			getList(){
				if(!this.isScroll) return
				historyList({
					page:this.page,
					limit:this.limit
				}).then(({data})=>{
					this.isScroll = data.list.length>=this.limit
					this.list = this.list.concat(data.list)
					this.page+=1
				})
			},
			// 删除
			bindDelete(item,index){
				console.log(index)
				historyDelete(item.user_visit_id).then(res=>{
					this.list.splice(index,1)
					uni.showToast({
						title:res.message,
						icon:'none'
					})
				}).catch(error=>{
					uni.showToast({
						title:error,
						icon:'none'
					})
				})
			},
			goPage(item){
				uni.navigateTo({
					url:`/pages/goods_details/index?id=${item.product.product_id}`
				})
			}
		},
		onReachBottom() {
			this.getList()
		}
	}
</script>

<style lang="scss">
.page-wrapper{
	.item{
		display: flex;
		position: relative;
		padding:25rpx 20rpx ;
		background-color: #fff;
		&:after{
			content: ' ';
			position: absolute;
			left: 20rpx;
			right: 0;
			bottom: 0;
			height: 1px;
			background: #f0f0f0;
		}
		image{
			width: 170rpx;
			height: 170rpx;
			border-radius:6rpx;
		}
		.info{
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 5rpx 0;
			margin-left: 20rpx;
			.title{
				font-size: 30rpx;
				color: #999;
			}
			.msg{
				display: flex;
				align-items: center;
				justify-content: space-between;
				.price{
					color: $theme-color;
					font-size: 34rpx;
					text{
						font-size: 26rpx;
					}
				}
				.num{
					font-size: 22rpx;
					color: #AAAAAA;
				}
				.tips{
					font-size: 24rpx;
					color: #AAAAAA;
				}
				.btn{
					display: flex;
					align-items: center;
					justify-content: center;
					width:120rpx;
					height:46rpx;
					border:1px solid #999;
					border-radius:23rpx;
					font-size: 26rpx;
					color: #999;
				}
			}
		}
		&.gary{
			.info .title{
				color: #333;
			}
		}
	}

}
</style>
