<template>
	<view class="product-con">
		<view class="head-wrapper" :style="'top:'+statusBarHeight">
			<view class="head-menu">
				<view class='iconfont icon-xiangzuo' @click="returns"></view>
				<view class="iconfont icon-shouye4" @click="goHome"></view>
			</view>
		</view>

		<view>
			<scroll-view :scroll-top="scrollTop" scroll-y='true' scroll-with-animation="true" :style='"height:"+height+"px;"'
			 @scroll="scroll">
				<view id="past0">
					<productConSwiper :imgUrls="storeInfo.slider_image" :videoline="storeInfo.video_link"></productConSwiper>
					<view class='nav acea-row row-between-wrapper'>
						<view class='money'>￥<text class='num'>{{storeInfo.price}}</text><text class='y-money'>￥{{storeInfo.ot_price}}</text></view>
						<view class='acea-row row-middle'>
							<view class='time'>
								<view>距秒杀结束仅剩</view>
								<!-- <view class='timeCon'>
										<text class='num'>{{countDownHour}}</text>：
										<text class='num'>{{countDownMinute}}</text>：
										<text class='num'>{{countDownSecond}}</text>
									</view> -->
								<countDown :is-day="false" :tip-text="' '" :day-text="' '" :hour-text="' : '" :minute-text="' : '" :second-text="' '"
								 :datatime="datatime"></countDown>
							</view>
							<!-- <view class="timeState" wx:if="{{status == 0}}">已结束</view>
					<view class="timeState" wx:if="{{status == 2}}">即将开始</view> -->
							<!-- <view class='iconfont icon-jiantou'></view> -->
						</view>
					</view>
					<view class='wrapper'>
						<!--<view class='share acea-row row-between row-bottom'>
							<view class='money font-color'>
								￥
								<text class='num'>{{storeInfo.price}}</text>
								<text class='vip-money' v-if="storeInfo.vip_price && storeInfo.vip_price > 0">￥{{storeInfo.vip_price}}</text>
								<image v-if="storeInfo.vip_price && storeInfo.vip_price > 0" src="/static/images/vip.png"></image>
							</view>
							
						</view>-->
						<view class='introduce'>{{storeInfo.store_name}}
							<view class='iconfont icon-fenxiang' @click="listenerActionSheet"></view>
						</view>
						<view class='label acea-row row-between-wrapper'>
							<view>原价:￥{{storeInfo.ot_price}}</view>
							<view>库存:{{storeInfo.stock - storeInfo.sales}}{{storeInfo.unit_name}}</view>
							<view>销量:{{storeInfo.sales}}{{storeInfo.unit_name}}</view>
						</view>
					</view>
					<view class='attribute acea-row row-between-wrapper' @click="selecAttr">
						<view>{{attrTxt}}：
							<text class='atterTxt'>{{attrValue}}</text>
						</view>
						<view class='iconfont icon-jiantou'></view>
					</view>
				</view>
				<view class='userEvaluation' id="past1" v-if="replyCount>0">
					<view class='title acea-row row-between-wrapper'>
						<view>用户评价({{replyCount}})</view>
						<navigator class='praise' hover-class='none' :url='"/pages/users/goods_comment_list/index?product_id="+id'>
							<text class='font-color'>{{replyChance}}</text>好评率
							<text class='iconfont icon-jiantou'></text>
						</navigator>
					</view>
					<block v-if="replyCount">
						<userEvaluation :reply="reply"></userEvaluation>
					</block>
				</view>
				<!-- 商铺信息 -->
				<view class="store-wrapper" v-if="storeInfo.merchant">
					<view class="store-hd">
						<view class="store-info">
							<view class="logo">
								<image :src="storeInfo.merchant.mer_avatar" mode=""></image>
							</view>
							<view class="info">
								<view class="name">{{storeInfo.merchant.mer_name}}</view>
								<view class="txt">{{storeInfo.merchant.care_count}}人关注</view>
							</view>
							<navigator :url="'/pages/store/home/index?id='+storeInfo.merchant.mer_id" class="link" hover-class="none">进店</navigator>
						</view>
						<view class="score-wrapper">
							<view class="item">
								商品描述<text>{{storeInfo.merchant.product_score}}</text>
							</view>
							<view class="item">
								卖家服务<text>{{storeInfo.merchant.service_score}}</text>
							</view>
							<view class="item">
								物流服务<text>{{storeInfo.merchant.postage_score}}</text>
							</view>
						</view>
					</view>
					<view class="con-box">
						<view class="title">店铺推荐</view>
						<view class="img-box">
							<view class="img-item" v-for="(item,index) in storeInfo.merchant.recommend" :key="index" @click="goProDetail(item)">
								<image :src="item.image" mode=""></image>
								<view class="txt">
									<view class="title line1">{{item.store_name}}</view>
									<view class="price">
										<text>￥</text>{{item.price}}
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
				<view class='product-intro' id="past3">
					<view class='title'>产品介绍</view>
					<view class='conter' v-if="description">
						<jyf-parser :html="description.content" ref="article" :tag-style="tagStyle"></jyf-parser>
					</view>
				</view>
				<view style='height:120rpx;'></view>
			</scroll-view>
		</view>
		<view class='footer acea-row row-between-wrapper'>

			<navigator hover-class="none" class="item" :url="`/pages/chat/customer_list/chat?mer_id=${storeInfo.mer_id}&uid=${uid}&productId=${id}`">
				<view class="iconfont icon-kefu"></view>
				<view>客服</view>
			</navigator>
			<view @click="setCollect" class='item'>
				<view class='iconfont icon-shoucang1' v-if="storeInfo.isRelation"></view>
				<view class='iconfont icon-shoucang' v-else></view>
				<view>收藏</view>
			</view>
			<!-- <navigator open-type='switchTab' class="animated item" :class="animated==true?'bounceIn':''" url='/pages/order_addcart/order_addcart'
			 hover-class="none">
				<view class='iconfont icon-gouwuche1'>
					<text class='num bg-color'>{{CartCount || 0}}</text>
				</view>
				<view>购物车</view>
			</navigator>-->
			<view class='bnt acea-row'>
				<!-- <form @submit="joinCart" report-submit='true'>
					<button class='joinCart bnts' form-type="submit">加入购物车</button>
				</form> -->
				<form @submit="joinCart" report-submit='true'>
					<button class='joinCart bnts' :disabled="storeInfo.old_status == 0" @tap="openAlone">单独购买</button>
				</form>
				<form @submit="goBuy" report-submit='true' v-if="attr.productSelect && storeInfo.seckill_status == '1'">
					<button v-if="attr.productSelect.stock == 0" class='buy bnts' form-type="submit" disabled>已售罄</button>
					<button v-else class='buy bnts' form-type="submit">立即购买</button>
				</form>
				<form @submit="goBuy" report-submit='true' v-if="attr.productSelect && storeInfo.seckill_status == '0'">
					<button class='buy bnts' form-type="submit" disabled>未开始</button>
				</form>
				<form @submit="goBuy" report-submit='true' v-if="attr.productSelect && storeInfo.seckill_status == '-1'">
					<button class='buy bnts' form-type="submit" disabled>已结束</button>
				</form>
			</view>
		</view>
		<block v-if="sharePacket.max&&sharePacket.min">
			<shareRedPackets :sharePacket="sharePacket" @listenerActionSheet="listenerActionSheet" @closeChange="closeChange"
			 :showAnimate="showAnimate" @boxStatus="boxStatus"></shareRedPackets>
		</block>
		<!-- 组件 -->
		<productWindow :attr="attr" :isShow='1' :iSplus='1' @myevent="onMyEvent" @ChangeAttr="ChangeAttr" @ChangeCartNum="ChangeCartNum"
		 @attrVal="attrVal" @iptCartNum="iptCartNum" id='product-window'></productWindow>
		<!-- 分享按钮 -->
		<view class="generate-posters acea-row row-middle" :class="posters ? 'on' : ''">
			<!-- #ifndef MP -->
			<button class="item" hover-class='none' v-if="weixinStatus === true" @click="H5ShareBox = true">
				<view class="iconfont icon-weixin3"></view>
				<view class="">发送给朋友</view>
			</button>
			<!-- #endif -->
			<!-- #ifdef MP -->
			<button class="item" open-type="share" hover-class='none' @click="goFriend">
				<view class="iconfont icon-weixin3"></view>
				<view class="">发送给朋友</view>
			</button>
			<!-- #endif -->
			<button class="item" hover-class='none' @click="goPoster">
				<view class="iconfont icon-haibao"></view>
				<view class="">生成海报</view>
			</button>
		</view>
		<view class="mask" v-if="posters" @click="listenerActionClose"></view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
		<!-- 海报展示 -->
		<view class='poster-pop' v-if="posterImageStatus">
			<image src='../../../static/images/poster-close.png' class='close' @click="posterImageClose"></image>
			<image :src='posterImage'></image>
			<!-- #ifndef H5  -->
			<view class='save-poster' @click="savePosterPath">保存到手机</view>
			<!-- #endif -->
			<!-- #ifdef H5 -->
			<view class="keep">长按图片可以保存到手机</view>
			<!-- #endif -->
		</view>
		<view class='mask' v-if="posterImageStatus"></view>
		<canvas class="canvas" canvas-id='myCanvas' v-if="canvasStatus"></canvas>
		<!-- 发送给朋友图片 -->
		<view class="share-box" v-if="H5ShareBox">
			<image src="/static/images/share-info.png" @click="H5ShareBox = false"></image>
		</view>
	</view>
</template>

<script>
	var statusBarHeight = uni.getSystemInfoSync().statusBarHeight + 'px';
	import {
		getSeckillProductDetail,
		getProductCode,
		collectAdd,
		collectDel,
		postCartAdd
	} from '@/api/store.js';
	import {
		getUserInfo,
		userShare
	} from '@/api/user.js';
	import {
		getCoupons,
		getShopCoupons
	} from '@/api/api.js';
	import {
		getCartCounts
	} from '@/api/order.js';
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	import {
		imageBase64
	} from "@/api/public";
	import productConSwiper from '@/components/productConSwiper';
	import productWindow from '@/components/productWindow';
	import userEvaluation from '@/components/userEvaluation';
	import shareRedPackets from '@/components/shareRedPackets';
	import countDown from '@/components/countDown';
	import home from '@/components/home';
	import {
		silenceBindingSpread
	} from "@/utils";
	import parser from "@/components/jyf-parser/jyf-parser";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	let app = getApp();
	export default {
		components: {
			productConSwiper,
			productWindow,
			userEvaluation,
			shareRedPackets,
			home,
			countDown,
			"jyf-parser": parser,
			// #ifdef MP
			authorize
			// #endif
		},
		data() {
			let that = this;
			return {
				countDownHour: "00",
				countDownMinute: "00",
				countDownSecond: "00",
				datatime: '',
				statusBarHeight: statusBarHeight, //系统导航条高度
				//属性是否打开
				coupon: {
					'coupon': false,
					list: [],
				},
				attrTxt: '请选择', //属性页面提示
				attrValue: '', //已选属性
				animated: false, //购物车动画
				id: 0, //商品id
				replyCount: 0, //总评论数量
				reply: [], //评论列表
				storeInfo: {}, //商品详情
				productValue: [], //系统属性
				cart_num: 1, //购买数量
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				isOpen: false, //是否打开属性组件
				actionSheetHidden: true,
				posterImageStatus: false,
				storeImage: '', //海报产品图
				PromotionCode: '', //二维码图片
				canvasStatus: false, //海报绘图标签
				posterImage: '', //海报路径
				posterbackgd: '/static/images/posterbackgd.png',
				sharePacket: {
					isState: true, //默认不显示
				}, //分销商详细
				// uid: 0, //用户uid
				circular: false,
				autoplay: false,
				interval: 3000,
				duration: 500,
				clientHeight: "",
				systemStore: {}, //门店信息
				replyChance: 0,
				CartCount: 0,
				isDown: true,
				storeSelfMention: true,
				posters: false,
				weixinStatus: false,
				attr: {
					cartAttr: false,
					productAttr: [],
					productSelect: {}
				},
				description: '',
				navActive: 0,
				H5ShareBox: false, //公众号分享图片
				activity: [],
				retunTop: true, //顶部返回
				navH: "",
				navList: [],
				opacity: 0,
				scrollY: 0,
				topArr: [],
				toView: '',
				height: 0,
				heightArr: [],
				lock: false,
				scrollTop: 0,
				tagStyle: {
					img: 'width:100%;'
				},
				// 动画状态
				showAnimate: true
			};
		},
		computed: mapGetters(['isLogin', 'uid']),
		onLoad(options) {
			let that = this
			var pages = getCurrentPages();
			if (pages.length <= 1) {
				that.retunTop = false
			}
			// #ifdef MP
			this.navH = app.globalData.navHeight;
			// #endif
			// #ifndef MP
			this.navH = 96;
			// #endif
			this.id = options.id;
			if (options.id) {
				this.id = options.id
				this.datatime = Number(options.time)
				// this.status = options.status
			}
			uni.getSystemInfo({
				success: function(res) {
					that.height = res.windowHeight
					//res.windowHeight:获取整个窗口高度为px，*2为rpx；98为头部占据的高度；
				},
			});
			//扫码携带参数处理
			// #ifdef MP
			if (options.scene) {

				let value = this.$util.getUrlParams(decodeURIComponent(options.scene));
				if (value.id) options.id = value.id;
				//记录推广人uid
				if (value.pid) app.globalData.spid = value.pid;
				if (value.time) this.datatime = value.time
			}
			if (!options.id) {
				return this.$util.Tips({
					title: '缺少参数无法查看商品'
				}, {
					tab: 3,
					url: 1
				});
			}
			//记录推广人uid
			if (options.spid) app.globalData.spid = options.spid;

			// #endif
			this.getGoodsDetails();
			this.downloadFilePromotionCode();
			//#ifdef H5
			this.isLogin && silenceBindingSpread();
			//#endif
		},
		onReady() {},
		show() {
			console.log('222')
		},
		/**
		 * 用户点击右上角分享
		 */
		// #ifdef MP
		onShareAppMessage: function() {
			let that = this;
			that.$set(that, 'actionSheetHidden', !that.actionSheetHidden);
			userShare();
			return {
				title: that.storeInfo.store_name || '',
				imageUrl: that.storeInfo.image || '',
				path: '/pages/goods_details/index?id=' + that.id + '&spid=' + that.uid,
			}
		},
		// #endif
		methods: {
			goProDetail(item) {
				console.log(item, 'goProDetail')
				uni.redirectTo({
					url: '/pages/goods_details/index?id=' + item.product_id
				})
			},
			boxStatus(data) {
				this.showAnimate = data
			},
			closeChange: function() {
				this.$set(this.sharePacket, 'isState', true);
			},
			goActivity: function(e) {
				console.log(e);
				let item = e;
				if (item.type === "1") {
					uni.navigateTo({
						url: `/pages/activity/goods_seckill_details/index?id=${item.id}&time=${item.time}&status=1`
					});
				} else if (item.type === "2") {
					uni.navigateTo({
						url: `/pages/activity/goods_bargain_details/index?id=${item.id}&bargain=${this.uid}`
					});
				} else {
					uni.navigateTo({
						url: `/pages/activity/goods_combination_details/index?id=${item.id}`
					});
				}
			},
			/**
			 * 购物车手动填写
			 * 
			 */
			iptCartNum: function(e) {
				this.$set(this.attr.productSelect, 'cart_num', e);
			},
			// 后退
			returns: function() {
				uni.navigateBack()
			},
			// 首页
			goHome() {
				uni.switchTab({
					url: '/pages/index/index'
				});
			},
			tap: function(index) {
				var id = "past" + index;
				var index = index;
				var that = this;
				// if (!this.data.good_list.length && id == "past2") {
				//   id = "past3"
				// }
				this.$set(this, 'toView', id);
				this.$set(this, 'navActive', index);
				this.$set(this, 'lock', true);
				this.$set(this, 'scrollTop', index > 0 ? that.topArr[index] - (app.globalData.navHeight / 2) : that.topArr[index]);
			},
			scroll: function(e) {
				var that = this,
					scrollY = e.detail.scrollTop;
				var opacity = scrollY / 200;
				opacity = opacity > 1 ? 1 : opacity;
				that.$set(that, 'showAnimate', false);
				that.$set(that, 'opacity', opacity);
				that.$set(that, 'scrollY', scrollY);
				if (that.lock) {
					that.$set(that, 'lock', false)
					return;
				}
				for (var i = 0; i < that.topArr.length; i++) {
					if (scrollY < that.topArr[i] - (app.globalData.navHeight / 2) + that.heightArr[i]) {
						that.$set(that, 'navActive', i)
						break
					}
				}
			},
			/*
			 *去商品详情页 
			 */
			goDetail(item) {
				if (item.activity.length == 0) {
					uni.redirectTo({
						url: '/pages/goods_details/index?id=' + item.id
					})
					return
				}
				// 砍价
				if (item.activity && item.activity.type == 2) {
					uni.redirectTo({
						url: `/pages/activity/goods_bargain_details/index?id=${item.activity.id}&bargain=${this.uid}`
					})
					return
				}
				// 拼团
				if (item.activity && item.activity.type == 3) {
					uni.redirectTo({
						url: `/pages/activity/goods_combination_details/index?id=${item.activity.id}`
					})
					return
				}
				// 秒杀
				if (item.activity && item.activity.type == 1) {
					uni.redirectTo({
						url: `/pages/activity/goods_seckill_details/index?id=${item.activity.id}&time=${item.activity.time}&status=1`
					})
					return
				}
			},
			// 微信登录回调
			onLoadFun: function(e) {
				this.getCartCount();
				this.downloadFilePromotionCode();
				// this.getUserInfo();
				// this.get_product_collect();
				this.isShowAuth = false
			},
			/*
			 * 获取用户信息
			 */
			getUserInfo: function() {
				let that = this;
				that.$set(that.sharePacket, 'isState', that.sharePacket.priceName != 0 ? false : true);
				getUserInfo().then(res => {

					// that.$set(that, 'uid', res.data.uid);
				});
			},
			/**
			 * 购物车数量加和数量减
			 * 
			 */
			ChangeCartNum: function(changeValue) {
				//changeValue:是否 加|减
				//获取当前变动属性
				let productSelect = this.productValue[this.attrValue];
				//如果没有属性,赋值给商品默认库存
				if (productSelect === undefined && !this.attr.productAttr.length)
					productSelect = this.attr.productSelect;
				//无属性值即库存为0；不存在加减；
				if (productSelect === undefined) return;
				let stock = productSelect.stock || 0;
				let num = this.attr.productSelect;
				if (changeValue) {
					num.cart_num++;
					if (num.cart_num > stock) {
						this.$set(this.attr.productSelect, "cart_num", stock);
						this.$set(this, "cart_num", stock);
					}
				} else {
					num.cart_num--;
					if (num.cart_num < 1) {
						this.$set(this.attr.productSelect, "cart_num", 1);
						this.$set(this, "cart_num", 1);
					}
				}
			},
			attrVal(val) {
				this.$set(this.attr.productAttr[val.indexw], 'index', this.attr.productAttr[val.indexw].attr_values[val.indexn]);
			},
			/**
			 * 属性变动赋值
			 * 
			 */
			ChangeAttr: function(res) {
				let productSelect = this.productValue[res];
				if (productSelect && productSelect.stock > 0) {
					this.$set(this.attr.productSelect, "image", productSelect.image);
					this.$set(this.attr.productSelect, "price", productSelect.price);
					this.$set(this.attr.productSelect, "stock", productSelect.stock);
					this.$set(this.attr.productSelect, "unique", productSelect.unique);
					this.$set(this.attr.productSelect, "cart_num", 1);
					this.$set(this, "attrValue", res);
					this.$set(this, "attrTxt", "已选择");
				} else {
					this.$set(this.attr.productSelect, "image", this.storeInfo.image);
					this.$set(this.attr.productSelect, "price", this.storeInfo.price);
					this.$set(this.attr.productSelect, "stock", 0);
					this.$set(this.attr.productSelect, "unique", "");
					this.$set(this.attr.productSelect, "cart_num", 0);
					this.$set(this, "attrValue", "");
					this.$set(this, "attrTxt", "请选择");
				}
			},

			setClientHeight: function() {
				let that = this;

				let view = uni.createSelectorQuery().in(this).select("#list0");
				view.fields({
					size: true,
				}, data => {
					that.$set(that, 'clientHeight', data.height + 20)
				}).exec();
			},
			/**
			 * 获取产品详情
			 * 
			 */
			getGoodsDetails: function() {
				uni.showLoading({
					title: '加载中',
					mask: true
				});
				let that = this;
				getSeckillProductDetail(that.id).then(res => {
					console.log(res)
					uni.hideLoading();
					let storeInfo = res.data;
					that.$set(that, 'storeInfo', storeInfo);
					that.$set(that, 'description', storeInfo.content);
					that.$set(that, 'reply', res.data.topReply ? [res.data.topReply] : []);
					that.$set(that, 'replyCount', res.data.replayData.count);
					that.$set(that, 'replyChance', res.data.replayData.rate);
					that.$set(that.attr, 'productAttr', res.data.attr);
					that.$set(that, 'productValue', res.data.sku);
					that.$set(that.sharePacket, 'priceName', res.data.priceName);
					that.$set(that.sharePacket, 'max', res.data.max_extension);
					that.$set(that.sharePacket, 'min', res.data.min_extension);
					that.$set(that, 'systemStore', res.data.system_store);
					that.$set(that, 'storeSelfMention', res.data.store_self_mention);
					that.$set(that, 'PromotionCode', storeInfo.code_base);
					that.$set(that, 'activity', res.data.activity ? res.data.activity : []);
					uni.setNavigationBarTitle({
						title: storeInfo.store_name.substring(0, 7) + "..."
					})
					// #ifdef H5
					that.$set(that, 'storeImage', that.storeInfo.image);
					that.getImageBase64();
					if (that.isLogin) {
						that.getCartCount();
					};
					this.ShareInfo();
					// #endif
					// if (that.isLogin) {
					// 	that.getUserInfo();
					// };
					setTimeout(function() {
						that.infoScroll();
					}, 500);
					// #ifndef H5
					that.downloadFilestoreImage();
					// #endif
					that.DefaultSelect();
				}).catch(err => {
					//状态异常返回上级页面
					return that.$util.Tips({
						title: err
					}, {
						tab: 3,
						url: 1
					});
				})
			},
			infoScroll: function() {
				var that = this,
					topArr = [],
					heightArr = [];
				for (var i = 0; i < that.navList.length; i++) { //productList
					//获取元素所在位置
					var query = uni.createSelectorQuery().in(this);
					var idView = "#past" + i;
					// if (!that.data.good_list.length && i == 2) {
					//   var idView = "#past" + 3;
					// }
					query.select(idView).boundingClientRect();
					query.exec(function(res) {
						var top = res[0].top;
						var height = res[0].height;
						topArr.push(top);
						heightArr.push(height);
						that.$set(that, 'topArr', topArr);
						that.$set(that, 'heightArr', heightArr);
					});
				};
			},
			/**
			 * 拨打电话
			 */
			makePhone: function() {
				uni.makePhoneCall({
					phoneNumber: this.systemStore.phone
				})
			},
			/**
			 * 打开地图
			 * 
			 */
			showMaoLocation: function() {
				if (!this.systemStore.latitude || !this.systemStore.longitude) return this.$util.Tips({
					title: '缺少经纬度信息无法查看地图！'
				});
				uni.openLocation({
					latitude: parseFloat(this.systemStore.latitude),
					longitude: parseFloat(this.systemStore.longitude),
					scale: 8,
					name: this.systemStore.name,
					address: this.systemStore.address + this.systemStore.detailed_address,
					success: function() {},
				});
			},
			/**
			 * 默认选中属性
			 * 
			 */
			DefaultSelect: function() {
				let productAttr = this.attr.productAttr;
				let value = [];
				let arr = []
				let unSortArr = []
				// this.attr.productAttr.forEach((item,index)=>{
				// 	arr.push(item.attr_values[0])
				// })
				// // 默认选中未排序 改变unSortArr数组原始对象指向
				// unSortArr = unSortArr.concat(arr)
				// // 默认选中排序
				// arr = arr.sort().join(',')
				// // for(let i; i<unSortArr.length;i++){

				// // }	

				console.log(arr, 'arr')


				for (var key in this.productValue) {
					if (this.productValue[key].stock > 0) {
						value = this.attr.productAttr.length ? key.split(",") : [];
						break;
					}
				}
				for (let i = 0; i < productAttr.length; i++) {
					this.$set(productAttr[i], "index", value[i]);
				}


				console.log(value, 'value')
				//sort();排序函数:数字-英文-汉字；
				let productSelect = this.productValue[value.join(",")];
				if (productSelect && productAttr.length) {
					this.$set(
						this.attr.productSelect,
						"store_name",
						this.storeInfo.store_name
					);
					this.$set(this.attr.productSelect, "image", productSelect.image);
					this.$set(this.attr.productSelect, "price", productSelect.price);
					this.$set(this.attr.productSelect, "stock", productSelect.stock);
					this.$set(this.attr.productSelect, "unique", productSelect.unique);

					this.$set(this, "attrValue", value.join(","));
					this.$set(this, "attrTxt", "已选择");
					if (productSelect.stock == 0) {
						this.$set(this.attr.productSelect, "cart_num", 0);
					} else {
						this.$set(this.attr.productSelect, "cart_num", 1);
					}
				} else if (!productSelect && productAttr.length) {
					this.$set(
						this.attr.productSelect,
						"store_name",
						this.storeInfo.store_name
					);
					this.$set(this.attr.productSelect, "image", this.storeInfo.image);
					this.$set(this.attr.productSelect, "price", this.storeInfo.price);
					this.$set(this.attr.productSelect, "stock", 0);
					this.$set(this.attr.productSelect, "unique", "");
					this.$set(this.attr.productSelect, "cart_num", 0);
					this.$set(this, "attrValue", "");
					this.$set(this, "attrTxt", "请选择");
				} else if (!productSelect && !productAttr.length) {
					this.$set(
						this.attr.productSelect,
						"store_name",
						this.storeInfo.store_name
					);
					this.$set(this.attr.productSelect, "image", this.storeInfo.image);
					this.$set(this.attr.productSelect, "price", this.storeInfo.price);
					this.$set(this.attr.productSelect, "stock", this.storeInfo.stock);
					this.$set(
						this.attr.productSelect,
						"unique",
						this.storeInfo.unique || ""
					);
					this.$set(this.attr.productSelect, "cart_num", 1);
					this.$set(this, "attrValue", "");
					this.$set(this, "attrTxt", "请选择");
				} else if (productSelect && !productAttr.length) {
					this.$set(
						this.attr.productSelect,
						"store_name",
						this.storeInfo.store_name
					);
					this.$set(this.attr.productSelect, "image", productSelect.image);
					this.$set(this.attr.productSelect, "price", productSelect.price);
					this.$set(this.attr.productSelect, "stock", productSelect.stock);
					this.$set(this.attr.productSelect, "unique", productSelect.unique);

					this.$set(this, "attrValue", value.join(","));
					this.$set(this, "attrTxt", "已选择");
					if (productSelect.stock == 0) {
						this.$set(this.attr.productSelect, "cart_num", 0);
					} else {
						this.$set(this.attr.productSelect, "cart_num", 1);
					}
				}
			},
			/*
			 *  单独购买
			 */
			openAlone: function() {
				uni.navigateTo({
					url: `/pages/goods_details/index?id=${this.storeInfo.old_product_id}`
				})
			},
			/** 
			 * 
			 * 
			 * 收藏商品
			 */
			setCollect: function() {
				if (this.isLogin === false) {
					// #ifdef H5 || APP-PLUS
					toLogin();
					// #endif 
					// #ifdef MP
					this.isAuto = true;
					this.$set(this, 'isShowAuth', true)
					// #endif
				} else {
					let that = this;
					if (this.storeInfo.isRelation) {
						collectDel({
							type: 1,
							type_id: this.id
						}).then(res => {
							that.$set(that.storeInfo, 'isRelation', !that.storeInfo.isRelation);
						})
					} else {
						collectAdd({
							type_id: this.id,
							type: 1
						}).then(res => {
							that.$set(that.storeInfo, 'isRelation', !that.storeInfo.isRelation);
						})
					}
				}
			},
			/**
			 * 打开属性插件
			 */
			selecAttr: function() {
				this.$set(this.attr, 'cartAttr', true);
				this.$set(this, 'isOpen', true);
			},

			onMyEvent: function() {
				this.$set(this.attr, 'cartAttr', false);
				this.$set(this, 'isOpen', false);
			},
			/**
			 * 打开属性加入购物车
			 * 
			 */
			joinCart: function(e) {
				//是否登录
				if (this.isLogin === false) {
					// #ifdef H5 || APP-PLUS
					toLogin();
					// #endif
					// #ifdef MP
					this.$set(this, 'isAuto', true);
					this.$set(this, 'isShowAuth', true)
					// #endif
				} else {
					this.goCat();
				}
			},
			/*
			 * 加入购物车
			 */
			goCat: function(news) {
				let that = this,
					productSelect = that.productValue[this.attrValue];
				//打开属性
				if (that.attrValue) {
					//默认选中了属性，但是没有打开过属性弹窗还是自动打开让用户查看默认选中的属性
					that.attr.cartAttr = !that.isOpen ? true : false;
				} else {
					if (that.isOpen) that.attr.cartAttr = true;
					else that.attr.cartAttr = !that.attr.cartAttr;

				}
				//只有关闭属性弹窗时进行加入购物车
				if (that.attr.cartAttr === true && that.isOpen === false)
					return (that.isOpen = true);
				//如果有属性,没有选择,提示用户选择
				if (
					that.attr.productAttr.length &&
					that.isOpen === true &&
					productSelect.stock == 0
				)
					return that.$util.Tips({
						title: "产品库存不足，请选择其它"
					});
				let q = {
					// productId: that.id,
					// cartNum: that.attr.productSelect.cart_num,
					is_new: 1,
					// uniqueId: that.attr.productSelect !== undefined ?
					// 	that.attr.productSelect.unique : ""
					product_id: that.id,
					cart_num: that.attr.productSelect.cart_num,
					product_attr_unique: that.attr.productSelect !== undefined ? that.attr.productSelect.unique : ""
				};
				postCartAdd(q)
					.then(function(res) {
						that.isOpen = false;
						that.attr.cartAttr = false;
						if (news) {
							uni.navigateTo({
								url: '/pages/users/order_confirm/index?cartId=' + res.data.cart_id + '&seckillId=' + that.id
							});
						} else {
							that.$util.Tips({
								title: "添加购物车成功",
								success: () => {
									that.getCartCount(true);
								}
							});
						}
					})
					.catch(res => {
						that.isOpen = false;
						return that.$util.Tips({
							title: res
						});
					});
			},
			/**
			 * 获取购物车数量
			 * @param boolean 是否展示购物车动画和重置属性
			 */
			getCartCount: function(isAnima) {
				let that = this;
				const isLogin = that.isLogin;
				if (isLogin) {
					getCartCounts().then(res => {
						that.CartCount = res.data[0].count;
						//加入购物车后重置属性
						if (isAnima) {
							that.animated = true;
							setTimeout(function() {
								that.animated = false;
							}, 500);
						}
					});
				}
			},
			/**
			 * 立即购买
			 */
			goBuy: function(e) {
				if (this.isLogin === false) {
					// #ifdef H5 || APP-PLUS
					toLogin();
					// #endif
					// #ifdef MP
					this.$set(this, 'isAuto', true);
					this.$set(this, 'isShowAuth', true);
					// #endif
				} else {
					this.goCat(true);
				}
			},
			// 授权关闭
			authColse: function(e) {
				console.log(e, 'eeeee')
				this.isShowAuth = e
			},
			/**
			 * 分享打开
			 * 
			 */
			listenerActionSheet: function() {
				if (this.isLogin === false) {
					// #ifdef H5 || APP-PLUS
					toLogin();
					// #endif
					// #ifdef MP
					this.$set(this, 'isAuto', true);
					this.$set(this, 'isShowAuth', true);
					// #endif
				} else {
					// #ifdef H5
					if (this.$wechat.isWeixin() === true) {
						this.weixinStatus = true;
					}
					this.downloadFilePromotionCode();
					// #endif
					this.posters = true;

				}
			},
			// 分享关闭
			listenerActionClose: function() {
				this.posters = false;
			},
			//隐藏海报
			posterImageClose: function() {
				this.posterImageStatus = false
			},
			//替换安全域名
			setDomain: function(url) {
				url = url ? url.toString() : '';
				//本地调试打开,生产请注销
				if (url.indexOf("https://") > -1) return url;
				else return url.replace('http://', 'https://');
			},
			//获取海报产品图
			downloadFilestoreImage: function() {
				let that = this;
				uni.downloadFile({
					// #ifdef MP
					url: that.setDomain(that.storeInfo.image),
					// #endif
					// #ifdef H5
					url: that.storeInfo.image,
					// #endif
					success: function(res) {
						that.storeImage = res.tempFilePath;
					},
					fail: function() {
						return that.$util.Tips({
							title: ''
						});
						that.storeImage = '';
					},
				});
			},
			/**
			 * 获取产品分销二维码
			 * @param function successFn 下载完成回调
			 * 
			 */
			downloadFilePromotionCode: function(successFn) {
				let that = this;
				getProductCode(that.id).then(res => {
					uni.downloadFile({
						// #ifdef MP
						url: that.setDomain(res.data.url),
						// #endif
						// #ifdef H5
						url: res.data.url,
						// #endif
						success: function(res) {
							that.$set(that, 'isDown', false);
							if (typeof successFn == 'function')
								successFn && successFn(res.tempFilePath);
							else
								that.$set(that, 'PromotionCode', res.tempFilePath);
						},
						fail: function() {
							that.$set(that, 'isDown', false);
							that.$set(that, 'PromotionCode', '');
						},
					});
				}).catch(err => {
					that.$set(that, 'isDown', false);
					that.$set(that, 'PromotionCode', '');
				});
			},
			getImageBase64: function() {
				let that = this;
				imageBase64(that.storeImage, that.PromotionCode)
					.then(res => {
						that.storeImage = res.data.image;
						that.PromotionCode = res.data.code;
					})
					.catch(() => {});
			},
			// 小程序关闭分享弹窗；
			goFriend: function() {
				this.posters = false;
			},
			/**
			 * 生成海报
			 */
			goPoster: function() {
				let that = this;
				that.posters = false;
				that.$set(that, 'canvasStatus', true);
				let arr2 = [that.posterbackgd, that.storeImage, that.PromotionCode];
				// // #ifndef H5
				if (that.isDown) return that.$util.Tips({
					title: '正在下载海报,请稍后再试！'
				});
				// // #endif
				uni.getImageInfo({
					src: that.PromotionCode,
					fail: function(res) {
						// #ifdef H5
						return that.$util.Tips({
							title: res
						});
						// #endif
						// #ifdef MP
						console.log(res, 'res')
						return that.$util.Tips({
							title: '小程序二维码需要发布正式版后才能获取到'
						});
						// #endif
					},
					success() {
						if (arr2[2] == '') {
							//海报二维码不存在则从新下载
							// // #ifndef H5
							that.downloadFilePromotionCode(function(msgPromotionCode) {
								arr2[2] = msgPromotionCode;
								if (arr2[2] == '')
									return that.$util.Tips({
										title: '海报二维码生成失败！'
									});
								that.$util.PosterCanvas(arr2, that.storeInfo.store_name, that.storeInfo.price, function(tempFilePath) {
									that.$set(that, 'posterImage', tempFilePath);
									that.$set(that, 'posterImageStatus', true);
									that.$set(that, 'canvasStatus', false);
									that.$set(that, 'actionSheetHidden', !that.actionSheetHidden);
								});
							});
							// // #endif
							// // #ifdef H5
							// that.$util.PosterCanvas(arr2, that.storeInfo.store_name, that.storeInfo.price, function(tempFilePath) {
							// 	that.$set(that, 'posterImage', tempFilePath);
							// 	that.$set(that, 'posterImageStatus', true);
							// 	that.$set(that, 'canvasStatus', false);
							// 	that.$set(that, 'actionSheetHidden', !that.actionSheetHidden);
							// });
							// // #endif
						} else {
							//生成推广海报
							that.$util.PosterCanvas(arr2, that.storeInfo.store_name, that.storeInfo.price, function(tempFilePath) {
								that.$set(that, 'posterImage', tempFilePath);
								that.$set(that, 'posterImageStatus', true);
								that.$set(that, 'canvasStatus', false);
								that.$set(that, 'actionSheetHidden', !that.actionSheetHidden);
							});
						}
					},
				});
			},

			/*
			 * 保存到手机相册
			 */
			// #ifdef MP
			savePosterPath: function() {
				let that = this;
				uni.getSetting({
					success(res) {
						if (!res.authSetting['scope.writePhotosAlbum']) {
							uni.authorize({
								scope: 'scope.writePhotosAlbum',
								success() {
									uni.saveImageToPhotosAlbum({
										filePath: that.posterImage,
										success: function(res) {
											that.posterImageClose();
											that.$util.Tips({
												title: '保存成功',
												icon: 'success'
											});
										},
										fail: function(res) {
											that.$util.Tips({
												title: '保存失败'
											});
										}
									})
								}
							})
						} else {
							uni.saveImageToPhotosAlbum({
								filePath: that.posterImage,
								success: function(res) {
									that.posterImageClose();
									that.$util.Tips({
										title: '保存成功',
										icon: 'success'
									});
								},
								fail: function(res) {
									that.$util.Tips({
										title: '保存失败'
									});
								},
							})
						}
					}
				})
			},
			// #endif
			//#ifdef H5
			ShareInfo() {
				let data = this.storeInfo;
				let href = location.href;
				if (this.$wechat.isWeixin()) {

					getUserInfo().then(res => {
						href =
							href.indexOf("?") === -1 ?
							href + "?spread=" + res.data.uid :
							href + "&spread=" + res.data.uid;

						let configAppMessage = {
							desc: data.store_info,
							title: data.store_name,
							link: href,
							imgUrl: data.image
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

		},
	}
</script>

<style scoped lang="scss">
	.product-con .nav {
		background-image: url('data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QN/aHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzE0MiA3OS4xNjA5MjQsIDIwMTcvMDcvMTMtMDE6MDY6MzkgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6NDNlZTU0ZDMtNjEwZS03ZjQ4LWEwODgtNTZlMTZiNzI3NTQwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjI1NEI2MDUyM0ZDMjExRTk5OTg1REI1OUM1NjNEMUZCIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjI1NEI2MDUxM0ZDMjExRTk5OTg1REI1OUM1NjNEMUZCIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE4IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjUyMDIzNWNmLTIwNGYtOTQ0My05YTBiLWNmMmZlMTJmMDk3NCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo0M2VlNTRkMy02MTBlLTdmNDgtYTA4OC01NmUxNmI3Mjc1NDAiLz4gPC9yZGY6RGVzY3JpcHRpb24+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');
		background-repeat: no-repeat;
		background-size: 100% 100%;
		width: 100%;
		height: 100rpx;
		padding: 0 30rpx;
		box-sizing: border-box;
	}

	.product-con .nav .money {
		font-size: 28rpx;
		color: #fff;
	}

	.product-con .nav .money .num {
		font-size: 48rpx;
	}

	.product-con .nav .money .y-money {
		font-size: 26rpx;
		margin-left: 10rpx;
		text-decoration: line-through;
	}

	.product-con .nav .time {
		font-size: 20rpx;
		color: #fff;
		text-align: center;
	}

	.product-con .nav .time .timeCon {
		margin-top: 10rpx;
	}

	.product-con .nav .time .timeCon .num {
		padding: 0 7rpx;
		font-size: 22rpx;
		color: #ff3d3d;
		background-color: #fff;
		border-radius: 2rpx;
	}

	.product-con .nav .timeState {
		font-size: 28RPX;
		color: #FFF;
	}

	.product-con .nav .iconfont {
		color: #fff;
		font-size: 30rpx;
		margin-left: 20rpx;
	}

	.product-con .wrapper {
		padding: 32rpx;
		width: 100%;
		box-sizing: border-box;
	}

	.product-con .wrapper .introduce {
		margin: 0;
		padding-right: 20rpx;
		position: relative;

		.icon-fenxiang {
			position: absolute;
			top: 0;
			right: 0;
		}
	}

	.product-con .wrapper .introduce .infor {
		width: 570rpx;
	}

	.product-con .wrapper .introduce .iconfont {
		font-size: 37rpx;
		color: #515151;
	}

	.product-con .wrapper .label {
		margin: 18rpx 0 0 0;
		font-size: 24rpx;
		color: #82848f;
	}

	.product-con .wrapper .label .stock {
		width: 255rpx;
		margin-right: 28rpx;
	}

	.activity_pin {
		width: auto;
		height: 44rpx;
		line-height: 44rpx;
		background: linear-gradient(90deg, rgba(233, 51, 35, 1) 0%, rgba(250, 101, 20, 1) 100%);
		opacity: 1;
		border-radius: 22rpx;
		padding: 0 15rpx;
		margin-left: 19rpx;
	}

	.activity_miao {
		width: auto;
		height: 44rpx;
		line-height: 44rpx;
		padding: 0 15rpx;
		background: linear-gradient(90deg, rgba(250, 102, 24, 1) 0%, rgba(254, 161, 15, 1) 100%);
		opacity: 1;
		border-radius: 22rpx;
		margin-left: 19rpx;
	}

	.iconfonts {
		color: #fff !important;
		font-size: 28rpx;
	}

	.activity_title {
		font-size: 24rpx;
		color: #fff;
	}

	.activity_kan {
		width: auto;
		height: 44rpx;
		line-height: 44rpx;
		padding: 0 15rpx;
		background: linear-gradient(90deg, rgba(254, 159, 15, 1) 0%, rgba(254, 178, 15, 1) 100%);
		opacity: 1;
		border-radius: 22rpx;
		margin-left: 19rpx;
	}

	.mask {
		z-index: 300 !important;
	}

	.head-bar {
		background: #fff;
	}

	.generate-posters {
		width: 100%;
		height: 170rpx;
		background-color: #fff;
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 388;
		transform: translate3d(0, 100%, 0);
		transition: all 0.3s cubic-bezier(0.25, 0.5, 0.5, 0.9);
		border-top: 1rpx solid #eee;
	}

	.generate-posters.on {
		transform: translate3d(0, 0, 0);
	}

	.generate-posters .item {
		flex: 50%;
		text-align: center;
		font-size: 30rpx;
	}

	.generate-posters .item .iconfont {
		font-size: 80rpx;
		color: #5eae72;
	}

	.generate-posters .item .iconfont.icon-haibao {
		color: #5391f1;
	}

	.product-con .mask {
		z-index: 88;
	}

	.product-con .footer {
		padding: 0 20rpx 0 30rpx;
		position: fixed;
		left: 0;
		bottom: 0;
		width: 100%;
		box-sizing: border-box;
		height: 100rpx;
		background-color: #fff;
		z-index: 277;
		border-top: 1rpx solid #f0f0f0;
	}

	.product-con .footer .item {
		font-size: 18rpx;
		color: #666;
	}

	.product-con .footer .item .iconfont {
		text-align: center;
		font-size: 40rpx;
	}

	.product-con .footer .item .iconfont.icon-shoucang1 {
		color: #f00;
	}

	.product-con .footer .item .iconfont.icon-gouwuche1 {
		font-size: 40rpx;
		position: relative;
	}

	.product-con .footer .item .iconfont.icon-gouwuche1 .num {
		color: #fff;
		position: absolute;
		font-size: 18rpx;
		padding: 2rpx 8rpx 3rpx;
		border-radius: 200rpx;
		top: -10rpx;
		right: -10rpx;
	}

	.product-con .footer .bnt {
		width: 444rpx;
		height: 76rpx;
	}

	.product-con .footer .bnt .bnts {
		width: 222rpx;
		text-align: center;
		line-height: 76rpx;
		color: #fff;
		font-size: 28rpx;
	}

	.product-con .footer .bnt .joinCart {
		border-radius: 50rpx 0 0 50rpx;
		background-image: linear-gradient(to right, #fea10f 0%, #fa8013 100%);
	}

	.product-con .footer .bnt .buy {
		border-radius: 0 50rpx 50rpx 0;
		background-image: linear-gradient(to right, #fa6514 0%, #e93323 100%);
	}

	.product-con .footer .bnt .joinCart[disabled] {
		background: #bbb;
	}

	.product-con .footer .bnt .buy[disabled] {
		background: #bbb;
	}

	.product-con .superior {
		background-color: #fff;
		margin-top: 20rpx;
		padding-bottom: 10rpx;
	}

	.product-con .superior .title {
		height: 98rpx;
	}

	.product-con .superior .title image {
		width: 30rpx;
		height: 30rpx;
	}

	.product-con .superior .title .titleTxt {
		margin: 0 20rpx;
		font-size: 30rpx;
		background-image: linear-gradient(to right, #f57a37 0%, #f21b07 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
	}

	.product-con .superior .slider-banner {
		width: 690rpx;
		margin: 0 auto;
		position: relative;
	}

	.product-con .superior .slider-banner swiper {
		height: 100%;
		width: 100%;
	}

	.product-con .superior .slider-banner swiper-item {
		height: 100%;
	}

	.product-con .superior .slider-banner .list {
		width: 100%;
	}

	.product-con .superior .slider-banner .list .item {
		width: 215rpx;
		margin: 0 22rpx 30rpx 0;
		font-size: 26rpx;
	}

	.product-con .superior .slider-banner .list .item:nth-of-type(3n) {
		margin-right: 0;
	}

	.product-con .superior .slider-banner .list .item .pictrue {
		position: relative;
		width: 100%;
		height: 215rpx;
	}

	.product-con .superior .slider-banner .list .item .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 6rpx;
	}

	.product-con .superior .slider-banner .list .item .name {
		color: #282828;
		margin-top: 12rpx;
	}

	.product-con .superior .slider-banner .swiper-pagination-bullet {
		background-color: #999;
	}

	.product-con .superior .slider-banner .swiper-pagination-bullet-active {
		background-color: #e93323;
	}

	button {
		padding: 0;
		margin: 0;
		line-height: normal;
		background-color: #fff;
	}

	button::after {
		border: 0;
	}

	action-sheet-item {
		padding: 0;
		height: 240rpx;
		align-items: center;
		display: flex;
	}

	.contact {
		font-size: 16px;
		width: 50%;
		background-color: #fff;
		padding: 8rpx 0;
		border-radius: 0;
		margin: 0;
		line-height: 2;
	}

	.contact::after {
		border: none;
	}

	.action-sheet {
		font-size: 17px;
		line-height: 1.8;
		width: 50%;
		position: absolute;
		top: 0;
		right: 0;
		padding: 25rpx 0;
	}

	.canvas {
		z-index: 300;
		width: 750px;
		height: 1190px;
	}

	.poster-pop {
		width: 450rpx;
		height: 714rpx;
		position: fixed;
		left: 50%;
		transform: translateX(-50%);
		z-index: 399;
		top: 50%;
		margin-top: -357rpx;
	}

	.poster-pop image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.poster-pop .close {
		width: 46rpx;
		height: 75rpx;
		position: fixed;
		right: 0;
		top: -73rpx;
		display: block;
	}

	.poster-pop .save-poster {
		background-color: #df2d0a;
		font-size: ：22rpx;
		color: #fff;
		text-align: center;
		height: 76rpx;
		line-height: 76rpx;
		width: 100%;
	}

	.poster-pop .keep {
		color: #fff;
		text-align: center;
		font-size: 25rpx;
		margin-top: 10rpx;
	}

	.mask {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0, 0, 0, 0.6);
		z-index: 9;
	}

	.pro-wrapper .iconn {
		background-image: url('data:image/png;base64,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');
		width: 100rpx;
		height: 100rpx;
		background-repeat: no-repeat;
		background-size: 100% 100%;
		margin: 0 auto;
	}

	.pro-wrapper .iconn.iconn1 {
		background-image: url('data:image/png;base64,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');
	}

	.pictrue_log {
		width: 80upx;
		height: 40upx;
		border-radius: 6upx 0 12upx 0;
		line-height: 40upx;
		font-size: 24upx;
	}

	.pictrue_log_class {
		z-index: 3;
		background: -webkit-gradient(linear, left top, right top, from(rgba(246, 122, 56, 1)), to(rgba(241, 27, 9, 1)));
		background: linear-gradient(90deg, rgba(246, 122, 56, 1) 0%, rgba(241, 27, 9, 1) 100%);
		opacity: 1;
		position: absolute;
		top: 0;
		left: 0;
		color: #fff;
		text-align: center;

	}

	.navbar .header {
		height: 96rpx;
		font-size: 30rpx;
		color: #050505;
		background-color: #fff;
		/* #ifdef MP */
		padding-right: 95rpx;
		/* #endif */
	}

	.navbar .header .item {
		position: relative;
		margin: 0 25rpx;
	}

	.navbar .header .item.on:before {
		position: absolute;
		width: 60rpx;
		height: 5rpx;
		background-repeat: no-repeat;
		content: "";
		background-image: linear-gradient(to right, #ff3366 0%, #ff6533 100%);
		bottom: -10rpx;
		left: 50%;
		margin-left: -28rpx;
	}

	.navbar {
		position: fixed;
		background-color: #fff;
		top: 0;
		left: 0;
		z-index: 99;
		width: 100%;
	}

	.navbar .navbarH {
		position: relative;
	}

	.navbar .navbarH .navbarCon {
		position: absolute;
		bottom: 0;
		height: 100rpx;
		width: 100%;
	}

	.share-box {
		z-index: 1000;
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;

		image {
			width: 100%;
			height: 100%;
		}
	}

	.store-wrapper {
		margin-top: 20rpx;
		background-color: #fff;

		.store-hd {
			padding: 30rpx;
			border-bottom: 1px solid #F5F5F5;

			.store-info {
				position: relative;
				display: flex;

				.logo {
					width: 86rpx;
					height: 86rpx;

					image {
						width: 86rpx;
						height: 86rpx;
						border-radius: 6rpx;
					}
				}

				.info {
					display: flex;
					flex-direction: column;
					justify-content: center;
					margin-left: 20rpx;

					.name {
						font-size: 28rpx;
						color: #282828;
						font-weight: bold;
					}

					.txt {
						margin-top: 8rpx;
						color: #666666;
						font-size: 22rpx;
					}
				}

				.link {
					position: absolute;
					right: 0;
					top: 50%;
					transform: translateY(-50%);
					display: flex;
					align-items: center;
					justify-content: center;
					width: 114rpx;
					height: 50rpx;
					background: linear-gradient(-90deg, $bg-end 0%, $bg-star 100%);
					border-radius: 25rpx;
					color: #fff;
				}
			}

			.score-wrapper {
				display: flex;
				justify-content: space-between;
				margin-top: 30rpx;

				.item {
					color: #948D8C;
					font-size: 26rpx;

					text {
						margin-left: 10rpx;
						color: $theme-color;
					}
				}
			}
		}

		.con-box {
			padding: 20rpx 30rpx 30rpx;

			.title {
				font-size: 28rpx;
				color: #282828;
			}

			.img-box {
				display: flex;
				margin-top: 20rpx;

				.img-item {
					width: 220rpx;
					margin-right: 15rpx;

					image {
						width: 220rpx;
						height: 220rpx;
						border-radius: 16rpx;
					}

					.txt {
						.title {
							font-size: 28rpx;
							color: #282828;
						}

						.price {
							color: $theme-color;
							font-size: 28rpx;

							text {
								font-size: 20rpx;
							}
						}
					}

					&:nth-child(3n) {
						margin-right: 0;
					}
				}
			}
		}
	}

	.sys-head {
		background: transparent;
	}

	.head-wrapper {
		z-index: 999;
		display: flex;
		align-items: center;
		position: fixed;
		left: 30rpx;
		top: 0;
		/* #ifdef MP */
		height: 43px;
		/* #endif */
		/* #ifdef H5 */
		height: 114rpx;
		/* #endif */
	}

	.head-menu {
		display: flex;
		align-items: center;
		height: 54rpx;
		width: 140rpx;
		background: rgba(0, 0, 0, .25);
		border-radius: 27rpx;

		.iconfont {
			flex: 1;
			text-align: center;
			color: #fff;
			box-sizing: border-box;

			&.icon-xiangzuo {
				border-right: 1px solid #fff;
			}
		}
	}
</style>
