<template>
	<view>
		<view class='commission-details'>
			<view class='promoterHeader bg-color'>
				<view class='headerCon acea-row row-between-wrapper'>
					<view>
						<view class='name'>{{name}}</view>
						<view class='money' v-if="recordType == 1">￥<text class='num'>{{userInfo.total_extract}}</text></view>
						<view class='money' v-if="recordType == 2">￥<text class='num'>{{userInfo.brokerage_price}}</text></view>
					</view>
					<view class='iconfont icon-jinbi1'></view>
				</view>
			</view>
			<view class='sign-record' v-if="type==1">
				<block v-for="(item,index) in recordList" :key="index" v-if="recordList.length>0">
					<view class='list'>
						<view class='item'>
							<!-- <view class='data'>{{item.create_time}}</view> -->
							<view class='listn'>
								<view class='itemn acea-row row-between-wrapper'>
									<view>
										<block v-if="item.status>=0">
											<view class='name line1' v-if="item.extract_type == 0">银行卡提现</view>
											<view class='name line1' v-if="item.extract_type == 1">微信提现</view>
											<view class='name line1' v-if="item.extract_type == 2">支付宝提现</view>
										</block>
										<block v-else>
											<view class='name line1'>提现失败<text class="message">({{item.fail_msg}})</text></view>
										</block>
										<view>{{item.create_time}}</view>
									</view>
									<view class='num' v-if="item.status>=0">-{{item.extract_price}}</view>
									<view class='num font-color' v-else>+{{item.extract_price}}</view>
									
								</view>
							</view>
						</view>
					</view>
				</block>
				<view v-if="recordList.length == 0">
					<emptyPage title='暂无提现记录~'></emptyPage>
				</view>
			</view>
			<view class='sign-record' v-if="type==2">
				<block v-for="(item,index) in recordList" :key="index" v-if="recordList.length>0">
					<view class='list'>
						<view class='item'>
							<!-- <view class='data'>{{item.create_time}}</view> -->
							<view class='listn'>
								<view class='itemn acea-row row-between-wrapper'>
									<view>
											<view class='name line1'>{{item.title}}</view>
										

										<view>{{item.create_time}}</view>
									</view>
									<view class='num' v-if="item.pm==0">-{{item.number}}</view>
									<view class='num font-color' v-else>+{{item.number}}</view>
									
								</view>
							</view>
						</view>
					</view>
				</block>
				<view v-if="recordList.length == 0">
					<emptyPage title='暂无提现记录~'></emptyPage>
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
		getCommissionInfo,
		spreadCount,
		getSpreadInfo,
		extractLst,
		brokerage_list,
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
	import emptyPage from '@/components/emptyPage.vue'
	export default {
		components: {
			// #ifdef MP
			authorize,
			// #endif
			emptyPage
		},
		data() {
			return {
				name: '',
				type: 0,
				page: 1,
				limit: 12,
				recordList: [],
				recordType: 0,
				recordCount: 0,
				status: false,
				isAuto: false, //没有授权的不会自动授权
				isShowAuth: false, //是否隐藏授权
				extractCount: 0,
				userInfo:''
			};
		},
		computed: mapGetters(['isLogin']),
		onLoad(options) {
			if (this.isLogin) {
				this.type = options.type;
			} else {
				// #ifdef H5 || APP-PLUS
				toLogin();
				// #endif 
				// #ifdef MP
				this.isAuto = true;
				this.$set(this, 'isShowAuth', true)
				// #endif
			}
		},
		onShow: function() {
			let type = this.type;
			
			if(type == 2){
				uni.setNavigationBarTitle({
					title: "佣金记录"
				});
				this.name = '佣金明细';
				this.recordType = 2;
			}
			if(type == 1){
				uni.setNavigationBarTitle({
					title: "提现记录"
				});
				this.name = '提现总额';
				this.recordType = 1;
			}
			this.spreadInfo();
			this.getRecordList();
			// this.getRecordListCount();
		},
		methods: {
			spreadInfo(){
				spreadInfo().then(res => {
					this.userInfo = res.data
				});
			},
			onLoadFun() {
				this.isShowAuth = false;
				this.getRecordList();
				// this.getRecordListCount();
			},
			// 授权关闭
			authColse: function(e) {
				this.isShowAuth = e
			},
			getRecordList: function() {
				let that = this;
				let page = that.page;
				let limit = that.limit;
				let status = that.status;
				let recordType = that.recordType;
				let recordList = that.recordList;
				let recordListNew = [];
				if (status == true) return;
				if(this.type == 1){
					extractLst({
						page: page,
						limit: limit
					}, recordType).then(res => {
						let len = res.data.list.length;
						let recordListData = res.data.list;
						recordListNew = recordList.concat(recordListData);
						that.status = limit > len;
						that.page+=1;
						that.$set(that, 'recordList', recordListNew);
					});
				}
				if(this.type == 2){
					brokerage_list({
						page: page,
						limit: limit
					}).then(res => {
						let len = res.data.list.length;
						let recordListData = res.data.list;
						recordListNew = recordList.concat(recordListData);
						that.status = limit > len;
						that.page+=1;
						that.$set(that, 'recordList', recordListNew);
					});
				}
			},
			getRecordListCount: function() {
				let that = this;
				getSpreadInfo().then(res => {
					that.recordCount = res.data.commissionCount;
					that.extractCount = res.data.extractCount;
				});
			}
		},
		onReachBottom: function() {
			this.getRecordList();
		}
	}
</script>

<style scoped lang="scss">
	.commission-details .promoterHeader .headerCon .money {
		font-size: 36rpx;
	}

	.commission-details .promoterHeader .headerCon .money .num {
		font-family: 'Guildford Pro';
	}
	.message{
		font-size: 18rpx;
		color: #fc4141;
	}
</style>
