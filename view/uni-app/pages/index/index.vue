<template>
	<view class="page-index" :class="{'bgf':navIndex >0}">
		<!-- #ifdef H5 -->
		<view class="header">
			<view class="serch-wrapper flex acea-row row-between-wrapper">
				<view class="logo">
					<!-- <image v-if="!logoUrl" class="logo" src="/static/images/crmeb.png"></image> -->
					<image :src="logoUrl" mode=""></image>
				</view>
				<navigator url="/pages/columnGoods/goods_search/index" class="input" hover-class="none"><text class="iconfont icon-xiazai5"></text>
					搜索商品</navigator>
				<navigator class="btn" url="/pages/chat/customer_list/index?type=0" hover-class="none">
					<view class="iconfont icon-xiaoxi"></view>
					<text class="iconnum bg-color-red" v-if="userInfo.total_unread">{{userInfo.total_unread}}</text>
				</navigator>
			</view>
			<tabNav class="tabNav" :class="{'fixed':isFixed}" :tabTitle="navTop" @changeTab='changeTab'></tabNav>
			<!-- <tabNav class="tabNav" :class="{'fixed':isFixed}" :tabTitle="navTop" @changeTab='changeTab' @emChildTab='emChildTab'
			 @childTab='childTab'></tabNav> -->
		</view>
		<!-- #endif -->
		<!-- #ifdef MP -->
		<view class="mp-header">
			<view class="sys-head" view :style="{ height: statusBarHeight }"></view>
			<view class="serch-box" view style="height: 43px;">
				<view class="serch-wrapper flex">
					<view class="logo">
						<!-- <image class="logo" src="/static/images/crmeb.png"></image> -->
						<image :src="logoUrl" mode=""></image>
					</view>
					<navigator url="/pages/columnGoods/goods_search/index" class="input" hover-class="none"><text class="iconfont icon-xiazai5"></text>
						搜索商品</navigator>
					<!-- <navigator class="btn" url="/pages/customer_list/index?type=0" hover-class="none">
						<view class="iconfont icon-xiaoxi"></view>
						<text class="iconnum bg-color-red" v-if="userInfo.total_unread">{{userInfo.total_unread}}</text>
					</navigator> -->
				</view>
			</view>
			<tabNav class="tabNav" :tabTitle="navTop" @changeTab='changeTab'></tabNav>
		</view>
		<!-- #endif -->
		<!-- 首页展示 -->
		<view class="page_content" v-if="navIndex == 0">
			<!-- #ifdef MP -->
			<!-- <view class="mp-bg"></view> -->
			<!-- #endif -->
			<!-- banner -->
			<view class="swiper" v-if="imgUrls.length">
				<swiper indicator-dots="true" :autoplay="true" :circular="circular" :interval="interval" :duration="duration"
				 indicator-color="#E4E4E4" indicator-active-color="#E93323" previous-margin="40rpx" next-margin="40rpx" :current="swiperCur"
				 @change="swiperChange">
					<block v-for="(item,index) in imgUrls" :key="index">
						<swiper-item :class="{active:index == swiperCur}">
							<navigator :url='item.url' class='slide-navigator acea-row row-between-wrapper' hover-class='none'>
								<image :src="item.pic" class="slide-image"></image>
							</navigator>
						</swiper-item>
					</block>
				</swiper>
			</view>
			<!-- menu -->
			<view class='nav acea-row' v-if="menus.length">
				<block v-for="(item,index) in menus" :key="index">
					<view class="item" @click="goMenuDetail(item)">
						<view class='pictrue'>
							<image :src='item.pic'></image>
						</view>
						<view class="menu-txt area-row">{{item.name}}</view>
					</view>


				</block>
			</view>
			<navigator class="ad" :url="ad.home_ad_url" hover-class="none">
				<image mode="" :src="ad.home_ad_pic"></image>
			</navigator>
			<!--秒杀-->
			<view class="main" v-if="spikeList.length > 0">
				<view class="seckill-count">
					<view class="spike-bd">
						<view class="title line1">限时秒杀</view>
						<view class="spike-distance">
							<text class="text bg-red">距结束</text>
							<countDown :is-day="false" :tip-text="' '" :day-text="' '" :hour-text="':'" :minute-text="':'" :second-text="' '"
							 :datatime="datatime"></countDown>
						</view>
						<navigator url="/pages/activity/goods_seckill/index" class="more-btn" hover-class="none">更多<text class="iconfont icon-jiantou"
							 hover-class='none'></text></navigator>
					</view>
					<view class="spike-wrapper">
						<scroll-view scroll-x="true" style="white-space: nowrap; display: flex" show-scrollbar="false">
							<navigator class="spike-item" v-for="(item,index) in spikeList" :key="index" :url="'/pages/activity/goods_seckill_details/index?id='+item.product_id+'&time='+item.stop+''"
							 hover-class='none'>
								<view class="img-box">
									<image :src="item.image" mode=""></image>
								</view>
								<view class="info">
									<view class="name line1">{{item.store_name}}</view>
									<view class="stock-box">
										<view class="grabbed" :style="'width:'+item.sales+';'"></view>
										<text class="stock-sales">{{item.sales}}</text>
									</view>
									<view class="price-box">
										<text class="price"><text>￥</text>{{item.price}}</text>
										<text class="old-price"><text>￥</text>{{item.ot_price}}</text>
									</view>
								</view>
							</navigator>
						</scroll-view>
					</view>
				</view>

			</view>
			<!-- #ifdef MP -->


			<!--直播-->

			<view class="main" v-if="liveList.length > 0">
				<view class="live-count">
					<view>
						<!-- 直播 -->
						<block>
							<view class="spike-bd">
								<view class="title line1">直播专场</view>
								<navigator url="/pages/activity/liveBroadcast/index" class="more-btn" hover-class="none">更多<text class="iconfont icon-jiantou"
									 hover-class='none'></text></navigator>
							</view>
							<view class="live-wrapper mores">
								<scroll-view scroll-x="true" style="white-space: nowrap; display: flex">
									<view class="item" v-for="(item, index) in liveList" :key="index">
										<navigator hover-class="none" :url="item.link">
											<view class="live-top" :style="'background:' + (item.live_status == 101 ? playBg : (item.live_status != 101 && item.live_status != 102) ? endBg : notBg) + ';'"
											 :class="item.live_status == 102 ? 'playRadius' : 'notPlayRadius'">
												<block v-if="item.live_status == 101">
													<image src="/static/images/live-01.png" mode=""></image>
													<text>直播中</text>
												</block>
												<block v-if="item.live_status == 103 && item.replay_status === 1">
													<image src="/static/images/live-02.png" mode=""></image>
													<text>回放</text>
												</block>
												<block v-if="(item.live_status != 101 && item.live_status != 102 && item.live_status != 103) ||  (item.live_status == 103 && item.replay_status == 0)">
													<image src="/static/images/live-02.png" mode=""></image>
													<text>已结束</text>
												</block>
												<block v-if="item.live_status == 102">
													<image src="/static/images/live-03.png" mode=""></image>
													<text>预告</text>
												</block>
											</view>
											<view v-if="item.live_status == 101 || item.live_status == 102" class="broadcast-time">{{ item.show_time }}</view>
											<image :src="item.share_img"></image>
											<!-- <view class="live-title">{{ item.live_status }}</view> -->
										</navigator>
									</view>
								</scroll-view>
							</view>
						</block>
					</view>
				</view>
			</view>
			<!-- #endif -->
			<view class="main">
				<!-- 热点菜单 -->
				<view class="hot-img" style="margin-top:20rpx">
					<navigator :url="item.url" class="item" v-for="(item,index) in hot" :key="index" hover-class="none">
						<view class="title area-row">{{item.title}}</view>
						<view class="msg area-row" :style="'color:'+item.color+';'">{{item.s_title}}</view>
						<view class="img">
							<image :src="item.pic" mode=""></image>
						</view>
					</navigator>
				</view>
				<!-- 品牌好店 -->
				<view class="explosion" v-if="brandList.length && hide_mer_status !=1">
					<view class="common-hd">
						<view class="title">品牌好店</view>
					</view>
					<view class="mer-box">
						<view class="mer-item" v-for="(item,index) in brandList" :key='index'>
							<view class="mer-hd" @click="goStore(item.mer_id)">
								<image :src="item.mer_banner"></image>
								<view class="mer-name">
									<image :src="item.mer_avatar"></image>
									<view class="txt line1">{{item.mer_name}}</view>
									<text v-if="item.is_trader" class="font-bg-red ml8">自营</text>
								</view>
							</view>
							<view class="pro-box">
								<navigator :url="`/pages/goods_details/index?id=${itemn.product_id}`" hover-class="none" class="pro-item" v-for="(itemn,indexn) in item.recommend"
								 :key='indexn' v-if="item.recommend.length<=3">
									<image :src="itemn.image" mode=""></image>
									<view class="price">
										<text>￥</text>{{itemn.price}}
									</view>
								</navigator>
							</view>
						</view>
					</view>
				</view>
				<!-- 首页推荐 -->
				<view class="index-product-wrapper">
					<!-- 首发新品 -->
					<recommend :hostProduct="hostProduct" :indexP='true'></recommend>
					<view class='loadingicon acea-row row-center-wrapper' v-if='hostProduct.length > 0 '>
						<text class='loading iconfont icon-jiazai' :hidden='hotLoading==false'></text>{{hotTitle}}
					</view>
				</view>
			</view>
		</view>
		<!-- 分类页 -->

		<view class="productList" v-if="navIndex>0" :style="'margin-top:'+prodeuctTop+'px;'">
			<view class="sort acea-row">
				<navigator hover-class='none' :url="'/pages/columnGoods/goods_list/index?id='+item.store_category_id+'&title='+item.cate_name"
				 class="item" v-for="(item,index) in sortList" :key="index">
					<view class="pictrue">
						<image :src="item.pic"></image>
					</view>
					<view class="text">{{item.cate_name}}</view>
				</navigator>
				<view class="item" @click="bindMore()" v-if="sortList.length">
					<view class="pictrues acea-row row-center-wrapper">
						<text class="iconfont icon-gengduo1"></text>
					</view>
					<view class="text" style="margin-top: 22rpx;">更多</view>
				</view>
			</view>
			<block v-if="sortProduct.length>0">
				<view class='list acea-row row-between-wrapper'>
					<navigator :url="`/pages/goods_details/index?id=${item.product_id}`" class='item' hover-class='none' v-for="(item,index) in sortProduct"
					 :key="index">
						<view class='pictrue'>
							<image :src='item.image'></image>
						</view>
						<view class='text'>							
							<view class='name line1'><text v-if="item.merchant.is_trader" class="font-bg-red">自营</text>{{item.store_name}}</view>
							
							<view class="acea-row row-middle">
								<view class='money font-color-red'>￥<text class='num'>{{item.price}}</text></view>
								<text class="coupon font-color-red" v-if="item.issetCoupon">领券</text>
							</view>
						</view>
					</navigator>
					<view class='loadingicon acea-row row-center-wrapper' v-if='sortProduct.length > 0 || sortProductLoading'>
						<text class='loading iconfont icon-jiazai' :hidden='loading==false'></text>{{loadTitle}}
					</view>
				</view>
			</block>
			<block v-if="sortProduct.length == 0">
				<view class="noCommodity">
					<view class='pictrue'>
						<image src='/static/images/noShopper.png'></image>
					</view>
					<recommend :hostProduct="hostProduct"></recommend>
				</view>

			</block>
		</view>
		<!-- #ifdef MP -->
		<authorize :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse" :isGoIndex="false"></authorize>
		<!-- #endif -->
	</view>
</template>

<script>
	var statusBarHeight = uni.getSystemInfoSync().statusBarHeight + 'px';
	let app = getApp();
	import {
		getUserInfo
	} from '@/api/user.js';
	import {
		getIndexData,
		getCoupons
	} from '@/api/api.js';
	// #ifdef MP-WEIXIN
	import {
		getTemlIds,
	} from '@/api/api.js';
	import {
		SUBSCRIBE_MESSAGE,
		TIPS_KEY
	} from '@/config/cache';
	import {
		getLiveList
	} from '@/api/store.js';
	// #endif

	import {
		getShare,
		follow,
		getconfig,
	} from '@/api/public.js';
	import {
		getSeckillIndexTime
	} from '@/api/activity.js';

	import goodList from '@/components/goodList';
	import promotionGood from '@/components/promotionGood';
	import couponWindow from '@/components/couponWindow';
	import {
		goShopDetail
	} from '@/libs/order.js'
	import {
		mapGetters
	} from "vuex";
	import tabNav from '@/components/tabNav.vue'
	import countDown from '@/components/countDown'
	import {
		getCategoryList,
		getProductslist,
		getProductHot,
		storeCategory,
		storeMerchantList,
		spikeListApi,
	} from '@/api/store.js';
	import {
		setVisit
	} from '@/api/user.js'
	import recommend from '@/components/recommend';
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	import {
		silenceBindingSpread
	} from '@/utils';

	export default {
		computed: mapGetters(['isLogin', 'uid']),
		components: {
			tabNav,
			goodList,
			promotionGood,
			couponWindow,
			countDown,
			recommend,
			// #ifdef MP
			authorize
			// #endif
		},
		data() {
			return {
				countDownHour: "00",
				countDownMinute: "00",
				countDownSecond: "00",
				datatime: '',
				ad: '',
				userInfo: '',
				loading: false,
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				statusBarHeight: statusBarHeight,
				navIndex: 0,
				navTop: [],
				subscribe: false,
				followUrl: "",
				followHid: true,
				followCode: false,
				logoUrl: app.globalData.site_logo,
				imgUrls: [],
				hot: [],
				sortList: [],
				itemNew: [],
				menus: [],
				bastInfo: '',
				fastInfo: '',
				firstInfo: '',
				firstList: [],
				salesInfo: '',
				likeInfo: [],
				benefit: [],
				indicatorDots: false,
				circular: true,
				autoplay: true,
				interval: 3000,
				duration: 500,
				window: false,
				iShidden: false,
				navH: "",
				newGoodsBananr: '',
				couponList: [],
				lovely: [],
				spikeList: [],
				liveList: [],
				combinationList: [],
				hotList: [{
					pic: '/static/images/hot_001.png'
				}, {
					pic: '/static/images/hot_002.png'
				}, {
					pic: '/static/images/hot_003.png'
				}],
				spikeList: [],
				bargList: [],
				ProductNavindex: 0,
				marTop: 0,
				datatime: 0,
				childID: 0,
				loadend: false,
				loading: false,
				loadTitle: '加载更多',
				sortProduct: [],
				where: {
					pid: 0,
					page: 1,
					limit: 6,
				},
				is_switch: true,
				hostProduct: [],
				hotPage: 1,
				hotLimit: 8,
				hotScroll: true,
				hotLoading: false,
				hotTitle: '加载更多',
				explosiveMoney: [],
				prodeuctTop: 0,
				pinkInfo: '',
				searchH: 0,
				isFixed: false,
				goodScroll: true, //精品推荐开关
				params: { //精品推荐分页
					page: 1,
					limit: 10,
				},
				tempArr: [], //精品推荐临时数组
				pageInfo: '', // 精品推荐全数据
				site_name: app.globalData.site_name, //首页title
				swiperCur: 0,
				brandList: [],
				d: '',
				h: '',
				m: '',
				s: '',
				sum_h: '',
				endBg: 'linear-gradient(#666666, #999999)',
				notBg: 'rgb(26, 163, 246)',
				playBg: 'linear-gradient(#FF0000, #FF5400)',
				hide_mer_status: ''
			}
		},
		onLoad() {

			uni.getLocation({
				type: 'wgs84',
				success: function(res) {
					try {
						uni.setStorageSync('user_latitude', res.latitude);
						uni.setStorageSync('user_longitude', res.longitude);
					} catch {}
				}
			});
			let self = this
			// #ifdef MP
			// 获取小程序头部高度
			this.navH = app.globalData.navHeight;
			let info = uni.createSelectorQuery().select(".mp-header");
			info.boundingClientRect(function(data) {
				self.marTop = data.height
			}).exec()
			// #endif
			// #ifndef MP
			this.navH = 0;
			// #endif
			this.isLogin && silenceBindingSpread();
			//   
			// this.setVisit()

			Promise.all([this.getIndexConfig(), this.storeMerchant(), this.get_host_product(), this.getSpeckillCutTime(), this.getSpikeProduct()]);

			// #ifdef MP
			this.getLiveList()
			// #endif
			if (this.isLogin) {
				this.getUserInfo();
			};
			this.setViewport(`width=device-width, initial-scale=1.0`);
		},
		onShow() {
			this.getConfig()
		},

		methods: {
			  setViewport(content) {
			      const meta = document.querySelector('meta[name=viewport]');
			      if (!meta) return;
			      meta.setAttribute('content', content);
			    },
			
			// 菜单详情
			goMenuDetail(item) {
				let url = this.$util.stringIntercept(item.url, 0, "\?")
				if (url == '/pages/goods_cate/goods_cate') {
					let data = this.$util.stringIntercept(item.url, 1, "\?")
					data = this.$util.stringIntercept(data, 1, "\=")
					try {
						uni.setStorageSync('storeIndex', data);
						uni.switchTab({
							url: '/pages/goods_cate/goods_cate'
						})
					} catch (e) {}
				} else {
					uni.navigateTo({
						url: item.url
					})
				}
			},
			getConfig() {
				// 获取配置
				getconfig().then(res => {
					let self = this
					this.logoUrl = res.data.site_logo
					this.site_name = res.data.site_name
					this.hide_mer_status = res.data.hide_mer_status
					uni.setNavigationBarTitle({
						title: self.site_name
					})
				}).catch(err => {})
			},



			// 分类页更多
			bindMore() {
				console.log(this.navTop[this.navIndex])
				try {
					uni.setStorageSync('storeIndex', this.navTop[this.navIndex].pid);
					uni.switchTab({
						url: '/pages/goods_cate/goods_cate'
					})
				} catch (e) {}
			},
			// 进店
			goStore: function(id) {
				uni.navigateTo({
					url: `/pages/store/home/index?id=${id}`
				})
			},
			/**
			 * 获取个人用户信息
			 */
			getUserInfo: function() {
				let that = this;
				getUserInfo().then(res => {
					that.userInfo = res.data
				});
			},
			// 品牌好店
			storeMerchant() {
				storeMerchantList({
					page: 1,
					limit: 3
				}).then(res => {
					this.brandList = res.data.list;
				})
			},
			// 获取秒杀截止时间
			getSpeckillCutTime() {
				getSeckillIndexTime().then(res => {

					this.datatime = res.data.seckillEndTime;

				});
			},
			// 获取秒杀商品
			getSpikeProduct() {
				let that = this;
				spikeListApi().then(res => {
					that.spikeList = res.data.list;
					that.spikeList.map((item) => {
						item.sales = item.stock === 0 ? '0%' : (item.sales * 100 / item.stock).toFixed(2) + '%'
					})
				}).catch((e) => {});
			},
			// #ifdef MP
			// 直播
			getLiveList() {
				let that = this;
				getLiveList().then(res => {
					that.liveList = res.data.list;
					that.liveList.forEach((val) => {
						val.link = ((val.live_status == 103 && val.replay_status) || val.live_status === 101 || val.live_status ===
							102) ? 'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin?room_id=' + val.room_id : ''
					});

				}).catch((e) => {});;
			},
			// #endif
			// swiper
			swiperChange(e) {
				// this.swiperCur = e.detail.current
				 let {current, source} = e.detail
				    if(source === 'autoplay' || source === 'touch') {
				    //根据官方 source 来进行判断swiper的change事件是通过什么来触发的，autoplay是自动轮播。touch是用户手动滑动。其他的就是未知问题。抖动问题主要由于未知问题引起的，所以做了限制，只有在自动轮播和用户主动触发才去改变current值，达到规避了抖动bug
				       this.swiperCur = e.detail.current				      
				    }
			},
			// 记录会员访问
			setVisit() {
				setVisit({
					url: '/pages/index/index'
				}).then(res => {
					console.log(res)
				})
			},
			// 导航分类切换
			changeTab(e) {
				this.navIndex = e.index;
				if (e.index > 0) {
					storeCategory({
						pid: e.pid
					}).then(res => {
						this.sortList = res.data.length > 9 ? res.data.splice(0, 9) : res.data;
					});
					this.where.pid = e.pid;
					this.where.page = 1;
					this.loadend = false;
					this.loading = false;
					this.sortProduct = [];
					this.get_product_list();
				}
			},
			//分类产品
			get_product_list: function() {
				console.log(123);
				let that = this;
				// if (!that.loadend) return;
				if (that.loading) return;
				that.loading = true;
				that.loadTitle = '';
				getProductslist(that.where).then(res => {
					let list = res.data.list;
					let productList = that.$util.SplitArray(list, that.sortProduct);
					let loadend = list.length < that.where.limit;
					that.loadend = loadend;
					that.loading = false;
					that.loadTitle = loadend ? '已全部加载' : '加载更多';
					that.$set(that, 'sortProduct', productList);
					that.$set(that.where, 'page', that.where.page + 1);
				}).catch(err => {
					that.loading = false;
					that.loadTitle = '加载更多';
				});
			},
			/**
			 * 获取我的推荐
			 */
			get_host_product: function() {
				let that = this;
				let num = that.hotLimit
				if (!that.hotScroll) return;
				if (that.hotLoading) return;
				that.hotLoading = true;
				that.hotTitle = '';
				getProductHot(
					that.hotPage,
					that.hotLimit,
				).then(res => {
					let list = res.data.list;
					let productList = that.$util.SplitArray(list, that.hostProduct);
					let hotScroll = list.length <= num && list.length != 0;
					that.hotScroll = hotScroll;
					that.hotLoading = false;
					that.hotTitle = !hotScroll ? '已全部加载' : '加载更多';
					that.$set(that, 'hostProduct', productList);
					that.$set(that, 'hotPage', that.hotPage + 1);
				});
			},
			// 首页数据
			getIndexConfig: function() {
				let that = this;
				getIndexData().then(res => {
					that.$set(that, "imgUrls", res.data.banner);
					that.$set(that, "menus", res.data.menu);
					that.$set(that, "hot", res.data.hot);
					that.$set(that, "ad", res.data.ad);
					res.data.category.unshift({
						'cate_name': '首页'
					})
					that.$set(that, "navTop", res.data.category);


					that.lovely = res.data.lovely
					that.$set(that, "pageInfo", res.data)
					that.$set(that, "likeInfo", res.data.likeInfo);
					that.$set(that, "benefit", res.data.benefit);
					that.explosiveMoney = res.data.explosive_money
					// #ifdef H5
					that.subscribe = res.data.subscribe;
					// #endif
					// 小程序判断用户是否授权；
					// #ifdef MP
					uni.getSetting({
						success(res) {
							if (!res.authSetting['scope.userInfo']) {
								that.window = that.couponList.length ? true : false;
							} else {
								that.window = false;
								that.iShidden = true;
							}
						}
					});
					// #endif
					// #ifndef MP
					if (that.isLogin) {
						that.window = false;
					} else {
						that.window = that.couponList.length ? true : false;
					}
					// #endif
				})
			},
			// 砍价详情
			bargDetail(item) {
				if (!this.isLogin) {
					// #ifdef H5
					uni.showModal({
						title: '提示',
						content: '您未登录，请登录！',
						success: function(res) {
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/users/login/index'
								})
							} else if (res.cancel) {}
						}
					})
					// #endif
					// #ifdef MP
					this.$set(this, 'isAuto', true);
					this.$set(this, 'isShowAuth', true);
					// #endif
				} else {
					uni.navigateTo({
						url: `/pages/activity/goods_bargain_details/index?id=${item.id}&bargain=${this.uid}`
					})
				}
			},
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			// 首发新品详情
			goDetail(item) {
				if (item.activity && item.activity.type === "2" && !this.isLogin) {
					// #ifdef H5
					uni.showModal({
						title: '提示',
						content: '您未登录，请登录！',
						success: function(res) {
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/users/login/index'
								})
							} else if (res.cancel) {}
						}
					})
					// #endif
					// #ifdef MP
					this.$set(this, 'isAuto', true);
					this.$set(this, 'isShowAuth', true);
					// #endif
					return
				} else {
					goShopDetail(item, this.uid).then(res => {
						uni.navigateTo({
							url: `/pages/goods_details/index?id=${item.id}`
						})
					})
				}
			},
			// 分类详情
			godDetail(item) {
				goShopDetail(item, this.uid).then(res => {
					uni.navigateTo({
						url: `/pages/goods_details/index?id=${item.id}`
					})
				})
			},
			countTime: function() {
				// 获取当前时间
				var date = new Date();
				var now = date.getTime();
				//设置截止时间
				var endDate = new Date("2020-10-22 23:23:23");
				var end = endDate.getTime();
				//时间差
				var leftTime = end - now;
				//定义变量 d,h,m,s保存倒计时的时间
				if (leftTime >= 0) {
					this.d = Math.floor(leftTime / 1000 / 60 / 60 / 24);
					this.h = Math.floor(leftTime / 1000 / 60 / 60 % 24);
					this.m = Math.floor(leftTime / 1000 / 60 % 60);
					this.s = Math.floor(leftTime / 1000 % 60);
					this.sum_h = this.d * 24 + this.h
				}
				// console.log(this.h)
				// console.log(this.m)
				// console.log(this.s)
				// console.log(this.s);
				//递归每秒调用countTime方法，显示动态时间效果
				setTimeout(this.countTime, 1000);
			}
		},
		mounted() {
			let self = this
			// #ifdef H5
			// 获取H5 搜索框高度
			let appSearchH = uni.createSelectorQuery().select(".serch-wrapper");
			appSearchH.boundingClientRect(function(data) {
				self.searchH = data.height
			}).exec()
			// #endif
		},
		// 滚动到底部
		onReachBottom() {
			console.log('到底了')
			if (this.navIndex == 0) {
				// 首页加载更多
				this.get_host_product();
			} else {
				// 分类栏目加载更多
				if (this.sortProduct.length > 0) {
					this.get_product_list();
				} else {
					this.get_host_product();
				}
			}
		},
		// 滚动监听
		onPageScroll(e) {
			// #ifdef H5
			let self = this
			if (e.scrollTop >= self.searchH) {
				self.isFixed = true
			} else {
				self.isFixed = false
			}
			// #endif
		}
	}
</script>
<style>
	page {
		display: flex;
		flex-direction: column;
		height: 100%;
		/* #ifdef H5 */
		background-color: #fff;
		/* #endif */

	}
</style>
<style lang="scss">
	.font-bg-red{
		display: inline-block;
		background: #E93424;
		color: #fff;
		font-size: 20rpx;
		width: 58rpx;
		text-align: center;
		line-height: 34rpx;
		border-radius: 20rpx;
		margin-right: 8rpx;
		&.ml8{
			margin-left: 8rpx;
			margin-right: 0;
		}
	}
	
	.area-row {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		display: block;
		width: 100%;
		text-align: center;
	}
	.seckill-count {
		background-color: #fff;
		margin-bottom: 20rpx;
		border-radius: 16rpx;
		padding: 20rpx 0 26rpx 20rpx;
		box-shadow: 4rpx 2rpx 12rpx 2rpx rgba(0, 0, 0, 0.03);
	}

	.spike-bd {
		margin-bottom: 20rpx;

		border-radius: 16rpx;
		padding-left: 20rpx;
		display: flex;
		position: relative;


		.title {
			font-weight: bold;
			color: #282828;
			font-size: 32rpx;
		}

		.spike-distance {
			margin-left: 15rpx;
			position: relative;
			top: 1.4rpx;
			display: flex;
			border: 1px solid #E93323;
			border-radius: 4rpx;

			.bg-red {
				font-size: 20rpx;
				color: #fff;
				background-color: #E93323;
				padding: 0 10rpx;
				line-height: 40rpx;
			}

			.time {
				font-size: 22rpx;
				padding: 0 12rpx;
				color: #E93323;
				display: inline;
				line-height: 40rpx;
				/* #ifdef MP */
				line-height: 40rpx;

				/* #endif*/
				/deep/.red {
					margin: 0;
				}
			}

			.red-color {
				color: #E93323;
			}

		}

		.more-btn {
			position: absolute;
			right: 20rpx;
			top: 0;
			color: #999;
			font-size: 28rpx;

			.iconfont {
				font-size: 26rpx;
			}
		}
	}

	.spike-wrapper {
		width: 100%;
		margin-top: 27rpx;

		.spike-item {
			display: inline-block;
			width: 222rpx;
			margin-right: 20rpx;

			.img-box {
				position: relative;
				height: 222rpx;

				image {
					width: 100%;
					height: 222rpx;
					border-radius: 16rpx;
				}

				.msg {
					position: absolute;
					left: 10rpx;
					bottom: 16rpx;
					width: 86rpx;
					height: 30rpx;
					background: rgba(255, 255, 255, 1);
					border: 1px solid rgba(255, 109, 96, 1);
					border-radius: 6rpx;
					font-size: 20rpx;
					color: $theme-color;
				}
			}


			.info {
				margin-top: 10rpx;

				.name {
					font-size: 26rpx;
				}

				.stock-box {
					width: 100%;
					height: 20rpx;
					background-color: #FFDCD9;
					border-radius: 20rpx;
					margin-top: 13rpx;
					position: relative;
					color: #fff;
					font-size: 18rpx;
					line-height: 20rpx;
					text-align: center;
					.grabbed {
						height: 20rpx;
						background: linear-gradient(#FF0000, #FF5400);
						position: absolute;
						top: 0;
						left: 0;
						border-radius: 20rpx;
					}
					.stock-sales{
						position: absolute;
						left: 50%;
						margin-left: -40rpx;
					}
				}

				.price-box {
					display: flex;
					align-items: center;
					justify-content: start;
					margin-top: 4rpx;

					.tips {
						display: flex;
						align-items: center;
						justify-content: center;
						width: 28rpx;
						height: 28rpx;
						background-color: $theme-color;
						color: #fff;
						font-size: 20rpx;
						border-radius: 2px;
					}

					.price {
						display: flex;
						color: $theme-color;
						font-size: 28rpx;
						font-weight: bold;

						text {
							font-size: 18rpx;
						}
					}

					.old-price {
						display: flex;
						margin-left: 10rpx;
						color: #AAAAAA;
						text-decoration: line-through;
						font-size: 20rpx;

						text {
							font-size: 18rpx;
						}
					}
				}
			}
		}
	}

	/deep/.spike-box .styleAll {
		display: inline-block;
		width: 44rpx;
		height: 40rpx;
		line-height: 40rpx;
		padding: 0;
		text-align: center;
		border-radius: 8rpx;
	}

	.page-index {
		display: flex;
		flex-direction: column;
		min-height: 100%;
		background: linear-gradient(180deg, #fff 0%, #f5f5f5 100%);

		.ad {
			width: 710rpx;
			height: 156rpx;
			margin: 10rpx auto 20rpx auto;

			image {
				width: 100%;
				height: 100%;
			}
		}

		// &.bgf{
		// 	background: #fff;
		// }
		.header {
			width: 100%;

			// height: 320rpx;
			.btn {
				position: relative;

				.iconfont {
					font-size: 45rpx;
				}
			}

			.iconnum {
				min-width: 6px;
				color: #fff;
				border-radius: 15rpx;
				position: absolute;
				right: -10rpx;
				top: -10rpx;
				font-size: 10px;
				padding: 0 4px;
			}

			.serch-wrapper {
				align-items: center;
				padding: 20rpx 30rpx 0 30rpx;

				.logo {
					width: 127rpx;
					height: 46rpx;
				}

				image {
					width: 118rpx;
					height: 42rpx;
				}

				.input {
					display: flex;
					align-items: center;
					width: 490rpx;
					height: 64rpx;
					padding: 0 0 0 30rpx;
					background: rgba(237, 237, 237, 1);
					border: 1px solid rgba(241, 241, 241, 1);
					border-radius: 32rpx;
					color: #BBBBBB;
					font-size: 28rpx;

					.iconfont {
						margin-right: 20rpx;
					}
				}
			}

			.tabNav {
				padding-top: 24rpx;
			}
		}

		/*直播*/
		.live-count {
			background-color: #fff;
			margin-bottom: 20rpx;
			border-radius: 16rpx;
			padding: 20rpx 0 26rpx 20rpx;
			box-shadow: 4rpx 2rpx 12rpx 2rpx rgba(0, 0, 0, 0.03);
		}

		.live-wrapper {
			position: relative;
			width: 100%;
			overflow: hidden;
			border-radius: 16rpx;

			image {
				width: 100%;
				height: 400rpx;
			}

			.live-top {
				z-index: 20;
				position: absolute;
				left: 0;
				top: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
				width: 120rpx;
				height: 32rpx;

				&.playRadius {
					border-radius: 0;
				}

				&.notPlayRadius {
					border-radius: 0rpx 0px 18rpx 0px;
				}

				image {
					width: 30rpx;
					height: 30rpx;
					margin-right: 6rpx;
					/* #ifdef H5 */
					display: block;
					/* #endif */
				}
			}

			.broadcast-time {
				z-index: 20;
				position: absolute;
				left: 110rpx;
				top: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
				width: 160rpx;
				height: 36rpx;
				background: rgba(0, 0, 0, .4);
				font-size: 22rpx;
				border-radius: 0 0 18rpx 0;
			}

			.live-title {
				position: absolute;
				left: 0;
				bottom: 6rpx;
				width: 100%;
				height: 70rpx;
				line-height: 70rpx;
				text-align: center;
				font-size: 30rpx;
				color: #fff;
				background: rgba(0, 0, 0, 0.35);
			}

			&.mores {
				width: 100%;

				.item {
					position: relative;
					width: 280rpx;
					height: 224rpx;
					display: inline-block;
					border-radius: 16rpx;
					overflow: hidden;
					margin-right: 20rpx;
					image {
						width: 320rpx;
						height: 180rpx;
						border-radius: 16rpx;
					}

					.live-title {
						height: 40rpx;
						line-height: 40rpx;
						text-align: center;
						font-size: 22rpx;
					}

					.live-top {
						width: 110rpx;
						height: 32rpx;
						font-size: 22rpx;

						image {
							width: 20rpx;
							height: 20rpx;
						}
					}
				}
			}
		}

		/* #ifdef MP */
		.mp-header {
			z-index: 999;
			position: fixed;
			left: 0;
			top: 0;
			width: 100%;
			height: 220rpx;
			/* #ifdef H5 */
			padding-bottom: 20rpx;
			/* #endif */
			// background: linear-gradient(90deg, $bg-star 50%, $bg-end 100%);
			background-color: #fff;

			.serch-wrapper {
				height: 100%;
				align-items: center;
				padding: 0 50rpx 0 53rpx;

				image {
					width: 118rpx;
					height: 42rpx;
					margin-right: 30rpx;
				}

				.input {
					display: flex;
					align-items: center;
					width: 305rpx;
					height: 58rpx;
					padding: 0 0 0 30rpx;
					background: rgba(247, 247, 247, 1);
					border: 1px solid rgba(241, 241, 241, 1);
					border-radius: 29rpx;
					color: #BBBBBB;
					font-size: 28rpx;

					.iconfont {
						margin-right: 20rpx;
					}
				}
			}
		}

		/* #endif */

		.page_content {

			/* #ifdef MP */
			margin-top: 240rpx;

			/* #endif */
			.swiper {
				position: relative;
				width: 100%;
				height: 320rpx;
				margin: 0 auto;
				border-radius: 10rpx;
				overflow-x: hidden;
				/* #ifdef MP */
				z-index: 10;

				/* #endif */
				swiper,
				.swiper-item,
				image {
					width: 100%;
					height: 280rpx;
					border-radius: 10rpx;

				}

				.slide-navigator {}

				image {
					transform: scale(.93);
					transition: all .6s ease;
				}

				swiper-item.active {
					image {
						transform: scale(1);
					}
				}
			}

			.nav {
				padding: 0 0rpx 30rpx;
				flex-wrap: wrap;
				margin-top: 40rpx;

				.item {
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 20%;
					margin-top: 30rpx;

					image {
						width: 82rpx;
						height: 82rpx;
					}
				}
			}

			.live-wrapper {
				position: relative;
				width: 100%;
				overflow: hidden;
				border-radius: 16rpx;

				image {
					width: 100%;
					height: 400rpx;
				}

				.live-top {
					z-index: 20;
					position: absolute;
					left: 0;
					top: 0;
					display: flex;
					align-items: center;
					justify-content: center;
					color: #fff;
					width: 110rpx;
					height: 36rpx;

					&.playRadius {
						border-radius: 0;
					}

					&.notPlayRadius {
						border-radius: 0rpx 0px 18rpx 0px;
					}

					image {
						width: 30rpx;
						height: 30rpx;
						margin-right: 10rpx;
						/* #ifdef H5 */
						display: block;
						/* #endif */
					}
				}

				.live-title {
					position: absolute;
					left: 0;
					bottom: 6rpx;
					width: 100%;
					height: 36rpx;
					line-height: 36rpx;
					text-align: center;
					font-size: 22rpx;
					color: #fff;
					background: rgba(0, 0, 0, .35);
				}

				&.mores {
					width: 100%;

					.item {
						position: relative;
						width: 280rpx;
						display: inline-block;
						border-radius: 16rpx;
						overflow: hidden;
						margin-right: 20rpx;

						image {
							width: 280rpx;
							height: 224rpx;
							border-radius: 16rpx;
						}

						.live-title {
							height: 36rpx;
							line-height: 36rpx;
							text-align: center;
							font-size: 22rpx;
						}

						.live-top {
							width: 110rpx;
							height: 36rpx;
							font-size: 22rpx;

							image {
								width: 20rpx;
								height: 20rpx;
							}
						}
					}
				}
			}

			.hot-img {
				display: flex;
				align-items: center;
				justify-content: space-between;

				.item {
					display: flex;
					flex-direction: column;
					align-items: center;
					width: 170rpx;
					background-color: #FEFEFF;
					padding: 20rpx 0 4rpx;
					border-radius: 8rpx;
					box-shadow: 2px 1px 6px 1px rgba(0, 0, 0, 0.03);

					.title {
						font-weight: bold;
						color: #282828;
					}

					.msg {
						margin-top: 5rpx;
						font-size: 20rpx;
					}

					.img {
						margin-top: 10rpx;
					}

					image {
						width: 130rpx;
						height: 130rpx;
					}

					&:first-child .msg {
						color: #8FBBE8;
					}

					&:nth-child(2) .msg {
						color: #D797B7;
					}

					&:nth-child(3) .msg {
						color: #C49BD1;
					}

					&:nth-child(4) .msg {
						color: #A3BF95;
					}
				}
			}

			.common-hd {
				display: flex;
				align-items: center;
				justify-content: center;
				height: 118rpx;

				.title {
					padding: 0 80rpx;
					font-size: 34rpx;
					color: $theme-color;
					font-weight: bold;
					background-image: url("~@/static/images/index-title.png");
					background-repeat: no-repeat;
					background-size: 100% auto;
					background-position: left center;
				}
			}

			.explosion {
				.mer-box {
					.mer-item {
						margin-bottom: 20rpx;
						background-color: #fff;
						border-radius: 16rpx;

						.mer-hd {
							position: relative;
							width: 100%;
							height: 200rpx;
							border-radius: 16rpx 16rpx 0 0;
							overflow: hidden;
							image {
								width: 100%;
								height: 100%;
							}
							.mer-name {
								position: absolute;
								left: 20rpx;
								top: 20rpx;
								display: flex;
								align-items: center;
								width: 300rpx;
								height: 50rpx;
								padding: 0 10rpx;
								border-radius: 26rpx;
								background: #fff;
								font-weight: bold;
								image {
									width: 38rpx;
									height: 38rpx;
									margin-right: 10rpx;
									border-radius: 50%;
								}

								.txt {
									flex: 1;
								}
							}
						}

						.pro-box {
							display: flex;
							align-items: center;
							padding: 20rpx 20rpx 30rpx;

							.pro-item {
								width: 218rpx;
								margin-right: 14rpx;

								image {
									width: 100%;
									height: 214rpx;
								}

								.price {
									margin-top: 5rpx;
									font-size: 28rpx;
									color: $theme-color;
									font-weight: bold;

									text {
										font-size: 28rpx;
									}
								}

								&:last-child {
									margin-right: 0;
								}
							}
						}
					}
				}
			}

			.spike-box {
				margin-top: 20rpx;
				padding: 23rpx 20rpx;
				border-radius: 24rpx;
				background-color: #fff;
				overflow: hidden;
				box-shadow: 0px 0px 16px 3px rgba(0, 0, 0, 0.04);

				.hd {
					display: flex;
					align-items: center;
					justify-content: space-between;

					.left {
						display: flex;
						align-items: center;
						width: 500rpx;

						.icon {
							width: 38rpx;
							height: 38rpx;
							margin-right: 8rpx;
						}

						.title {
							width: 134rpx;
							height: 33rpx;
						}
					}

					.more {
						font-size: 26rpx;
						color: #999;

						.iconfont {
							margin-left: 6rpx;
							font-size: 25rpx;
						}
					}
				}

				.spike-wrapper {
					width: 100%;
					margin-top: 27rpx;

					.spike-item {
						display: inline-block;
						width: 222rpx;
						margin-right: 20rpx;

						.img-box {
							position: relative;
							height: 222rpx;

							image {
								width: 100%;
								height: 222rpx;
								border-radius: 16rpx;
							}

							.msg {
								position: absolute;
								left: 10rpx;
								bottom: 16rpx;
								width: 86rpx;
								height: 30rpx;
								background: rgba(255, 255, 255, 1);
								border: 1px solid rgba(255, 109, 96, 1);
								border-radius: 6rpx;
								font-size: 20rpx;
								color: $theme-color;
							}
						}


						.info {
							margin-top: 8rpx;
							padding: 0 10rpx;

							.name {
								font-size: 28rpx;
							}

							.price-box {
								display: flex;
								align-items: center;
								justify-content: center;

								.tips {
									display: flex;
									align-items: center;
									justify-content: center;
									width: 28rpx;
									height: 28rpx;
									background-color: $theme-color;
									color: #fff;
									font-size: 20rpx;
									border-radius: 2px;
								}

								.price {
									display: flex;
									margin-left: 10rpx;
									color: $theme-color;
									font-size: 28rpx;
									font-weight: bold;

									text {
										font-size: 18rpx;
									}
								}
							}
						}
					}
				}
			}

			.barg {
				width: 100%;
				height: 478rpx;
				margin-top: 20rpx;
				padding-left: 20rpx;
				background-size: 100% 100%;

				.title {
					display: flex;
					align-items: center;
					justify-content: center;
					padding-top: 42rpx;

					image {
						width: 463rpx;
						height: 39rpx;
					}
				}

				.barg-swiper {
					margin-top: 43rpx;
					padding-right: 20rpx;

					.wrapper {
						display: flex;
					}

					.list-box {
						flex-shrink: 0;
						width: 210rpx;
						background-color: #fff;
						border-radius: 16rpx;
						overflow: hidden;
						padding-bottom: 18rpx;
						margin-right: 20rpx;

						image {
							width: 100%;
							height: 210rpx;
						}

						.info-txt {
							width: 100%;
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: center;
							padding-top: 15rpx;

							.price {
								font-weight: 700;
								color: $theme-color;
							}

							.txt {
								display: flex;
								align-items: center;
								justify-content: center;
								width: 136rpx;
								height: 33rpx;
								margin-top: 10rpx;
								background: linear-gradient(90deg, $bg-star 0%, $bg-end 100%);
								border-radius: 17px;
								font-size: 22rpx;
								color: #fff;

							}
						}
					}

					.more-box {
						flex-shrink: 0;
						display: flex;
						flex-direction: column;
						align-items: center;
						justify-content: center;
						width: 80rpx;
						background-color: #fff;
						border-radius: 16rpx;

						image {
							width: 24rpx;
							height: 24rpx;
							margin-top: 10rpx;
						}

						.txt {
							display: block;
							writing-mode: vertical-lr;
							font-size: 26rpx;
							line-height: 1.2;
						}
					}
				}
			}

			.group-wrapper {
				padding: 26rpx 20rpx;
				margin-top: 20rpx;
				background: #fff;
				border-radius: 24rpx;

				.hd {
					display: flex;
					align-items: center;
					justify-content: space-between;

					.left {
						display: flex;
						align-items: center;

						.icon {
							width: 38rpx;
							height: 38rpx;
							margin-right: 8rpx;
						}

						.title {
							width: 134rpx;
							height: 33rpx;
						}

						.person {
							display: flex;
							align-items: center;
							margin-left: 40rpx;

							.avatar-box {
								display: flex;
								align-items: center;

								image {
									width: 30rpx;
									height: 30rpx;
									border-radius: 50%;
									margin-right: -10rpx;
								}
							}

							.num {
								margin-left: 18rpx;
								font-size: 26rpx;
								color: #999999;
							}
						}
					}

					.more {
						font-size: 26rpx;
						color: #999;

						.iconfont {
							margin-left: 6rpx;
							font-size: 25rpx;
						}
					}
				}

				.group-scroll {
					width: 100%;
					margin-top: 25rpx;

					.group-item {
						display: inline-block;
						width: 222rpx;
						margin-right: 20rpx;
						box-shadow: 0px 2px 6px 2px rgba(0, 0, 0, 0.03);
						border-radius: 16rpx;

						image {
							width: 100%;
							height: 222rpx;
							border-radius: 16rpx 16rpx 0 0;
						}

						.info {
							padding: 8rpx 13rpx;

							.name {
								font-size: 24rpx;
							}

							.price-box {
								display: flex;
								align-items: center;
								margin-top: 10rpx;

								.tips {
									display: flex;
									align-items: center;
									justify-content: center;
									width: 76rpx;
									height: 30rpx;
									margin-right: 6rpx;
									background: linear-gradient(90deg, rgba(255, 0, 0, .1) 0%, rgba(255, 84, 0, .1) 100%);
									border-radius: 2px;
									font-size: 20rpx;
									color: $theme-color;
								}

								.price {
									font-size: 26rpx;
									color: $theme-color;
									font-weight: 700;

									text {
										font-size: 20rpx;
									}
								}
							}

						}

						.bom-btn {
							display: flex;
							align-items: center;
							justify-content: center;
							width: 100%;
							height: 48rpx;
							background: linear-gradient(90deg, $bg-star 0%, $bg-end 100%);
							border-radius: 0px 0px 16rpx 16rpx;
							color: #fff;

						}
					}
				}
			}

			.boutique {
				margin-top: 20rpx;

				swiper,
				swiper-item,
				.slide-image {
					width: 100%;
					height: 240rpx;
					border-radius: 12rpx;
				}
			}

			.index-product-wrapper {
				.nav-bd {
					display: flex;
					align-items: center;

					.item {
						display: flex;
						flex-direction: column;
						align-items: center;
						justify-content: center;
						width: 25%;

						.txt {
							font-size: 32rpx;
							color: #282828;
						}

						.label {
							display: flex;
							align-items: center;
							justify-content: center;
							width: 124rpx;
							height: 32rpx;
							margin-top: 5rpx;
							font-size: 24rpx;
							color: #999;
						}

						&.active {
							color: $theme-color;

							.label {
								background: linear-gradient(90deg, $bg-star 0%, $bg-end 100%);
								border-radius: 16rpx;
								color: #fff;
							}
						}
					}
				}

				.list-box {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;

					.item {
						width: 345rpx;
						margin-bottom: 20rpx;
						background-color: #fff;
						border-radius: 10px;
						overflow: hidden;

						image {
							width: 100%;
							height: 345rpx;
						}

						.text-info {
							padding: 10rpx 20rpx 15rpx;

							.title {
								color: #222222;
							}

							.old-price {
								margin-top: 8rpx;
								font-size: 26rpx;
								color: #AAAAAA;
								text-decoration: line-through;

								text {
									margin-right: 2px;
									font-size: 20rpx;
								}
							}

							.price {
								display: flex;
								align-items: flex-end;
								color: $theme-color;
								font-size: 34rpx;
								font-weight: 800;

								text {
									padding-bottom: 4rpx;
									font-size: 24rpx;
									font-weight: normal;
								}

								.txt {
									display: flex;
									align-items: center;
									justify-content: center;
									width: 28rpx;
									height: 28rpx;
									margin-left: 15rpx;
									margin-bottom: 10rpx;
									border: 1px solid $theme-color;
									border-radius: 4rpx;
									font-size: 22rpx;
									font-weight: normal;
								}
							}
						}
					}

					&.on {
						display: flex;
					}
				}
			}
		}
	}

	.productList {
		background-color: #F1F1F1;

		.sort {
			width: 710rpx;
			max-height: 380rpx;
			background: rgba(255, 255, 255, 1);
			border-radius: 16rpx;
			padding: 0 0rpx 30rpx;
			flex-wrap: wrap;
			margin: 25rpx auto 30rpx auto;
			padding-top: 8rpx;
			/* #ifdef MP */
			margin-top: 230rpx;

			/* #endif */
			.item {
				.pictrues {
					width: 90rpx;
					height: 90rpx;
					background: rgba(248, 248, 248, 1);
					border-radius: 50%;
					margin: 0 auto;
				}

				width: 20%;
				margin-top: 30rpx;
				text-align: center;

				image {
					width: 90rpx;
					height: 90rpx;
				}

				.text {
					color: #272727;
					font-size: 24rpx;
					margin-top: 10rpx;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
				}
			}
		}
	}

	.productList .list {
		padding: 0 20rpx;
	}

	.productList .list.on {
		background-color: #fff;
		border-top: 1px solid #f6f6f6;
	}

	.productList .list .item {
		width: 345rpx;
		margin-top: 20rpx;
		background-color: #fff;
		border-radius: 10rpx;
	}

	.productList .list .item.on {
		width: 100%;
		display: flex;
		border-bottom: 1rpx solid #f6f6f6;
		padding: 30rpx 0;
		margin: 0;
	}

	.productList .list .item .pictrue {
		position: relative;
		width: 100%;
		height: 345rpx;
	}

	.productList .list .item .pictrue.on {
		width: 180rpx;
		height: 180rpx;
	}

	.productList .list .item .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 10rpx 10rpx 0 0;
	}

	.productList .list .item .pictrue image.on {
		border-radius: 6rpx;
	}

	.productList .list .item .text {
		padding: 14rpx 17rpx 26rpx 17rpx;
		font-size: 28rpx;
		color: #212121;
	}

	.productList .list .item .text.on {
		width: 508rpx;
		padding: 0 0 0 22rpx;
	}

	.productList .list .item .text .money {
		font-size: 26rpx;
		font-weight: bold;
		margin-top: 8rpx;
	}

	.productList .list .item .text .coupon {
		background: rgba(255, 248, 247, 1);
		border: 1px solid rgba(233, 51, 35, 1);
		border-radius: 4rpx;
		font-size: 20rpx;
		margin-left: 18rpx;
		padding: 1rpx 4rpx;
	}

	.productList .list .item .text .money.on {
		margin-top: 50rpx;
	}

	.productList .list .item .text .money .num {
		font-size: 34rpx;
	}

	.productList .list .item .text .vip {
		font-size: 22rpx;
		color: #aaa;
		margin-top: 7rpx;
	}

	.productList .list .item .text .vip.on {
		margin-top: 12rpx;
	}

	.productList .list .item .text .vip .vip-money {
		font-size: 24rpx;
		color: #282828;
		font-weight: bold;
	}

	.productList .list .item .text .vip .vip-money image {
		width: 46rpx;
		height: 21rpx;
		margin-left: 4rpx;
	}

	.pictrue {
		position: relative;
	}

	.fixed {
		z-index: 100;
		position: fixed;
		left: 0;
		top: 0;
		background-color: #fff;
		box-shadow: 0 10rpx 20rpx -5rpx rgba(0, 0, 0, 0.06);
		// background: linear-gradient(90deg, red 50%, #ff5400 100%);
	}

	.mores-txt {
		width: 100%;
		align-items: center;
		justify-content: center;
		height: 70rpx;
		color: #999;
		font-size: 24rpx;

		.iconfont {
			margin-top: 2rpx;
			font-size: 20rpx;
		}
	}

	.menu-txt {
		font-size: 24rpx;
		color: #454545;
	}

	.mp-bg {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 330rpx;
		// background: linear-gradient(90deg, #fff 50%, #fff 100%);
		// border-radius: 0 0 30rpx 30rpx;
	}

	.main {
		padding: 0 20rpx;
	}
</style>
