<template>
	<view :style="{ 'background-image': `linear-gradient(0deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.8) 40%),url(${store.mer_banner})` }"
	 class="store-home">
		<!-- 搜索 -->
		<!-- #ifdef MP -->
		<view :style="{ height: `${systemInfo.statusBarHeight}px` }"></view>
		<view :style="{ height: `${(menuButtonInfo.top - systemInfo.statusBarHeight) * 2 + menuButtonInfo.height}px`, 'padding-right': `${(systemInfo.windowWidth - menuButtonInfo.left) * 2 + 34}rpx`}"
		 class="header">
			<view class="iconfont icon-xiangzuo" @click="goback"></view>
			<navigator :url="'/pages/store/list/index?mer_id='+id" hover-class="none" class="search"><text class="iconfont icon-xiazai5"></text>搜索商品</navigator>
		</view>
		<!-- #endif -->
		<!-- #ifdef H5 -->
		<view class="header">
			<view class="head-menu">
				<view class="iconfont icon-xiangzuo" @click="goback"></view>
				<view class="iconfont icon-shouye4" @click="goHome"></view>
			</view>
			<navigator :url="'/pages/store/list/index?mer_id='+id" hover-class="none" class="search"><text class="iconfont icon-xiazai5"></text>搜索商品</navigator>
		</view>
		<!-- #endif -->
		<view v-show="navShow && tabActive === 0" class="nav">
			<view class="nav-cont">
				<view :class="{ active: navActive === 0 }" class="item" @click="navActive = 0;select.show = !select.show">
					<view class="cont">
						{{ select.selected ? '评分' : '默认' }}
						<text :class="['arrow-icon', 'iconfont', select.show ? 'icon-xiangshang' : 'icon-xiangxia']"></text>
					</view>
				</view>
				<view :class="{ active: navActive === 1 }" class="item" @click="set_where(2)">
					<view class="cont">
						销量
					</view>
				</view>
				<view :class="{ active: navActive === 2 }" class="item" @click="set_where(3)">
					<view class="cont">
						价格
						<image :src="sortPrice ? '/static/images/up.png' : '/static/images/down.png'"></image>
					</view>
				</view>
				<view :class="{ active: navActive === 3 }" class="item" @click="set_where(4)">
					<view class="cont">
						新品
					</view>
				</view>
				<view :class="{ active: navActive === 4 }" class="item" @click="select.show = false;navActive = 4;isColumn = !isColumn">
					<view class="cont">
						<text :class="['layout-icon', 'iconfont', isColumn ? 'icon-pailie' : 'icon-tupianpailie']"></text>
					</view>
				</view>
			</view>
			<view v-show="select.show && navShow" class="select">
				<view v-for="item in select.options" :key="item.id" :class="{ active: item.id === select.selected }" class="item"
				 @click="set_where(item.id)">{{ item.name }}</view>
			</view>
		</view>
		<scroll-view class="main" scroll-y="true" @scroll="scrollHome">
			<!-- 店铺信息 -->
			<view id="store" class="store">
				<image :src="store.mer_avatar"></image>
				<view class="text">
					<navigator :url="`/pages/store/detail/index?id=${id}`" hover-class="none">
						<text v-if="store.is_trader" class="font-bg-red">自营</text>
						<text class="name">{{ store.mer_name }}</text>
						<text class="iconfont icon-xiangyou"></text>
					</navigator>
					<view class="score">
						<view class="star">
							<view :style="{ width: `${score.star.toFixed(2)}%` }"></view>
						</view>
						<view>{{ score.number.toFixed(1) }}</view>
					</view>
				</view>
				<button hover-class="none" :class="store.care ? 'care' : ''" @click="followToggle">
					<text v-show="!store.care" class="iconfont icon-guanzhu"></text>
					{{ store.care ? '已关注' : '关注' }}
				</button>
			</view>

			<view v-show="!navShow && tabActive === 0" class="nav">

				<view class="nav-cont">
					<view :class="{ active: navActive === 0 }" class="item" @click="navActive = 0;select.show = !select.show">
						<view class="cont">
							{{ select.selected ? '评分' : '默认' }}
							<text :class="['arrow-icon', 'iconfont', select.show ? 'icon-xiangshang' : 'icon-xiangxia']"></text>
						</view>
					</view>
					<view :class="{ active: navActive === 1 }" class="item" @click="set_where(2)">
						<view class="cont">
							销量
						</view>
					</view>
					<view :class="{ active: navActive === 2 }" class="item" @click="set_where(3)">
						<view class="cont">
							价格
							<image :src="sortPrice ? '/static/images/up.png' : '/static/images/down.png'"></image>
						</view>
					</view>
					<view :class="{ active: navActive === 3 }" class="item" @click="set_where(4)">
						<view class="cont">
							新品
						</view>
					</view>
					<view :class="{ active: navActive === 4 }" class="item" @click="select.show = false;navActive = 4;isColumn = !isColumn">
						<view class="cont">
							<text :class="['layout-icon', 'iconfont', isColumn ? 'icon-pailie' : 'icon-tupianpailie']"></text>
						</view>
					</view>
				</view>
				<view v-show="select.show && !navShow" class="select">
					<view v-for="item in select.options" :key="item.id" :class="{ active: item.id === select.selected }" class="item"
					 @click="set_where(item.id)">{{ item.name }}</view>
				</view>
			</view>
			<view class="tab-cont">
				<!-- 首页 -->
				<view v-show="tabActive === 0">
					<!-- 商品 -->
					<view class="goods-wrap">
						<view v-if="goods.length" :class="{ column: isColumn }" class="goods">
							<view v-for="item in goods" :key="item.product_id" class="item" @click="goGoodsDetail(item.product_id)">
								<view class="image">
									<image :src="item.image"></image>
								</view>
								<view class="text">
									<view class="name">{{ item.store_name }}</view>
									<view class="money-wrap">
										<view class="money">
											¥
											<text>{{ item.price }}</text>
										</view>
										<view class="ticket" v-if="item.issetCoupon">领券</view>
									</view>
									<view class="score">{{ item.rate }}评分 {{ item.reply_count }}条评论</view>
								</view>
								<view v-if="item.max_extension" class="foot">
									<text v-show="!isColumn" class="iconfont"></text>
									最高赚 ¥{{ item.max_extension }}
								</view>
							</view>
						</view>
						<view :hidden="!goodsLoading" class="acea-row row-center-wrapper loadingicon">
							<text class="iconfont icon-jiazai loading"></text>
						</view>
					</view>
				</view>
				<!-- 分类 -->
				<view v-show="tabActive === 1">
					<view class="category">
						<view class="section">
							<view class="head" @click="goCategoryGoods('')">
								<view class="title">全部</view>
								<view class="iconfont icon-xiangyou"></view>
							</view>
						</view>
						<view v-for="item in category" :key="item.store_category_id" class="section">
							<view class="head" @click="goCategoryGoods(item.store_category_id)">
								<view class="title">{{ item.cate_name }}</view>
								<view class="iconfont icon-xiangyou"></view>
							</view>
							<view v-if="item.children" class="body">
								<view v-for="value in item.children" :key="value.store_category_id" class="item" @click="goCategoryGoods(value.store_category_id)">{{ value.cate_name }}</view>
							</view>
						</view>
					</view>
					<view :hidden="!categoryLoading" class="acea-row row-center-wrapper loadingicon">
						<text class="iconfont icon-jiazai loading"></text>{{loadTitle}}
					</view>
				</view>
				<!-- 优惠券 -->
				<view v-show="tabActive === 2">
					<view v-if="coupon.length" class="coupon">
						<view v-for="item in coupon" :key="item.coupon_id" class="item">
							<view class="left" :class="{gary:item.issue}">
								<view class="money">
									¥
									<text>{{ item.coupon_price }}</text>
								</view>
								<view>满{{ item.use_min_price }}元可用</view>
							</view>
							<view class="right">
								<view class="name">
									<text :class="{gary:item.issue}">{{item.type===0?'店铺券':'商品券'}}</text>
									<!--购物满{{ item.use_min_price }}元可用-->
									{{ item.title }}
								</view>
								<view class="time-wrap" style="justify-content: space-between;">
									<block v-if="item.coupon_type == 1">
										<view class="time">{{ item.use_start_time | dateFormat }}-{{ item.use_end_time | dateFormat }}</view>
									</block>
									<block v-if="item.coupon_type == 0">
										<view>领取后{{ item.coupon_time}}天内可用</view>
									</block>
									<block v-if="item.issue">
										<view class="button gary">已领取</view>
									</block>
									<block v-else>
										<view class="button" @click="receiveCoupon(item)">立即领取</view>
									</block>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</scroll-view>
		<view class="footer">
			<view v-for="(item, index) in tabs" :key="index" :class="{ active: tabActive === index }" class="item" @click="tab(index)">
				<view :class="['iconfont', item.icon]"></view>
				<view>{{ item.name }}</view>
			</view>
		</view>
	</view>
</template>

<script>
	import request from "@/utils/request.js";
	import {
		getStoreDetail,
		getStoreGoods,
		getStoreCategory,
		followStore,
		unfollowStore
	} from '@/api/store.js';
	import {
		getShopCoupons,
		setCouponReceive,
	} from '@/api/api.js';
	import {
		getUserInfo
	} from '@/api/user.js';
	import {
		mapGetters
	} from "vuex";
	import {
		goShopDetail
	} from '@/libs/order.js';
	export default {
		filters: {
			dateFormat: function(value) {
				if (!value) {
					return '';
				}
				return value.split(' ')[0].replace('-', '.');
			}
		},
		data() {
			return {
				systemInfo: uni.getSystemInfoSync(),
				// #ifdef MP
				menuButtonInfo: uni.getMenuButtonBoundingClientRect(),
				// #endif
				id: 0, // 商铺id
				store: {}, // 商铺详情
				goods: [], // 商铺商品
				category: [], // 商铺分类
				coupon: [], // 优惠券
				isColumn: true, // 商品列表排列方式
				navShow: false,
				navActive: 0,
				tabActive: 0, // 底部切换
				keyword: '',
				order: '',
				sortPrice: true, // 价格排序
				goodsLoading: false,
				categoryLoading: false,
				loadTitle: '加载更多',
				where: {
					order: '',
					keyword: '',
					page: 1,
					limit: 100
				},
				// 下拉菜单
				select: {
					show: false,
					selected: 0,
					options: [{
							id: 0,
							name: '默认'
						},
						{
							id: 1,
							name: '评分'
						}
					]
				},
				// 底部菜单
				tabs: [{
						icon: 'icon-yizhan_o',
						name: '首页'
					},
					{
						icon: 'icon-yingyongAPP_o',
						name: '分类'
					},
					{
						icon: 'icon-huobiliu_o',
						name: '领券'
					},
					{
						icon: 'icon-kefu_o',
						name: '联系客服'
					}
				],
				storeScroll: true,
				storeTop: 0,
				navHeight: 0
			}
		},
		computed: {
			score: function() {
				let store = this.store,
					score = {
						star: 0,
						number: 0
					};
				if ('postage_score' in store) {
					score.number = (parseFloat(store.postage_score) + parseFloat(store.product_score) + parseFloat(store.service_score)) /
						3;
					score.star = score.number / 5 * 100;
				}
				return score;
			},
			...mapGetters(['isLogin', 'uid']),
		},
		watch: {
			tabActive: function(value, old) {
				switch (value) {
					case 1:
						this.getCategory();
						break;
					case 2:

						this.getCoupon();
						break;
					case 3:
						this.tabActive = old
						uni.navigateTo({
							url: `/pages/chat/customer_list/chat?mer_id=${this.store.mer_id}&uid=${this.uid}`
						})
						break;
				}
			},
			order: function() {
				this.getGoods();
			}
		},
		onLoad: function(options) {
			this.id = options.id;

		},
		onShow() {
			this.getStore();
			this.getGoods();
		},
		mounted: function() {
			const query = uni.createSelectorQuery().in(this);
			query.select('#store').boundingClientRect(data => {
				this.storeHeight = data.height;
				this.storeTop = data.top;
			}).exec();
		},
		methods: {
			// 领取优惠券
			receiveCoupon(item) {
				setCouponReceive(item.coupon_id).then(res => {
					item.issue = 1
					uni.showToast({
						title: res.message,
						icon: 'none'
					})
				}).catch(err=>{
					uni.showToast({
						title: err,
						icon: 'none'
					})
				})
			},
			// 获取商品详情
			getStore: function() {
				getStoreDetail(this.id).then(res => {
					this.store = res.data;
					// #ifdef H5
					this.ShareInfo();
					// #endif
				}).catch(err => {
					this.loading = false;
					uni.showToast({
						title: err,
						icon: 'none'
					})
					setTimeout(function(){
						uni.navigateBack()
					},1000);
				})
			},
			// 获取商铺商品
			getGoods: function() {
				let that = this;
				if (that.loadend) return;
				if (that.loading) return;
				that.goodsLoading = true;
				that.loadTitle = '';
				getStoreGoods(this.id, this.where).then(res => {
					that.goodsLoading = false;
					let list = res.data.list;
					let goodsList = that.$util.SplitArray(list, that.goods);
					let loadend = list.length < that.where.limit;
					that.loadend = loadend;
					that.loading = false;
					that.loadTitle = loadend ? '已全部加载' : '加载更多';
					that.$set(that, 'goods', goodsList);
					that.$set(that.where, 'page', that.where.page + 1);
				}).catch(err => {
					that.loading = false;
					that.goodsLoading = false;
					uni.showToast({
						title: err,
						icon: 'none'
					})
					setTimeout(function(){
						uni.navigateBack()
					},1000);
				});
			},
			// 获取商铺分类
			getCategory: function() {
				if (this.category.length) {
					return;
				}
				this.categoryLoading = true;
				getStoreCategory(this.id).then(res => {
					this.categoryLoading = false;
					this.category = res.data;
				});
			},
			// 获取商铺优惠券
			getCoupon: function() {
				if (this.coupon.length) {
					return;
				}
				getShopCoupons(this.id).then(res => {
					this.coupon = res.data;
				});
			},
			// 关注商铺
			follow: function() {
				followStore(this.id).then(res => {
					if (res.status === 200) {
						this.store.care = true;
					}
					this.$util.Tips({
						title: res.message
					});
				});
			},
			// 取消关注
			unfollow: function() {
				unfollowStore(this.id).then(res => {
					if (res.status === 200) {
						this.store.care = false;
					}
					this.$util.Tips({
						title: res.message
					});
				});
			},
			// 设置是否关注
			followToggle: function() {
				this.store.care ? this.unfollow() : this.follow();
			},
			// 选择条件展示商品
			set_where: function(param) {
				this.select.show = false;
				this.loading = false;
				this.loadend = false;
				this.where.page = 1;
				this.goods = [];
				switch (param) {					
					case 0:
						this.select.selected = 0;
						this.where.order = '';
						this.getGoods();
						break;
					case 1:
						this.select.selected = 1;
						this.where.order = 'rate';
						this.getGoods();
						break;
					case 2:
						this.navActive = 1;
						this.where.order = 'sales';
						this.getGoods();
						break;
					case 3:
						this.navActive = 2;
						this.sortPrice = !this.sortPrice;
						this.where.order = this.sortPrice ? 'price_asc' : 'price_desc';
						this.getGoods();
						break;
					case 4:
						this.navActive = 3;
						this.where.order = 'is_new';
						this.getGoods();
						break;
				}
			},
			// 去分类商品页
			goCategoryGoods: function(id) {
				uni.navigateTo({
					url: `/pages/store/list/index?id=${id}&mer_id=${this.id}`
				})
			},
			// 去商品详情页
			goGoodsDetail(id) {
				uni.navigateTo({
					url: `/pages/goods_details/index?id=${id}`
				})
			},
			// 商铺首页滚动 navbar 吸顶
			scrollHome: function(e) {
				this.navShow = e.detail.scrollTop >= this.storeHeight-50;
			},
			goback: function() {
				uni.navigateBack();
			},
			// 首页
			goHome() {
				uni.switchTab({
					url: '/pages/index/index'
				});
			},
			// 商铺底部切换
			tab: function(param) {
				this.tabActive = param;
			},
			//#ifdef H5
			ShareInfo() {
				let data = this.store;
				let href = location.href;
				if (this.$wechat.isWeixin()) {
					getUserInfo().then(res => {
						href =
							href.indexOf("?") === -1 ?
							href + "?spread=" + res.data.uid :
							href + "&spread=" + res.data.uid;

						let configAppMessage = {
							desc: data.mer_info,
							title: data.mer_name,
							link: href,
							imgUrl: data.mer_avatar
						};

						this.$wechat.wechatEvevt([
							"updateAppMessageShareData",
							"updateTimelineShareData",
							"onMenuShareAppMessage",
							"onMenuShareTimeline"
						], configAppMessage).then(res => {
							console.log(res, '=============================>>WXAPI');
						}).catch(err => {
							console.log(err);
						})
					});
				}
			},
			//#endif
		}
	}
</script>

<style lang="scss">
	/deep/ .care{
		background-image: none!important;
		border: 1px solid #fff;
		background-color: transparent;
	}
	.store-home {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		flex-direction: column;
		padding-bottom: 100rpx;
		background: left top/750rpx 360rpx no-repeat fixed;
		overflow: hidden;
	}

	.font-bg-red {
		background: #E93424;
		color: #fff;
		font-size: 20rpx;
		width: 58rpx;
		text-align: center;
		line-height: 34rpx;
		border-radius: 5rpx;
		margin-right: 8rpx;
	}

	.header {
		position: relative;
		z-index: 6;
		display: flex;
		align-items: center;
		padding-right: 34rpx;
		height: 86rpx;
		padding-left: 33rpx;

		.head-menu {
			display: -webkit-box;
			display: -webkit-flex;
			display: flex;
			-webkit-box-align: center;
			-webkit-align-items: center;
			align-items: center;
			height: 27px;
			width: 70px;
			background: rgba(0, 0, 0, 0.25);
			border-radius: 13px;

			.icon-xiangzuo {
				font-size: 32rpx;
				color: #FFFFFF;
			}

			.iconfont {
				-webkit-box-flex: 1;
				-webkit-flex: 1;
				flex: 1;
				text-align: center;
				color: #fff;
				box-sizing: border-box;

				&.icon-xiangzuo {
					border-right: 1px solid #fff;
				}
			}
		}


		.search {
			flex: 1;
			display: flex;
			align-items: center;
			min-width: 0;
			height: 58rpx;
			border-radius: 29rpx;
			margin-left: 32rpx;
			background-color: #FFFFFF;
			font-weight: 500;
			font-size: 26rpx;
			color: #999999;

			.iconfont {
				margin-right: 13rpx;
				margin-left: 30rpx;
				font-size: 24rpx;
			}
		}
	}

	.main {
		flex: 1;
		min-height: 0rpx;
	}

	.store {
		position: relative;
		z-index: 6;
		display: flex;
		align-items: center;
		padding-right: 20rpx;
		padding-left: 20rpx;
		padding-top: 20rpx;
		padding-bottom: 22rpx;
		image {
			width: 74rpx;
			height: 74rpx;
			border-radius: 6rpx;
		}

		.text {
			flex: 1;
			min-width: 0;
			margin-right: 20rpx;
			margin-left: 20rpx;

			navigator {
				display: inline-flex;
				align-items: center;
				max-width: 100%;

				.name {
					flex: 1;
					min-width: 0;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					font-weight: bold;
					font-size: 30rpx;
					line-height: 1;
					color: #FFFFFF;
				}

				.iconfont {
					margin-left: 10rpx;
					font-size: 17rpx;
					color: #FFFFFF;
				}
			}

			.score {
				display: flex;
				align-items: center;
				margin-top: 17rpx;
				font-weight: 500;
				font-size: 24rpx;
				line-height: 1;
				color: #FFFFFF;

				.star {
					position: relative;
					width: 111rpx;
					height: 19rpx;
					margin-right: 10rpx;
					background: url(../../columnGoods/images/star.png) left top/100% 100% no-repeat;
					overflow: hidden;

					view {
						position: absolute;
						top: 0;
						left: 0;
						width: 100%;
						height: 100%;
						background: url(../../columnGoods/images/star_active.png) left top/111rpx 19rpx no-repeat;
					}
				}
			}
		}

		button {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 113rpx;
			height: 48rpx;
			border-radius: 24rpx;
			background-image: linear-gradient(-90deg, rgba(246, 122, 56, 1) 0%, rgba(241, 27, 9, 1) 100%);
			font-weight: 500;
			font-size: 22rpx;
			color: #FFFFFF;

			.iconfont {
				margin-right: 6rpx;
				font-size: 22rpx;
			}

			&.gary {
				background-color: #999;
			}
		}
	}

	.nav.fixed {
		position: fixed;
		left: 0;
		width: 100%;

		.nav-cont {
			position: absolute;
			width: 100%;
		}
	}

	.nav {
		position: relative;

		.nav-cont {
			display: flex;
			align-items: center;
			height: 84rpx;

			.item {
				flex: 1;
				display: flex;
				justify-content: center;
				align-items: center;
				min-width: 0;

				.cont {
					display: flex;
					justify-content: center;
					align-items: center;
					width: 116rpx;
					height: 44rpx;
					border-radius: 22rpx;
					font-weight: 500;
					font-size: 24rpx;
					color: #FFFFFF;

					.arrow-icon {
						margin-left: 10rpx;
						font-size: 18rpx;
					}

					.layout-icon {
						font-size: 32rpx;
					}

					.icon-pailie {
						font-size: 32rpx;
					}

					image {
						width: 15rpx;
						height: 21rpx;
						margin-left: 7rpx;
					}
				}
			}

			.active {
				.cont {
					background-color: #FFFFFF;
					font-weight: bold;
					color: $theme-color;
				}
			}
		}

		.select {
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 2;
			width: 100%;
			padding-right: 40rpx;
			padding-bottom: 28rpx;
			padding-left: 74rpx;
			border-bottom-right-radius: 24rpx;
			border-bottom-left-radius: 24rpx;
			background-color: #FFFFFF;

			.item {
				margin-top: 28rpx;
				font-size: 24rpx;
				color: #454545;
			}

			.active {
				background: url(../../../static/images/active.png) right center/20rpx no-repeat;
				color: #E93323;
			}
		}
	}

	.goods {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		padding-top: 20rpx;
		padding-right: 20rpx;
		padding-left: 20rpx;
		background-color: #F5F5F5;

		.item {
			width: 345rpx;
			border-radius: 16rpx;
			margin-bottom: 20rpx;
			background-color: #FFFFFF;
			overflow: hidden;

			.image {
				width: 345rpx;
				height: 345rpx;

				image {
					display: block;
					width: 100%;
					height: 100%;
				}
			}

			.text {
				padding: 20rpx 20rpx 25rpx;

				.name {
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					font-weight: 500;
					font-size: 30rpx;
					line-height: 1;
					color: #222222;
				}

				.money-wrap {
					display: flex;
					align-items: center;
					margin-top: 43rpx;

					.money {
						font-weight: bold;
						font-size: 26rpx;
						color: $theme-color;

						text {
							font-size: 34rpx;
							line-height: 1;
						}
					}

					.ticket {
						height: 26rpx;
						padding-right: 9rpx;
						padding-left: 9rpx;
						border: 1rpx solid $theme-color;
						border-radius: 4rpx;
						margin-left: 10rpx;
						font-weight: 500;
						font-size: 20rpx;
						line-height: 24rpx;
						color: $theme-color;
					}
				}

				.score {
					margin-top: 20rpx;
					font-weight: 500;
					font-size: 20rpx;
					line-height: 1;
					color: #737373;
				}
			}

			.foot {
				display: flex;
				justify-content: center;
				align-items: center;
				height: 52rpx;
				background: linear-gradient(to right, #F11B09, #F67A38);
				font-weight: 500;
				font-size: 24rpx;
				color: #FFFFFF;

				.iconfont {
					margin-right: 10rpx;
					font-size: 22rpx;
					line-height: 1;
				}
			}
		}
	}

	.column {
		padding: 0;
		background-color: #FFFFFF;

		.item {
			position: relative;
			display: flex;
			align-items: center;
			width: 100%;
			padding: 30rpx 20rpx;
			border-radius: 0;
			margin-bottom: 0;

			&::before {
				content: " ";
				position: absolute;
				top: 0;
				right: 20rpx;
				left: 250rpx;
				border-top: 1rpx solid #F5F5F5;
			}

			.image {
				width: 200rpx;
				height: 200rpx;
				border-radius: 16rpx;
				overflow: hidden;
			}

			.text {
				position: relative;
				flex: 1;
				min-width: 0;
				padding-top: 0;
				padding-right: 0;
				padding-bottom: 0;

				.name {
					color: #282828;
				}

				.money-wrap {
					display: flex;
					flex-direction: column;
					align-items: flex-start;
					margin-top: 52rpx;

					.ticket {
						height: 28rpx;
						padding-right: 12rpx;
						padding-left: 12rpx;
						border: none;
						border-radius: 0;
						margin-top: 17rpx;
						margin-left: 0;
						background: url(../../../static/images/yh.png) top left/100% 100% no-repeat;
						line-height: 28rpx;
					}
				}
			}

			.foot {
				position: absolute;
				right: 20rpx;
				bottom: 30rpx;
				height: 44rpx;
				padding-right: 17rpx;
				padding-left: 17rpx;
				border-radius: 22rpx;
				font-size: 22rpx;
				color: #F5F5F5;
			}
		}
	}

	.category {
		padding-top: 34rpx;
		padding-right: 20rpx;
		padding-left: 20rpx;

		.section {
			border-radius: 10rpx;
			margin-bottom: 20rpx;
			background-color: #FFFFFF;

			.head {
				position: relative;
				display: flex;
				align-items: center;
				height: 90rpx;
				padding-right: 20rpx;
				padding-left: 36rpx;
				font-weight: bold;
				color: #282828;

				&::before {
					content: " ";
					position: absolute;
					top: 50%;
					left: 20rpx;
					width: 6rpx;
					height: 24rpx;
					background-color: $theme-color;
					transform: translateY(-50%);
				}

				.title {
					flex: 1;
					min-width: 0;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					font-size: 30rpx;
				}

				.iconfont {
					font-size: 22rpx;
					line-height: 1;
				}
			}

			.body {
				display: flex;
				flex-wrap: wrap;
				justify-content: space-between;
				align-items: center;
				padding: 9rpx 36rpx 14rpx;

				.item {
					width: 314rpx;
					height: 84rpx;
					padding-right: 30rpx;
					padding-left: 30rpx;
					border-radius: 10rpx;
					background-color: #F5F5F5;
					margin-bottom: 10rpx;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					font-weight: 500;
					font-size: 26rpx;
					line-height: 84rpx;
					color: #282828;
				}
			}
		}
	}

	.coupon {
		padding: 30rpx;
		margin-top: 34rpx;
		background-color: #F5F5F5;

		.item {
			display: flex;
			margin-bottom: 16rpx;

			.left {
				display: flex;
				flex-direction: column;
				justify-content: center;
				align-items: center;
				width: 240rpx;
				background: url(../static/images/coupon1.png) left top/100% 100% no-repeat;
				font-weight: 500;
				font-size: 24rpx;
				line-height: 1;
				color: #FFFFFF;

				&.gary {
					background-image: url('data:image/png;base64,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');
				}

				.money {
					margin-bottom: 25rpx;
					font-weight: 800;
					font-size: 36rpx;

					text {
						margin-left: 10rpx;
						font-size: 60rpx;
					}
				}
			}

			.right {
				flex: 1;
				min-width: 0;
				padding-right: 18rpx;
				padding-left: 27rpx;
				background-color: #FFFFFF;

				.name {
					padding-top: 32rpx;
					padding-bottom: 32rpx;
					border-bottom: 1rpx solid #F0F0F0;
					font-weight: 500;
					font-size: 30;
					line-height: 1;
					color: #282828;

					text {
						display: inline-block;
						width: 84rpx;
						height: 28rpx;
						border: 1rpx solid $theme-color;
						border-radius: 14rpx;
						margin-right: 15rpx;
						background-color: #FFF4F3;
						font-weight: 500;
						font-size: 20rpx;
						line-height: 26rpx;
						text-align: center;
						color: $theme-color;

						&.gary {
							border-color: #BBB;
							color: #bbb;
							background-color: #F5F5F5;
						}
					}
				}

				.time-wrap {
					display: flex;
					align-items: center;
					padding-top: 16rpx;
					padding-bottom: 16rpx;
					font-weight: 500;
					font-size: 20rpx;
					color: #999999;

					.time {
						flex: 1;
						min-width: 0;
					}

					.button {
						width: 136rpx;
						height: 44rpx;
						border-radius: 22rpx;
						background-color: $theme-color;
						font-weight: 500;
						font-size: 22rpx;
						line-height: 44rpx;
						text-align: center;
						color: #FFFFFF;

						&.gary {
							background-color: #999;
						}
					}
				}
			}
		}

		.disabled {
			.left {
				background-image: url(../static/images/coupon2.png);
			}

			.right {
				.name {
					text {
						border-color: #C1C1C1;
						color: #C1C1C1;
					}
				}

				.time-wrap {
					.button {
						background-color: #CCCCCC;
						color: #FFFFFF;
					}
				}
			}
		}
	}

	.footer {
		position: fixed;
		bottom: 0;
		left: 0;
		z-index: 5;
		display: flex;
		width: 100%;
		height: 100rpx;
		background-color: #FFFFFF;
		opacity: 0.96;

		.item {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			font-weight: 500;
			font-size: 20rpx;
			color: #282828;

			.iconfont {
				font-size: 43rpx;
			}
		}

		.active {
			color: $theme-color;
		}
	}
</style>
