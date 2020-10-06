<template>
	<view class="refund-list">
		<view class="tab-box">
			<view class="item" v-for="(item,index) in tabList" :key="index" :class="{'active':index == tabIndex}" @click="bindTab(index)">{{item.title}}</view>
		</view>
		<view class="goods-wrapper">
			<view class="info-box" v-for="(item,index) in goodsList" :key="index">
				<view class="title" @click="goStore(item)">
					<text class="iconfont icon-shangjiadingdan"></text>
					<text class="txt">{{item.merchant.mer_name}}</text>
					<text class="iconfont icon-xiangyou"></text>
				</view>
				<view class="product-box">
					<view class="product-item" v-for="goods in item.refundProduct" :key="goods.order_product_id">
						<image class="img-box" :src="goods.product.cart_info.product.image" mode=""></image>
						<view class="msg">
							<view class="name line1">{{goods.product.cart_info.product.store_name}}</view>
							<view class="des">{{goods.product.cart_info.productAttr.sku}}</view>
							<view class="price">{{goods.product.product_price}}</view>
							<view class="num">x {{goods.product.product_num}}</view>
						</view>
					</view>
				</view>
				<view class="btn-box" v-if="item.status == 1">
					<view class="btn gray" @click="goDetail(item)">查看详情</view>
					<view class="btn"  @click="goPage(item.refund_order_id)">退回商品</view>
				</view>
				<view class="btn-box" v-else-if="item.status == -1">
					<view class="btn gray" @click="goDetail(item)">查看详情</view>
					<view class="btn" @click="applyAgain(item)">再次申请</view>
				</view>
				<view class="btn-box" v-else>
					<view class="btn gray" v-if="item.status == 3" @click="bindDetele(item,index)">删除记录</view>
					<view class="btn" @click="goDetail(item)">查看详情</view>
				</view>
				<view class="status">
					<!-- 0审核中 1待发货 2待收货 3已退款 -1已拒绝 -->
					<block v-if="item.status == 0">
						<text class="iconfont icon-shenhezhong1 red-color"></text>
					</block>
					<block v-if="item.status == 1">
						<text class="iconfont icon-daituihuo"></text>
					</block>
					<block v-if="item.status == 2">
						<text class="iconfont icon-tuihuozhong"></text>
					</block>
					<block v-if="item.status == 3">
						<text class="iconfont icon-yituikuan"></text>
					</block>
					<block v-if="item.status == -1">
						<text class="iconfont icon-yijujue1"></text>
					</block>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { refundList,refundDel } from '@/api/order.js'
	export default{
		data(){
			return {
				tabIndex:0,
				tabList:[
					{
						title:'全部'
					},
					{
						title:'处理中'
					},
					{
						title:'已处理'
					},
				],
				goodsList:[],
				isScroll:true,
				page:1,
				limit:15
			}
		},
		onLoad() {
			this.getList();
		},
		methods:{
			goStore(item){
				uni.navigateTo({
					url:`/pages/store/home/index?id=${item.merchant.mer_id}`
				})
			},
			goPage(id){
				uni.navigateTo({
					url:'/pages/users/refund/goods/index?id='+id
				})
			},
			applyAgain(item){
				uni.navigateTo({
					url:`/pages/order_details/index?order_id=${item.refundProduct[0].product.order_id}`
				})
			},
			bindTab(index){
				this.tabIndex = index
				this.page =1
				this.isScroll = true
				this.goodsList = []
				this.getList()
			},
			getList(){
				if(!this.isScroll) return
				refundList({
					type:this.tabIndex,
					page:this.page,
					limit:this.limit
				}).then(({data})=>{
					this.isScroll = data.list.length>=this.limit
					this.goodsList = this.goodsList.concat(data.list)
					this.page+=1
				})
			},
			// 去详情页
			goDetail(item){
				uni.navigateTo({
					url:'/pages/users/refund/detail?id='+item.refund_order_id
				})
			},
			// 删除记录
			bindDetele(item,index){
				let self = this
				uni.showModal({
					title: '提示',
					content: '确定删除该记录吗？',
					success: function (res) {
						if (res.confirm) {
								refundDel(item.refund_order_id).then(res=>{
									self.goodsList.splice(index,1)
								})
								uni.showToast({
									title:'删除成功',
									icon:'none'
								})
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
				
			}
		},
		onReachBottom() {
			this.getList();
		}
	}
</script>
<style lang="scss">
	.refund-list{
		.tab-box{
			z-index: 50;
			position: fixed;
			left: 0;
			top: 0;
			width: 100%;
			display: flex;
			.item{
				flex: 1;
				display: flex;
				justify-content: center;
				align-items: center;
				height: 90rpx;
				background-color: #fff;
				border-bottom: 1px solid transparent;
				&.active{
					color: $theme-color;
					border-color: $theme-color;
				}
			}
		}
		.goods-wrapper{
			margin-top: 102rpx;
		}
		.info-box{
			position: relative;
			margin-top: 12rpx;
			background-color: #fff;
			
			.title{
				display: flex;
				align-items: center;
				padding: 0 32rpx;
				height: 86rpx;
				border-bottom: 1px solid #F0F0F0;
				color: #282828;
				.icon-shangjiadingdan{
					font-size: 32rpx;
				}
				.txt{
					margin: 0 5rpx ;
				}
				.icon-xiangyou{
					color: #999;
					font-size: 20rpx;
					margin-top: 6rpx;
				}
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
			.btn-box{
				display: flex;
				justify-content: flex-end;
				padding:0 20rpx 20rpx;
				.btn{
					width:176rpx;
					height:60rpx;
					line-height: 60rpx;
					margin-left: 18rpx;
					text-align: center;
					background:$theme-color;
					border-radius:30rpx;
					color: #fff;
					font-size: 27rpx;
					&.gray{
						border:1px solid #ddd;
						background: transparent;
						color: #aaa;
					}
				}
			}
			.status{
				position: absolute;
				right: 30rpx;
				top: 0;
				.iconfont{
					font-size: 120rpx;
					opacity: .3;
				}
				.red-color{
					color: $theme-color;
				}
			}
		}
	}
</style>
