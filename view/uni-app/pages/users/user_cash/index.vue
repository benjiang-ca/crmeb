<template>
	<view>
		<view class='cash-withdrawal'>
			<view class='nav acea-row'>
				<view v-for="(item,index) in navList" :key="index" class='item font-color' @click="swichNav(index)">
					<view class='line bg-color' :class='currentTab==index ? "on":""'></view>
					<view class='iconfont' :class='item.icon+" "+(currentTab==index ? "on":"")'></view>
					<view>{{item.name}}</view>
				</view>
			</view>
			<view class='wrapper'>
				<view :hidden='currentTab != 0' class='list'>
					<form @submit="subCash" report-submit='true'>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>持卡人</view>
							<view class='input'><input placeholder='请输入持卡人姓名' placeholder-class='placeholder' name="real_name"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>卡号</view>
							<view class='input'><input type='number' placeholder='请填写卡号' placeholder-class='placeholder' name="bank_code"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper' v-if="array.length>0">
							<view class='name'>银行</view>
							<view class='input'>
								<picker @change="bindPickerChange" :value="index" :range="array" range-key="name">
									<text class='Bank'>{{array[index]["name"]}}</text>
									<text class='iconfont icon-qiepian38'></text>
								</picker>
							</view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>提现</view>
							<view class='input'><input :placeholder='"最低提现金额"+minPrice' placeholder-class='placeholder' name="extract_price"
								 type='digit' v-model="extract_price"></input></view>
						</view>
						<view class='tip'>
							当前可提现金额: <text class="price">￥{{userInfo.brokerage_price}},</text>冻结佣金：￥{{userInfo.lock_brokerage}}
						</view>
						<view class='tip'>
							说明: 每笔佣金的冻结期为{{userInfo.broken_day}}天，到期后可提现
						</view>
						<button formType="submit" :disabled="load" class='bnt bg-color'>提现</button>
					</form>
				</view>
				<view :hidden='currentTab != 1' class='list'>
					<form @submit="subCash" report-submit='true'>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>账号</view>
							<view class='input'><input placeholder='请填写您的微信账号' placeholder-class='placeholder' name="wechat"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>提现</view>
							<view class='input'><input :placeholder='"最低提现金额"+minPrice' placeholder-class='placeholder' name="extract_price"
								 type='digit' v-model="extract_price"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper uploadItem'>
							<view class='name'>收款码</view>
							<view class='input upload acea-row row-middle'>
								<view class='picture' v-for="(item,index) in pics" :key="index">
									<image :src='item'></image>
									<text class='iconfont icon-guanbi1' @click='DelPic(index)'></text>
								</view>
								<view class='picture acea-row row-center-wrapper row-column' @click='uploadpic' v-if="pics.length < 1">
									<text class='iconfont icon-icon25201'></text>
									<view>上传图片</view>
								</view>
							</view>
						</view>

						<view class='tip'>
							当前可提现金额: <text class="price">￥{{userInfo.brokerage_price}},</text>冻结佣金：￥{{userInfo.lock_brokerage}}
						</view>
						<view class='tip'>
							说明: 每笔佣金的冻结期为{{userInfo.broken_day}}天，到期后可提现
						</view>
						<button formType="submit" :disabled="load" class='bnt bg-color'>提现</button>
					</form>
				</view>
				<view :hidden='currentTab != 2' class='list'>
					<form @submit="subCash" report-submit='true'>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>账号</view>
							<view class='input'><input placeholder='请填写您的支付宝账号' placeholder-class='placeholder' name="alipay_code"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper'>
							<view class='name'>提现</view>
							<view class='input'><input :placeholder='"最低提现金额"+minPrice' placeholder-class='placeholder' name="extract_price"
								 type='digit' v-model="extract_price"></input></view>
						</view>
						<view class='item acea-row row-between-wrapper uploadItem'>
							<view class='name'>收款码</view>
							<view class='input upload acea-row row-middle'>
								<view class='picture' v-for="(item,index) in pics" :key="index">
									<image :src='item'></image>
									<text class='iconfont icon-guanbi1' @click='DelPic(index)'></text>
								</view>
								<view class='picture acea-row row-center-wrapper row-column' @click='uploadpic' v-if="pics.length < 1">
									<text class='iconfont icon-icon25201'></text>
									<view>上传图片</view>
								</view>
							</view>
						</view>
						<view class='tip'>
							当前可提现金额: <text class="price">￥{{userInfo.brokerage_price}},</text>冻结佣金：￥{{userInfo.lock_brokerage}}
						</view>
						<view class='tip' v-if="userInfo.broken_day>0">
							说明: 每笔佣金的冻结期为{{userInfo.broken_day}}天，到期后可提现
						</view>
						<button formType="submit" :disabled="load" class='bnt bg-color'>提现</button>
					</form>
				</view>
			</view>
		</view>
		<!-- #ifdef MP -->
		<authorize @onLoadFun="onLoadFun" :isAuto="isAuto" :isShowAuth="isShowAuth" @authColse="authColse"></authorize>
		<!-- #endif -->
	</view>
</template>

<script>
	import {
		extractCash,
		extractBank,
		getUserInfo,
		spreadInfo
	} from '@/api/user.js';
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
			// #ifdef MP
			authorize
			// #endif
		},
		data() {
			return {
				navList: [{
						'name': '银行卡',
						'icon': 'icon-yinhangqia'
					},
					{
						'name': '微信',
						'icon': 'icon-weixin2'
					},
					{
						'name': '支付宝',
						'icon': 'icon-icon34'
					}
				],
				currentTab: 0,
				extract_price: '',
				index: 0,
				array: [], //提现银行
				minPrice: 0.00, //最低提现金额
				userInfo: [],
				isClone: false,
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				loading: false,
				load: false,
				pics: [], //收款码
				extract_pic: ''
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad() {
			if (this.isLogin) {
				this.getUserInfo();
				this.getUserExtractBank();
			} else {
				// #ifdef H5 || APP-PLUS
				toLogin();
				// #endif 
				// #ifdef MP
				this.isAuto = true;
				this.$set(this, 'isShowAuth', true);
				// #endif
			}
		},
		methods: {
			onLoadFun: function() {
				this.isShowAuth = false;
				this.getUserInfo();
				// this.getUserExtractBank();
			},
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			getUserExtractBank: function() {
				let that = this;
				extractBank().then(res => {
					let array = res.data;
					that.$set(that, 'array', array);
				});
			},
			/**
			 * 获取个人用户信息
			 */
			getUserInfo: function() {
				let that = this;
				spreadInfo().then(res => {
					that.userInfo = res.data;
					that.minPrice = res.data.user_extract_min;
				});
			},
			swichNav: function(current) {
				this.currentTab = current;
			},
			bindPickerChange: function(e) {
				this.index = e.detail.value;
			},
			uploadpic: function() {
				let that = this;
				console.log('地方');
				that.$util.uploadImageOne('upload/image', function(res) {
					console.log(res);
					that.pics.push(res.data.path);
					that.$set(that, 'pics', that.pics);
					that.$set(that, 'extract_pic', that.pics[0])
				});

			},
			/**
			 * 删除图片
			 * 
			 */
			DelPic: function(index) {
				let that = this,
					pic = this.pics[index];
				that.pics.splice(index, 1);
				that.$set(that, 'pics', that.pics);
			},
			subCash: function(e) {
				let that = this,
					value = e.detail.value;
				if (that.currentTab == 0) { //银行卡
					if (value.real_name.length == 0) return this.$util.Tips({
						title: '请填写持卡人姓名'
					});
					if (value.bank_code.length == 0) return this.$util.Tips({
						title: '请填写卡号'
					});
					// if (that.index == 0) return this.$util.Tips({
					// 	title: "请选择银行"
					// });
					value.extract_type = 'bank';
					value.bank_address = that.array[that.index].name;
				} else if (that.currentTab == 1) { //微信
					value.extract_type = 'weixin';
					value.extract_pic = that.extract_pic
					if (value.wechat.length == 0) return this.$util.Tips({
						title: '请填写微信号'
					});
					if (value.extract_pic.length == 0) return this.$util.Tips({
						title: '请上传收款码'
					});

				} else if (that.currentTab == 2) { //支付宝
					value.extract_type = 'alipay';
					value.extract_pic = that.extract_pic
					if (value.alipay_code.length == 0) return this.$util.Tips({
						title: '请填写账号'
					});
					if (value.extract_pic.length == 0) return this.$util.Tips({
						title: '请上传收款码'
					});
				}
				if (value.extract_price.length == 0) return this.$util.Tips({
					title: '请填写提现金额'
				});
				if (Number(value.extract_price) < that.minPrice) return this.$util.Tips({
					title: '提现金额不能低于' + that.minPrice
				});
				value.extract_type = this.currentTab
				console.log(value, 'value')
				if(that.load) return ;
				that.load = true;
				extractCash(value).then(res => {
					that.getUserInfo();
					setTimeout(function(){
						that.load = false;
					}, 500);
					return that.$util.Tips({
						title: res.message,
						icon: 'success'
					});
				}).catch(err => {
					that.load = false;
					return that.$util.Tips({
						title: err
					});
				});
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff !important;
	}

	.cash-withdrawal .nav {
		height: 130rpx;
		box-shadow: 0 10rpx 10rpx #f8f8f8;
	}

	.cash-withdrawal .nav .item {
		font-size: 26rpx;
		flex: 1;
		text-align: center;

	}

	.cash-withdrawal .nav .item~.item {
		border-left: 1px solid #f0f0f0;
	}

	.cash-withdrawal .nav .item .iconfont {
		width: 40rpx;
		height: 40rpx;
		border-radius: 50%;
		border: 2rpx solid #e93323;
		text-align: center;
		line-height: 37rpx;
		margin: 0 auto 6rpx auto;
		font-size: 22rpx;
		box-sizing: border-box;
	}

	.cash-withdrawal .nav .item .iconfont.on {
		background-color: #e93323;
		color: #fff;
		border-color: #e93323;
	}

	.cash-withdrawal .nav .item .line {
		width: 2rpx;
		height: 20rpx;
		margin: 0 auto;
		transition: height 0.3s;
	}

	.cash-withdrawal .nav .item .line.on {
		height: 39rpx;
	}

	.cash-withdrawal .wrapper .list {
		padding: 0 30rpx;
	}

	.cash-withdrawal .wrapper .list .item {
		border-bottom: 1rpx solid #eee;
		height: 107rpx;
		font-size: 30rpx;
		color: #333;

		&.uploadItem {
			border-bottom: none;
			height: auto;

			.name {
				height: 107rpx;
				;
			}
		}
	}

	.picture {
		width: 70px;
		height: 70px;
		margin: 0 0 17px 0;
		position: relative;
		font-size: 11px;
		color: #bbb;
		border: 0.5px solid #ddd;
		box-sizing: border-box;
		margin-top: 40rpx;

		uni-image,image {
			width: 100%;
			height: 100%;
			border-radius: 1px;
		}

		.icon-guanbi1 {
			font-size: 22px;
			position: absolute;
			top: -10px;
			right: -10px;
			color: #fc4141;
		}
	}

	.cash-withdrawal .wrapper .list .item .name {
		width: 130rpx;
	}

	.cash-withdrawal .wrapper .list .item .input {
		width: 505rpx;
	}

	.cash-withdrawal .wrapper .list .item .input .placeholder {
		color: #bbb;
	}

	.cash-withdrawal .wrapper .list .tip {
		font-size: 26rpx;
		color: #999;
		margin-top: 25rpx;
	}

	.cash-withdrawal .wrapper .list .bnt {
		font-size: 32rpx;
		color: #fff;
		width: 690rpx;
		height: 90rpx;
		text-align: center;
		border-radius: 50rpx;
		line-height: 90rpx;
		margin: 64rpx auto;

		/deep/ &.disabled {
			// background: #E3E3E3!important;
		}
	}

	.cash-withdrawal .wrapper .list .tip2 {
		font-size: 26rpx;
		color: #999;
		text-align: center;
		margin: 44rpx 0 20rpx 0;
	}

	.cash-withdrawal .wrapper .list .value {
		height: 135rpx;
		line-height: 135rpx;
		border-bottom: 1rpx solid #eee;
		width: 690rpx;
		margin: 0 auto;
	}

	.cash-withdrawal .wrapper .list .value input {
		font-size: 80rpx;
		color: #282828;
		height: 135rpx;
		text-align: center;
	}

	.cash-withdrawal .wrapper .list .value .placeholder2 {
		color: #bbb;
	}

	.price {
		color: $theme-color;
	}

	.Bank {
		display: block;
		width: 100%;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;


	}
</style>
