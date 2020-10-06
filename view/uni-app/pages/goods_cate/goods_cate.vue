<template>
	<view class='productSort' :style="'height:'+winHeight+'px'">
		<view class='header acea-row row-center-wrapper'>
			<navigator url="/pages/columnGoods/goods_search/index" class='acea-row row-between-wrapper input' hover-class="none">
				<text class='iconfont icon-sousuo'></text>
				<view class="input-box">点击搜索商品信息</view>
			</navigator>
		</view>
		<view class="con-box">
			<view class='aside'>
				<scroll-view scroll-y="true" style="height: 100%; overflow: hidden;" scroll-with-animation='true'>
				<view class='item acea-row row-center-wrapper' :class='index==navActive?"on":""' v-for="(item,index) in productList"
				 :key="index" @click='tap(index,"b"+index)'><text class="item_text">{{item.cate_name}}</text></view>
				</scroll-view>
			</view>
			<view class='conter' v-if="productList.length>0">
				<scroll-view scroll-y="true" style="height: 100%; overflow: hidden;" @scroll="scroll" scroll-with-animation='true'>
					<block v-for="(item,index) in productList[navActive].children" :key="index" v-if="productList[navActive].children">
						<view class='listw' :id="'b'+index">
							<view class='title acea-row'>
								<view class='name'>{{item.cate_name}}</view>
							</view>
							<view class='list acea-row'>
								<block v-for="(itemn,indexn) in item.children" :key="indexn">
									<!-- :url='"/pages/columnGoods/goods_list/index"+itemn.id+"&title="+itemn.cate_name' -->
									<navigator hover-class='none' :url="'/pages/columnGoods/goods_list/index?id='+itemn.store_category_id+'&title='+itemn.cate_name"
									 class='item acea-row row-column row-middle'>
										<view class='picture'>
											<image :src='itemn.pic'></image>
										</view>
										<view class='name line1'>{{itemn.cate_name}}</view>
									</navigator>
								</block>
							</view>
						</view>
					</block>
				</scroll-view>
			</view>
		</view>

	</view>
</template>

<script>
	let app = getApp();
	import {
		getCategoryList
	} from '@/api/store.js';
	export default {
		data() {
			return {
				navlist: [],
				productList: [],
				navActive: 0,
				number: "",
				height: 0,
				hightArr: [],
				toView: "",
				winHeight:0
			}
		},
		onLoad(options) {
			let that = this
			this.getAllCategory();
			uni.getSystemInfo({
				success: function(res) {
					that.winHeight = res.windowHeight
				},
			});
			// #ifdef H5
			console.log(this.$route)
			document.body.addEventListener('touchmove', function (event) {
				if(that.$route.path == '/pages/goods_cate/goods_cate'){
					 event.preventDefault();
				}
			}, {passive:false});
			// #endif
		},
		onShow() {
			let value = ""
			try {
				value = uni.getStorageSync('storeIndex') ? uni.getStorageSync('storeIndex') : this.productList[0].store_category_id

				uni.removeStorageSync('storeIndex')

			} catch (e) {}
			this.productList.map((item, index) => {
				if (item.store_category_id == value) {
					this.navActive = index
				}
			})
		},
		methods: {
			infoScroll: function() {
				let that = this;
				let len = that.productList.length;
				//this.number = that.productList[len - 1].children.length;
				//设置商品列表高度
				uni.getSystemInfo({
					success: function(res) {
						that.height = (res.windowHeight) * (750 / res.windowWidth) - 98;
					},
				});
				// let height = 0;
				// let hightArr = [];
				// for (let i = 0; i < len; i++) {
				// 	//获取元素所在位置
				// 	let query = uni.createSelectorQuery().in(this);
				// 	let idView = "#b" + i;
				// 	query.select(idView).boundingClientRect();
				// 	query.exec(function(res) {
				// 		let top = res[0].top;
				// 		hightArr.push(top);
				// 		that.hightArr = hightArr
				// 	});
				// };
			},
			tap: function(index, id) {
				this.toView = id;
				this.navActive = index;
			},
			getAllCategory: function() {
				let that = this;
				let value = ""
				getCategoryList().then(res => {
					that.productList = res.data;
					this.infoScroll()
				})
			},
			scroll: function(e) {
				let scrollTop = e.detail.scrollTop;
				let scrollArr = this.hightArr;
				for (let i = 0; i < scrollArr.length; i++) {
					if (scrollTop >= 0 && scrollTop < scrollArr[1] - scrollArr[0]) {
						this.navActive = 0
					} else if (scrollTop >= scrollArr[i] - scrollArr[0] && scrollTop < scrollArr[i + 1] - scrollArr[0]) {
						this.navActive = i
					} else if (scrollTop >= scrollArr[scrollArr.length - 1] - scrollArr[0]) {
						this.navActive = scrollArr.length - 1
					}
				}
			},
			searchSubmitValue: function(e) {
				if (this.$util.trim(e.detail.value).length > 0)
					uni.navigateTo({
						url: '/pages/columnGoods/goods_list/index?searchValue=' + e.detail.value
					})
				else
					return this.$util.Tips({
						title: '请填写要搜索的产品信息'
					});
			},
		}
	}
</script>


<style scoped lang="scss">
	.productSort {
		display: flex;
		flex-direction: column;
		width: 100%;
		.con-box {
			flex: 1;
			display: flex;
			overflow: hidden;
		}
	}

	.productSort .header {
		width: 100%;
		height: 96rpx;
		background-color: #fff;
		border-bottom: 1rpx solid #f5f5f5;
	}

	.productSort .header .input {
		width: 700rpx;
		height: 60rpx;
		background-color: #f5f5f5;
		border-radius: 50rpx;
		box-sizing: border-box;
		padding: 0 25rpx;
	}

	.productSort .header .input .iconfont {
		font-size: 35rpx;
		color: #555;
	}

	.productSort .header .input .placeholder {
		color: #999;
	}

	.productSort .header .input .input-box {
		display: flex;
		align-items: center;
		font-size: 26rpx;
		height: 100%;
		width: 597rpx;
		color: #999999;
	}

	.productSort .aside {
		background-color: #fff;
		overflow-y: auto;
		overflow-x: hidden;
		width: 200rpx;
		height: 100%;
		overflow: hidden;
	}

	.productSort .aside .item {
		height: 100rpx;
		width: 100%;
		font-size: 26rpx;
		color: #424242;
	}
	.productSort .aside .item_text{
		padding-left: 20rpx;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;	
	}
	.productSort .aside .item_text .text{
			
	}

	.productSort .aside .item.on {
		background-color: #f7f7f7;
		border-left: 4rpx solid #fc4141;
		width: 100%;
		text-align: center;
		color: #fc4141;
		font-weight: bold;
	}

	.productSort .conter {
		flex: 1;
		height: 100%;
		padding: 0 14rpx;
		background-color: #f7f7f7;
	}

	.productSort .conter .listw {
		// padding-top: 20rpx;
	}

	.productSort .conter .listw .title {
		height: 100rpx;
		align-items: center;
	}

	.productSort .conter .listw .title .line {
		width: 100rpx;
		height: 2rpx;
		background-color: #f0f0f0;
	}

	.productSort .conter .listw .title .name {
		font-size: 28rpx;
		color: #333;
		margin: 0 30rpx;
		font-weight: bold;
	}

	.productSort .conter .list {
		flex-wrap: wrap;
		background: #fff;
		border-radius: 16rpx;
		padding-bottom: 26rpx;
	}

	.productSort .conter .list .item {
		width: 174rpx;
		margin-top: 26rpx;
	}

	.productSort .conter .list .item .picture {
		width: 110rpx;
		height: 110rpx;
		border-radius: 50%;
	}

	.productSort .conter .list .item .picture image {
		width: 100%;
		height: 100%;
	}

	.productSort .conter .list .item .name {
		font-size: 24rpx;
		color: #333;
		height: 56rpx;
		line-height: 56rpx;
		width: 120rpx;
		text-align: center;
	}
</style>
