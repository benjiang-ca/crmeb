<template>
	<view>
		<view class='order-submission'>
			<view class="allAddress" @click="onAddress">
				<view class='address acea-row row-between-wrapper' v-if='shippingType == 0 '>
					<view class='addressCon' v-if="addressInfo.real_name">
						<view class='name'>{{addressInfo.real_name}}
							<text class='phone'>{{addressInfo.phone}}</text>
						</view>
						<view>
							<text class='default font-color' v-if="addressInfo.is_default">[默认]</text>{{addressInfo.province}}{{addressInfo.city}}{{addressInfo.district}}{{addressInfo.detail}}</view>
						<!-- <view class='setaddress'>设置收货地址</view> -->
					</view>
					<view class='addressCon' v-else>
						<view class='setaddress'>设置收货地址</view>
					</view>
					<view class='iconfont icon-jiantou'></view>
				</view>
				<view class='line'>
					<image src='/static/images/line.jpg'></image>
				</view>
			</view>
			<!-- 商品信息 -->
			<view class="storeinfo-wrapper">
				<view class="store-item" v-for="(item,index) in cartInfo" :key="index">
					<view class="store-title">
						<text class="iconfont icon-shangjiadingdan"></text>
						<view class="txt" @click="goStore(item.mer_id)">{{item.mer_name}}</view>
						<text class="iconfont icon-xiangyou"></text>
					</view>
					<view class="product-item" v-for="(goods,j) in item.list" :key="j">
						<view class="img-box">
							<image :src="goods.product.image"></image>
						</view>
						<view class="content">
							<view class="name line1">{{goods.product.store_name}}</view>
							<view class="label">{{goods.productAttr.sku}}</view>
							<view class="price">
								￥{{goods.productAttr.price}}
								<text>X{{goods.cart_num}}</text>
							</view>
							<view class="err-txt" v-if="goods.undelivered && addressInfo.real_name">
								<text class="iconfont icon-zhuyi-copy"></text>
								<view class="txt">此商品不支持该区域配送</view>
							</view>
						</view>
					</view>
					<view class="wrapper">
						<view class="boxs" v-if="item.take.mer_take_status != 0">
							<view class='item acea-row row-between-wrapper' @tap='openShowBox(item,index)'>
								<view>配送方式</view>
								<view class='discount'>
									{{item.isTake==0?'快递配送':'到店自提'}}
									<text class='iconfont icon-jiantou'></text>
								</view>
							</view>
							<view class="store-address" v-if="item.isTake" @click="goMap(item)">
								<view class="name">{{item.take.mer_take_name}}</view>
								<view class="info">{{item.take.mer_take_address}}</view>
							</view>
						</view>
						<view class='item acea-row row-between-wrapper' v-if='shippingType==0 && item.isTake == 0'>
							<view>快递费用</view>
							<view class='discount' v-if='item.order.postage_price > 0'>+￥{{item.order.postage_price}}</view>
							<view class='discount' v-else>免运费</view>
						</view>
						<view class='item acea-row row-between-wrapper' v-if="!pinkId && !BargainId && !combinationId && !seckillId">
							<view>优惠券</view>
							<block v-if="item.coupon.length>0">
								<view class='discount' @tap='couponTap(item,index)'>优惠￥{{item.order.coupon_price}}
									<text class='iconfont icon-jiantou'></text>
								</view>
							</block>
							<block v-else>
								<view class='discount'>暂无优惠券</view>
							</block>
						</view>
						<view class='item acea-row row-between-wrapper' v-if="textareaStatus">
							<view>备注信息</view>
							<input v-if="coupon.status===false" placeholder-class='placeholder' @input='bindHideKeyboard' value="" name="mark"
							 placeholder='选填备注信息' v-model="msgObj[item.mer_id]"></input>
						</view>
						<view class="total">
							共{{item.order.total_num}}件 小计 
							<view class="price" v-if="item.isTake == 0"><text>￥</text>{{item.order.pay_price}}</view>
							<view class="price" v-if="item.isTake == 1"><text>￥</text>{{item.order.org_price}}</view>
						</view>
					</view>
				</view>
			</view>
			<view class='wrapper'>
				<view class='item'>
					<view>支付方式</view>
					<view class='list'>
						<!-- #ifdef H5 -->
						<view class='payItem acea-row row-middle' :class='active==index ?"on":""' @tap='payItem(index)' v-for="(item,index) in cartArr" :key='index' v-if="item.payStatus==1">
							<view class='name acea-row row-center-wrapper'>
								<view class='iconfont animated' :class='(item.icon) + " " + (animated==true&&active==index ?"bounceIn":"")'></view>{{item.name}}
							</view>
							<view class='tip'>
								{{item.title}}
								<block v-if="item.value == 'balance'">
									{{userInfo.now_money}}
								</block>
							</view>
						</view>
						<!-- #endif -->
						<!-- #ifdef MP || APP-PLUS -->
						<view class='payItem acea-row row-middle' :class='active==index ?"on":""' @tap='payItem(index)' v-for="(item,index) in cartArr"
						 :key='index' v-if="item.payStatus==1">
							<view class='name acea-row row-center-wrapper'>
								<view class='iconfont animated' :class='(item.icon) + " " + (animated==true&&active==index ?"bounceIn":"")'></view>{{item.name}}
							</view>
							<view class='tip'>
								{{item.title}}
								<block v-if="item.value == 'balance'">
									{{userInfo.now_money}}
								</block>
							</view>
						</view>
						<!-- #endif -->
					</view>
				</view>
			</view>
			<view class='moneyList'>
				<view class='item acea-row row-between-wrapper'>
					<view>商品总价：</view>
					<view class='money'>￥{{proPrice}}</view>
				</view>
				<view class='item acea-row row-between-wrapper' v-if="coupon_price > 0">
					<view>优惠券抵扣：</view>
					<view class='money'>-￥{{coupon_price}}</view>
				</view>
				<view class='item acea-row row-between-wrapper' v-if="integral_price > 0">
					<view>积分抵扣：</view>
					<view class='money'>-￥{{integral_price}}</view>
				</view>
				<view class='item acea-row row-between-wrapper' v-if="priceGroup.storePostage > 0">
					<view>运费：</view>
					<view class='money'>+￥{{priceGroup.storePostage}}</view>
				</view>
			</view>
			<view style='height:120rpx;'></view>
			<view class='footer acea-row row-between-wrapper'>
				<view>合计:
					<text class='font-color'>￥{{totalPrice || 0}}</text>
				</view>
				<view class='settlement' :class='addressInfo.real_name ? "" : "disabled"' style='z-index:100' @tap="SubOrder">{{addressInfo.real_name ? '立即结算':'选择地址'}}</view>
			</view>
		</view>
		<block v-if="coupon.status">
			<couponListWindow :coupon='coupon' @ChangCouponsClone="ChangCouponsClone" :openType='openType' :cartId='cartId'
			 @ChangCoupons="ChangCoupons" :subCoupon='subCoupon'></couponListWindow>
		</block>
		<addressWindow ref="addressWindow" @changeTextareaStatus="changeTextareaStatus" :address='address' :pagesUrl="pagesUrl"
		 @OnChangeAddress="OnChangeAddress" @changeClose="changeClose"></addressWindow>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
		<block v-if="isShowBox">
			<checkDelivery :isShowBox="isShowBox" :activeObj="activeObj" @close="boxClose" @confirmBtn="getData"></checkDelivery>
		</block>
	</view>
</template>
<script>
	let app = getApp();
	import {
		orderConfirm,
		getCouponsOrderPrice,
		orderCreate
	} from '@/api/order.js';
	import {
		getAddressDefault,
		getAddressDetail,
		getAddressList,
		getUserInfo
	} from '@/api/user.js';
	import {
		openPaySubscribe
	} from '@/utils/SubscribeMessage.js';
	import {
		storeListApi
	} from '@/api/store.js';
	import {
		CACHE_LONGITUDE,
		CACHE_LATITUDE
	} from '@/config/cache.js';
	import couponListWindow from '@/components/checkCoupon';
	import addressWindow from '@/components/addressWindow';
	import orderGoods from '@/components/orderGoods';
	import checkDelivery from '@/components/checkDelivery/index.vue'
	import {
		toLogin
	} from '@/libs/login.js';
	import {
		mapGetters
	} from "vuex";
	// #ifdef MP
	import authorize from '@/components/Authorize';
	// #endif
	export default {
		components: {
			couponListWindow,
			addressWindow,
			orderGoods,
			checkDelivery,
			// #ifdef MP
			authorize
			// #endif
		},
		data() {
			return {
				// 备注
				msgObj: {

				},
				textareaStatus: true,
				//支付方式
				cartArr: [{
						"name": "微信支付",
						"icon": "icon-weixin2",
						value: 'weixin',
						title: '微信快捷支付',
						payStatus: 1,
					},
					{
						"name": "余额支付",
						"icon": "icon-icon-test",
						value: 'balance',
						title: '可用余额:',
						payStatus: app.globalData.yue_pay_status,
					},
					{
						"name": "线下支付",
						"icon": "icon-yinhangqia",
						value: 'offline',
						title: '线下支付',
						payStatus: 2,
					},
				],
				payType: 'weixin', //支付方式
				openType: 1, //优惠券打开方式 1=使用
				active: 0, //支付方式切换
				coupon: {
					status: false,
					list: [],
					statusTile: '立即使用'
				}, //优惠券组件
				address: {
					address: false
				}, //地址组件
				addressInfo: {}, //地址信息
				pinkId: 0, //拼团id
				addressId: 0, //地址id
				couponId: 0, //优惠券id
				cartId: '', //购物车id
				BargainId: 0,
				combinationId: 0,
				seckillId: 0,
				userInfo: {}, //用户信息
				mark: '', //备注信息
				couponTitle: '请选择', //优惠券
				coupon_price: 0, //优惠券抵扣金额
				useIntegral: false, //是否使用积分
				integral_price: 0, //积分抵扣金额
				integral: 0,
				ChangePrice: 0, //使用积分抵扣变动后的金额
				formIds: [], //收集formid
				status: 0,
				is_address: false,
				toPay: false, //修复进入支付时页面隐藏从新刷新页面
				shippingType: 0,
				system_store: {},
				storePostage: 0,
				contacts: '',
				contactsTel: '',
				mydata: {},
				storeList: [],
				store_self_mention: 0,
				cartInfo: [],
				priceGroup: {},
				animated: false,
				totalPrice: 0,
				integralRatio: "0",
				pagesUrl: "",
				orderKey: "",
				// usableCoupon: {},
				offlinePostage: "",
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				from: '',
				orderStatus: '', //是否有地址
				couponIndex: 0, //选择商铺优惠券索引
				subCoupon: {}, //提交订单使用的优惠券
				proPrice:0 ,//商品总价
				isShowBox:false,
				activeObj:{}, //选中店铺信息
				activeIndex:'' // 选中店铺索引
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad: function(options) {
			this.seckillId = options.seckillId
			// #ifdef H5
			this.from = this.$wechat.isWeixin() ? 'weixin' : 'h5'
			// #endif
			// #ifdef MP
			this.from = 'routine'
			// #endif
			if (!options.cartId) return this.$util.Tips({
				title: '请选择要购买的商品'
			}, {
				tab: 3,
				url: 1
			});
			this.couponId = options.couponId || 0;
			this.pinkId = options.pinkid ? parseInt(options.pinkid) : 0;
			this.addressId = options.addressId || 0;
			this.cartId = options.cartId;
			this.is_address = options.is_address ? true : false;
			if (this.isLogin) {
				// this.getaddressInfo();
				// //调用子页面方法授权后执行获取地址列表
				// this.$nextTick(function() {
				// 	this.$refs.addressWindow.getAddressList();
				// })
			} else {
				// #ifdef H5 || APP-PLUS
				toLogin();
				// #endif 
				// #ifdef MP
				this.isAuto = true;
				this.$set(this, 'isShowAuth', true);
				// #endif
			}
			if(this.payType == 'weixin'){
				this.payType = this.from
			}			
			let _this = this
			this.textareaStatus = true;
			if (this.isLogin && this.toPay == false) {
				this.getaddressInfo();
				this.$nextTick(function() {
					this.$refs.addressWindow.getAddressList();
					this.getUserInfo()
				})				
			}
		},
		/**
		 * 生命周期函数--监听页面显示
		 */
		onShow: function() {
			uni.$on("handClick", res => {
				if (res) {
					_this.system_store = res.address
				}
				// 清除监听
				uni.$off('handClick');
			})
			uni.$on("updataAddress",()=>{
				this.getaddressInfo();
				this.$nextTick(function() {
					this.$refs.addressWindow.getAddressList();
					this.getUserInfo()
				})				
			})
			// let pages = getCurrentPages();
			// let currPage = pages[pages.length - 1]; //当前页面
			// if (currPage.data.storeItem) {
			// 	let json = currPage.data.storeItem;
			// 	this.$set(this, 'system_store', json);
			// }
		},
		/**
		 * 生命周期函数--监听页面隐藏
		 */
		// onHide: function() {
		// 	console.log(999);
		// 	this.isClose = true
		// },
		methods: {
			// 获取个人信息
			getUserInfo(){
				getUserInfo().then(res=>{
					// console.log(res)
					this.userInfo = res.data
				})
			},
			
			/**
			 * 授权回调事件
			 * 
			 */
			onLoadFun: function() {
				this.isShowAuth = false;
				this.getaddressInfo();
				this.getUserInfo();
				//调用子页面方法授权后执行获取地址列表
				// this.$scope.selectComponent('#address-window').getAddressList();
			},
			/**
			 * 获取门店列表数据
			 */
			getList: function() {
				let longitude = uni.getStorageSync("CACHE_LONGITUDE"); //经度
				let latitude = uni.getStorageSync("CACHE_LATITUDE"); //纬度
				let data = {
					latitude: latitude, //纬度
					longitude: longitude, //经度
					page: 1,
					limit: 10
				}
				storeListApi(data).then(res => {
					console.log(res)
					let list = res.data.list.list || [];
					this.$set(this, 'storeList', list);
					this.$set(this, 'system_store', list[0]);
				}).catch(err => {})
			},
			// 关闭地址弹窗；
			changeClose: function() {
				this.$set(this.address, 'address', false);
			},
			/*
			 * 跳转门店列表
			 */
			showStoreList: function() {

				let _this = this
				if (this.storeList.length > 0) {
					uni.navigateTo({
						url: '/pages/users/goods_details_store/index'
					})
				}
			},
			// 进店
			goStore: function(id) {
				uni.navigateTo({
					url: `/pages/store/home/index?id=${id}`
				})
			},
			computedPrice: function() {
				let shippingType = this.shippingType;
				/*postOrderComputed(this.orderKey, {
					addressId: this.addressId,
					useIntegral: this.useIntegral ? 1 : 0,
					couponId: this.couponId,
					shipping_type: parseInt(shippingType) + 1,
					payType: this.payType
				}).then(res => {
					let result = res.data.result;
					if (result) {
						this.totalPrice = result.pay_price;
						this.integral_price = result.deduction_price;
						this.coupon_price = result.coupon_price;
						this.integral = this.useIntegral ? result.SurplusIntegral : this.userInfo.integral;
						this.$set(this.priceGroup, 'storePostage', shippingType == 1 ? 0 : result.pay_postage);
					}
				})*/
			},
			addressType: function(e) {
				let index = e;
				this.shippingType = parseInt(index);
				this.computedPrice();
				if (index == 1) this.getList();
			},
			bindPickerChange: function(e) {
				let value = e.detail.value;
				this.shippingType = value;
				this.computedPrice();
			},
			ChangCouponsClone: function() {
				this.$set(this.coupon, 'status', false);
			},
			changeTextareaStatus: function() {
				for (let i = 0, len = this.coupon.list.length; i < len; i++) {
					this.coupon.list[i].use_title = '';
					this.coupon.list[i].is_use = 0;
				}
				this.textareaStatus = true;
				this.status = 0;
				this.$set(this.coupon, 'list', this.coupon.list);
			},
			/**
			 * 处理点击优惠券后的事件
			 * 
			 */
			ChangCoupons: function(data) {
				let tempTotal = 0
				this.cartInfo.forEach(item => {
					console.log(item.order.pay_price,'item.order.pay_price')
					tempTotal = this.$util.$h.Add(tempTotal, item.order.pay_price)
				})
				this.totalPrice = tempTotal
				// this.subCoupon = data
				this.$set(this.coupon, 'status', false);
			},
			/**
			 * 使用积分抵扣
			 */
			ChangeIntegral: function() {
				this.useIntegral = !this.useIntegral;
				this.computedPrice();
			},
			/**
			 * 选择地址后改变事件
			 * @param object e
			 */
			OnChangeAddress: function(e) {
				console.log(e, 'zouni')
				this.textareaStatus = true;
				this.addressId = e;
				this.address.address = false;
				this.getaddressInfo();
				this.computedPrice();
			},
			bindHideKeyboard: function(e) {
				this.mark = e.detail.value;
			},
			/**
			 * 获取当前订单详细信息
			 * 
			 */
			getConfirm: function(address_id) {
				console.log(address_id,'address_id')
				let that = this;
				let tempObj = {

				}
				orderConfirm({
					cart_id: this.cartId.split(","),
					address_id: address_id
				}).then(res => {
					// 默认选中
					res.data.order.forEach(el => {
						el.isTake = 0
						tempObj = this.subCoupon[el.mer_id] = {}
						tempObj.product = []
						tempObj.store = ''
						tempObj.product = el.order.use_coupon_product
						
						el.coupon.forEach(coupon => {
							if (coupon.coupon.type == 0 && coupon.checked) {
								tempObj.store = coupon.coupon_user_id
							}
						})
					})
					that.$set(that, 'cartInfo', res.data.order);
					that.totalPrice = res.data.order_price
					that.orderStatus = res.data.status
					that.proPrice = res.data.total_price
				}).catch(err => {
					return this.$util.Tips({
						title: err
					}, {
						tab: 3,
						url: 1
					});
				});
			},
			/*
			 * 提取砍价和拼团id
			 */
			getBargainId: function() {
				let that = this;
				let cartINfo = that.cartInfo;
				let BargainId = 0;
				let combinationId = 0;
				cartINfo.forEach(function(value, index, cartINfo) {
					BargainId = cartINfo[index].bargain_id,
						combinationId = cartINfo[index].combination_id
				})
				that.$set(that, 'BargainId', parseInt(BargainId));
				that.$set(that, 'combinationId', parseInt(combinationId));
				if (that.cartArr.length == 3 && (BargainId || combinationId || that.seckillId)) {
					that.cartArr[2].payStatus = 0;
					that.$set(that, 'cartArr', that.cartArr);
				}
			},
			/**
			 * 获取当前金额可用优惠券
			 * 
			 */
			getCouponList: function() {
				let that = this;
				let data = {
					cartId: this.cartId
				}
				getCouponsOrderPrice(this.totalPrice, data).then(res => {
					that.$set(that.coupon, 'list', res.data);
					that.openType = 1;
				});
			},
			/*
			 * 获取默认收货地址或者获取某条地址信息
			 */
			getaddressInfo: function() {
				let that = this;
				if (that.addressId) {
					getAddressDetail(that.addressId).then(res => {
						res.data.is_default = parseInt(res.data.is_default);
						that.addressInfo = res.data || {};
						that.addressId = res.data.address_id || 0;
						that.address.addressId = res.data.address_id || 0;
						this.getConfirm(that.addressId);
					})
				} else {
					getAddressList().then(res => {
						that.addressInfo = res.data.list.length>0? res.data.list[0] : {};
						that.addressId = res.data.list.length>0?res.data.list[0].address_id : 0;
						that.address.addressId = res.data.list.length>0? res.data.list[0].address_id : 0;
						this.getConfirm(that.addressId);
					})
				}

			},
			payItem: function(e) {
				let that = this;
				let active = e;
				that.active = active;
				that.animated = true;
				that.payType = that.cartArr[active].value;
				if(that.payType == 'weixin'){
					that.payType = that.from
				}
				
				
				// that.computedPrice();
				// setTimeout(function() {
				// 	that.car();
				// }, 500);
			},
			couponTap: function(item, index) {
				this.coupon = item
				this.$set(this.coupon, 'status', true)
				this.couponIndex = index
				// this.coupon.status = true;

			},
			car: function() {
				let that = this;
				that.animated = false;
			},
			onAddress: function() {
				let that = this;
				that.textareaStatus = false;
				that.address.address = true;
				that.pagesUrl = '/pages/users/user_address_list/index?cartId=' + this.cartId + '&pinkId=' + this.pinkId +
					'&couponId=' +this.couponId;
					
			},
			realName: function(e) {
				this.contacts = e.detail.value;
			},
			phone: function(e) {
				this.contactsTel = e.detail.value;
			},
			payment: function(data) {
				console.log(data,'data')
				let that = this;
				orderCreate(data).then(res => {
					let status = res.data.status,
						orderId = res.data.result.order_id,
						jsConfig = res.data.result.config,
						goPages = '/pages/order_pay_status/index?order_id=' + orderId + '&msg=' + res.message;
					switch (status) {			
						case 'ORDER_EXIST':
						case 'EXTEND_ORDER':
						case 'PAY_ERROR':
						case 'error':
							uni.hideLoading();
							return that.$util.Tips({
								title: res.msg
							}, {
								tab: 5,
								url: goPages
							});
							break;
						case 'success':
							uni.hideLoading();
							if (that.BargainId || that.combinationId || that.pinkId || that.seckillId)
								return that.$util.Tips({
									title: res.msg,
									icon: 'success'
								}, {
									tab: 4,
									url: goPages
								});
							return that.$util.Tips({
								title: res.msg,
								icon: 'success'
							}, {
								tab: 5,
								url: goPages
							});
							break;
						// #ifndef MP
						case "wechat":
						case "weixin":
							jsConfig.timeStamp = jsConfig.timestamp;
							this.$wechat.pay(jsConfig).then(res => {	
								console.log('测试支付数据无效的success：'+res.data)
								return that.$util.Tips({
									title: res.message,
									icon: 'success'
								}, {
									tab: 5,
									url: goPages
								});
							}).catch(res => {
								console.log('测试支付数据无效的catch：'+res.data)
								if (res.errMsg == 'chooseWXPay:cancel') return that.$util.Tips({
									title: '取消支付'
								}, {
									tab: 5,
									url: goPages + '&status=0'
								});
							})
							break;
						// #endif
						// #ifdef MP
						case "routine":
							jsConfig.timeStamp = jsConfig.timestamp;
							that.toPay = true;
							uni.requestPayment({
								...jsConfig,
								success: function(res) {
									uni.hideLoading();
									if (that.BargainId || that.combinationId || that.pinkId || that.seckillId)
										return that.$util.Tips({
											title: '支付成功',
											icon: 'success'
										}, {
											tab: 4,
											url: goPages
										});
									return that.$util.Tips({
										title: '支付成功',
										icon: 'success'
									}, {
										tab: 5,
										url: goPages
									});
								},
								fail: function(e) {
									uni.hideLoading();
									return that.$util.Tips({
										title: '取消支付'
									}, {
										tab: 5,
										url: goPages + '&status=0'
									});
								},
								complete: function(e) {
									uni.hideLoading();
									//关闭当前页面跳转至订单状态
									if (res.errMsg == 'requestPayment:cancel') return that.$util.Tips({
										title: '取消支付'
									}, {
										tab: 5,
										url: goPages + '&status=0'
									});
								},
							})
							break;
						// #endif
						case "balance":
							uni.hideLoading();
							//余额不足
							return that.$util.Tips({
								title: res.msg
							}, {
								tab: 5,
								url: goPages + '&status=1'
							});
							break;
						// #ifdef H5
						case 'h5':
							let host = window.location.protocol+"//"+window.location.host;
							let url = `${host}/pages/order_pay_status/index?order_id=${orderId}`
							let eUrl = encodeURIComponent(url)
							let locations = `${jsConfig.mweb_url}&redirect_url=${eUrl}` 
							setTimeout(() => {
								location.href = locations;
							}, 100);
							break;
						// #endif		
					}
				}).catch(err => {
					uni.hideLoading();
					return that.$util.Tips({
						title: err
					});
				});
			},
			
			isEmojiCharacter: function(substring){
			    if(substring){
			        // var reg = new RegExp("[~#^$@%&!?%*]", 'g');
			        // if (substring.match(reg)) {
			        //     return true;
			        // }
			        for ( var i = 0; i < substring.length; i++) {
			            var hs = substring.charCodeAt(i);
			            if (0xd800 <= hs && hs <= 0xdbff) {
			                if (substring.length > 1) {
			                    var ls = substring.charCodeAt(i + 1);
			                    var uc = ((hs - 0xd800) * 0x400) + (ls - 0xdc00) + 0x10000;
			                    if (0x1d000 <= uc && uc <= 0x1f77f) {
			                        return true;
			                    }
			                }
			            } else if (substring.length > 1) {
			                var ls = substring.charCodeAt(i + 1);
			                if (ls == 0x20e3) {
			                    return true;
			                }
			            } else {
			                if (0x2100 <= hs && hs <= 0x27ff) {
			                    return true;
			                } else if (0x2B05 <= hs && hs <= 0x2b07) {
			                    return true;
			                } else if (0x2934 <= hs && hs <= 0x2935) {
			                    return true;
			                } else if (0x3297 <= hs && hs <= 0x3299) {
			                    return true;
			                } else if (hs == 0xa9 || hs == 0xae || hs == 0x303d || hs == 0x3030
			                    || hs == 0x2b55 || hs == 0x2b1c || hs == 0x2b1b
			                    || hs == 0x2b50) {
			                    return true;
			                }
			            }
			        }
			    }
			},
			SubOrder: function(e) {

				let that = this,
					data = {};
				if (!that.payType) return that.$util.Tips({
					title: '请选择支付方式'
				});
				if (!that.addressId && !that.shippingType) return that.$util.Tips({
					title: '请选择收货地址'
				});
				if (that.orderStatus != 'finish') {
					return that.$util.Tips({
						title: '请选择收货地址'
					});
				}
				if (that.orderStatus == 'noDeliver') {
					return that.$util.Tips({
						title: '暂不发货'
					});
				}
				data = {
					cart_id: this.cartId.split(","),
					address_id: this.addressId,
					coupon: this.subCoupon,
					pay_type: this.payType,
					mark: this.msgObj,
					take:[]
				};
				if(data.mark && this.isEmojiCharacter(data.mark[Object.keys(data.mark)[0]])){
					that.$util.Tips({
						title: '备注不允许输入表情！'
					});
					return;
				}
				this.cartInfo.map(el=>{
					if(el.isTake == 1){
						data.take.push(el.mer_id)
					}
				})
				console.log(data,'data')
				if (data.payType == 'balance' && parseFloat(that.userInfo.now_money) < parseFloat(that.totalPrice)) return that.$util.Tips({
					title: '余额不足！'
				});
				uni.showLoading({
					title: '订单支付中',
					mask:true
				});
				// #ifdef MP
				openPaySubscribe().then(() => {
					that.payment(data);
				});
				// #endif
				// #ifndef MP
				that.payment(data);
				// #endif
			},
			// 打开配送方式弹窗
			openShowBox(item,index){
				this.activeObj = item
				this.activeIndex = index
				this.isShowBox = true
			},
			boxClose(){
				this.isShowBox = false
			},
			getData(data){
				let self = this
				let freePostage = 0 //没有邮费总价
				let Postage = 0 //有邮费总价
				this.cartInfo[this.activeIndex] = data
				this.cartInfo.map(el=>{
					if(el.isTake == 1){
						freePostage = self.$util.$h.Add(freePostage,el.order.org_price) 
					}else{
						Postage = self.$util.$h.Add(Postage,el.order.pay_price)
					}
				})
				this.totalPrice = self.$util.$h.Add(freePostage,Postage)
				this.isShowBox = false
			},
			//查看内置地图
			goMap(item){
				uni.openLocation({
					latitude: item.take.mer_take_location[0],
					longitude: item.take.mer_take_location[1],
					success: function () {
					}
				});
			}
		}
	}
</script>

<style lang="scss" scoped>
	.order-submission .line {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 3rpx;
	}

	.order-submission .line image {
		width: 100%;
		height: 100%;
		display: block;
	}

	.order-submission .address {
		padding: 28rpx 30rpx;
		background-color: #fff;
		box-sizing: border-box;
	}

	.order-submission .address .addressCon {
		width: 610rpx;
		font-size: 26rpx;
		color: #666;
	}

	.order-submission .address .addressCon .name {
		font-size: 30rpx;
		color: #282828;
		font-weight: bold;
		margin-bottom: 10rpx;
	}

	.order-submission .address .addressCon .name .phone {
		margin-left: 50rpx;
	}

	.order-submission .address .addressCon .default {
		margin-right: 12rpx;
	}

	.order-submission .address .addressCon .setaddress {
		color: #333;
		font-size: 28rpx;
	}

	.order-submission .address .iconfont {
		font-size: 35rpx;
		color: #707070;
	}

	.order-submission .allAddress {
		width: 100%;
	}

	.order-submission .allAddress .nav {
		width: 710rpx;
		margin: 0 auto;
	}

	.order-submission .allAddress .nav .item {
		width: 355rpx;
	}

	.order-submission .allAddress .nav .item.on {
		position: relative;
		width: 250rpx;
	}

	.order-submission .allAddress .nav .item.on::before {
		position: absolute;
		bottom: 0;
		content: "快递配送";
		font-size: 28rpx;
		display: block;
		height: 0;
		width: 336rpx;
		border-width: 0 20rpx 80rpx 0;
		border-style: none solid solid;
		border-color: transparent transparent #fff;
		z-index: 2;
		border-radius: 7rpx 30rpx 0 0;
		text-align: center;
		line-height: 80rpx;
	}

	.order-submission .allAddress .nav .item:nth-of-type(2).on::before {
		content: "到店自提";
		border-width: 0 0 80rpx 20rpx;
		border-radius: 30rpx 7rpx 0 0;
	}

	.order-submission .allAddress .nav .item.on2 {
		position: relative;
	}

	.order-submission .allAddress .nav .item.on2::before {
		position: absolute;
		bottom: 0;
		content: "到店自提";
		font-size: 28rpx;
		display: block;
		height: 0;
		width: 400rpx;
		border-width: 0 0 60rpx 60rpx;
		border-style: none solid solid;
		border-color: transparent transparent #f7c1bd;
		border-radius: 40rpx 6rpx 0 0;
		text-align: center;
		line-height: 60rpx;
	}

	.order-submission .allAddress .nav .item:nth-of-type(1).on2::before {
		content: "快递配送";
		border-width: 0 60rpx 60rpx 0;
		border-radius: 6rpx 40rpx 0 0;
	}

	.order-submission .allAddress .address {
		height: 150rpx;
		margin: 0 auto;
	}

	.order-submission .allAddress .line {
		margin: 0 auto;
	}

	.order-submission .wrapper .item .discount .placeholder {
		color: #ccc;
	}

	.order-submission .wrapper {
		margin-bottom: 12rpx;
		background-color: #fff;
	}

	.order-submission .wrapper .item {
		padding: 27rpx 30rpx;
		font-size: 30rpx;
		color: #282828;
	}

	.order-submission .wrapper .item .discount {
		font-size: 30rpx;
		color: #999;
	}

	.order-submission .wrapper .item .discount .iconfont {
		color: #515151;
		font-size: 30rpx;
		margin-left: 15rpx;
	}

	.order-submission .wrapper .item .discount .num {
		font-size: 32rpx;
		margin-right: 20rpx;
	}

	.order-submission .wrapper .item .shipping {
		font-size: 30rpx;
		color: #999;
		position: relative;
		padding-right: 58rpx;
	}

	.order-submission .wrapper .item .shipping .iconfont {
		font-size: 35rpx;
		color: #707070;
		position: absolute;
		right: 0;
		top: 50%;
		transform: translateY(-50%);
		margin-left: 30rpx;
	}

	.order-submission .wrapper .item input {
		display: flex;
		flex: 1;
		height: 100%;
		margin-left: 20rpx;
	}

	.order-submission .wrapper .item .placeholder {
		color: #ccc;
	}

	.order-submission .wrapper .item .list {
		margin-top: 35rpx;
	}

	.order-submission .wrapper .item .list .payItem {
		border: 1px solid #eee;
		border-radius: 6rpx;
		height: 86rpx;
		width: 100%;
		box-sizing: border-box;
		margin-top: 20rpx;
		font-size: 28rpx;
		color: #282828;
	}

	.order-submission .wrapper .item .list .payItem.on {
		border-color: #fc5445;
		color: #e93323;
	}

	.order-submission .wrapper .item .list .payItem .name {
		width: 50%;
		text-align: center;
		border-right: 1px solid #eee;
	}

	.order-submission .wrapper .item .list .payItem .name .iconfont {
		width: 44rpx;
		height: 44rpx;
		border-radius: 50%;
		text-align: center;
		line-height: 44rpx;
		background-color: #fe960f;
		color: #fff;
		font-size: 30rpx;
		margin-right: 15rpx;
	}

	.order-submission .wrapper .item .list .payItem .name .iconfont.icon-weixin2 {
		background-color: #41b035;
	}

	.order-submission .wrapper .item .list .payItem .tip {
		width: 49%;
		text-align: center;
		font-size: 26rpx;
		color: #aaa;
	}

	.order-submission .moneyList {
		margin-top: 12rpx;
		background-color: #fff;
		padding: 30rpx;
	}

	.order-submission .moneyList .item {
		font-size: 28rpx;
		color: #282828;
	}

	.order-submission .moneyList .item~.item {
		margin-top: 20rpx;
	}

	.order-submission .moneyList .item .money {
		color: #868686;
	}

	.order-submission .footer {
		width: 100%;
		height: 100rpx;
		background-color: #fff;
		padding: 0 30rpx;
		font-size: 28rpx;
		color: #333;
		box-sizing: border-box;
		position: fixed;
		bottom: 0;
		left: 0;
	}

	.order-submission .footer .settlement {
		font-size: 30rpx;
		color: #fff;
		width: 240rpx;
		height: 70rpx;
		background-color: #e93323;
		border-radius: 50rpx;
		text-align: center;
		line-height: 70rpx;
		
		&.disabled{
			background-color: #cccccc;
		}
	}

	.footer .transparent {
		opacity: 0
	}

	.storeinfo-wrapper {

		.store-item {
			margin-top: 12rpx;
			background-color: #fff;

			.store-title {
				display: flex;
				align-items: center;
				padding: 28rpx 30rpx;

				.icon-shangjiadingdan {
					font-size: 32rpx;
				}

				.icon-xiangyou {
					font-size: 26rpx;
					color: #999;
				}

				.txt {
					margin: 0 8rpx;
				}
			}

			.product-item {
				display: flex;
				padding: 25rpx 30rpx;
				border-top: 1px solid #F0F0F0;

				.img-box {
					width: 130rpx;
					height: 130rpx;

					image {
						width: 130rpx;
						height: 130rpx;
						border-radius: 16rpx;
					}
				}

				.content {
					position: relative;
					width: 550rpx;
					margin-left: 30rpx;
					font-size: 28rpx;
					color: #282828;

					.label {
						margin-top: 10rpx;
						color: #868686;
						font-size: 20rpx;
					}

					.price {
						margin-top: 20rpx;
						color: $theme-color;

						text {
							margin-left: 10rpx;
							color: #999;
						}
					}

					.err-txt {
						display: flex;
						align-items: center;
						margin-top: 18rpx;
						color: $theme-color;

						.iconfont {
							margin-right: 10rpx;
						}
					}

					.step {
						position: absolute;
						right: 0;
						top: 100rpx;
						display: flex;
						align-items: center;

						.min {
							width: 50rpx;
							height: 40rpx;
							background-image: url('~@/static/images/min.png');
							background-size: 17rpx 17rpx;
							background-position: center;
							background-repeat: no-repeat;
						}

						.num-box {
							width: 68rpx;
							height: 40rpx;
							line-height: 40rpx;
							text-align: center;
							background: #F2F2F2;
							font-size: 23rpx;
						}

						.add {
							width: 50rpx;
							height: 40rpx;
							background-image: url('~@/static/images/add.png');
							background-size: 17rpx 17rpx;
							background-position: center;
							background-repeat: no-repeat;
						}
					}
				}
			}
		}
	}

	.total {
		display: flex;
		justify-content: flex-end;
		padding-right: 30rpx;
		padding-bottom: 30rpx;
		font-size: 26rpx;
		color: #282828;

		.price {
			margin-left: 10rpx;
			color: $theme-color;
			font-size: 28rpx;
			font-weight: bold;

			text {
				font-size: 20rpx;
			}
		}
	}
	.store-address{
		display: flex;
		padding: 30rpx 23rpx;
		margin: 0 30rpx;
		background: #F6F6F6;
		border-radius:8rpx;
		font-size: 24rpx;
		.name{
			width: 148rpx;
			margin-right: 20rpx;
		}
		.info{
			flex: 1;
		}
	}
</style>
