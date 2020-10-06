<template>
	<view class="feedback-container">
		<view class="item-box">
			<view class="title-box">
				<text>*</text>反馈类型
			</view>
			<view class="tab-box" v-if="tabList.length>0">
				<block v-for="(item,index) in tabList">
					<view class="tab-item" :key="index" :class="{'active':index == tabKey}"
					 @click="bindTab(item,index)">{{item.cate_name}}</view>
					<!--<view class="tab-item" v-if="item.children && item.children.length > 0" :key="index" :class="{'active':index == tabKey}"
					 @click="bindTab(item,index)">{{item.cate_name}}</view>-->
				</block>
			</view>
			<view class="picker" v-if="qsArray.length>0">
				<picker @change="bindPickerChange" :value="qsIndex" :range="qsArray" range-key="cate_name">
					<view class="picker-box">
						{{qsArray[qsIndex]['cate_name']}}
						<text class="iconfont icon-xiangxia"></text>
					</view>
				</picker>
			</view>
		</view>
		<view class="item-box">
			<view class="title-box">
				<text>*</text>反馈内容
			</view>
			<view class="textarea-box">
				<textarea maxlength="200" placeholder="请输入文字" v-model="con" />
				<view class="num">{{con.length}}/200字</view>
			</view>
		</view>
		<view class="item-box">
			<view class="title-box">
				<text>*</text>图片上传 <text class="des">(上传聊天截图或与问题描述相关的图片)</text>
			</view>
			<view class="upload-img">
				<view class="img-wrapper" v-if="uploadImg.length>0" v-for="(item,index) in uploadImg">
					<image :src="item"></image>
					<view class="iconfont icon-guanbi1" @click="deleteImg(index)"></view>
				</view>
				<view class="add-img" @click="uploadpic">
					<text class="iconfont icon-xiangji"></text>
				</view>
			</view>
		</view>
		<view class="item-box">
			<view class="title-box">
				<text>*</text>联系方式
			</view>
			<view class="input-box">
				<input type="text" placeholder="请填写您的姓名" v-model="name">
				<input type="text" placeholder="请填写您的电话或邮箱" v-model="phone">
			</view>
		</view>
		<view class="item-box">
			<view class="sub-btn" @click="bindSub">提交反馈</view>
			<navigator url="/pages/users/feedback/list" class="link" hover-class="none">反馈记录 <text class="iconfont icon-xiangyou"></text></navigator>
		</view>
		<view class="success" v-if="isShowbox">
			<view class="bg"></view>
			<view class="con">
				<image src="/static/images/success.png" mode=""></image>
				<view class="text">反馈提交成功</view>
				<view class="btn" @click="close">我知道了</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {feedbackType,feedback} from '@/api/user.js'
	import { checkPhone,isEmailAvailable } from '@/utils/validate.js'
	export default {
		data() {
			return {
				// 反馈类型
				tabList: [],
				// 反馈类型key
				tabKey: 0,
				// 反馈列表
				qsArray: [],
				// 反馈index
				qsIndex:0,
				// 反馈内容
				con:'',
				uploadImg:[],
				name:'',
				phone:'',
				isShowbox:false
			}
		},
		onLoad() {
			// 获取反馈类型
			feedbackType().then(({data})=>{
				this.tabList = data
				this.getFeedBack(data)
				console.log(this.tabKey)
				
			})
		},
		methods:{
			// 获取含有二级分类的反馈类型
			getFeedBack(data){
				let that = this;
				data.forEach(function(item,i) {
					if(item.children){
						that.tabKey = i;
						that.qsArray = data[that.tabKey].children
						return;
					}
				});
			},
			// 下拉选中
			bindPickerChange(e){
				this.qsIndex = e.target.value
			},
			// tab切换
			bindTab(item,index){
				this.tabKey = index
				this.qsIndex = 0
				this.qsArray = this.tabList[this.tabKey].children?this.tabList[this.tabKey].children:[]
				
			},
			/**
			 * 上传文件
			 * 
			*/
			uploadpic: function () {
			  let that = this;
			  that.$util.uploadImageOne('upload/image', function (res) {
					console.log(res,'回调')
			    that.uploadImg.push(res.data.path);
					that.$set(that,'uploadImg',that.uploadImg);
			  });
			},
			// 弹窗关闭
			close(){
				this.con = ''
				this.uploadImg = []
				this.name = ''
				this.phone = ''
				this.isShowbox = false
			},
			// 删除图片
			deleteImg(index){
				this.uploadImg.splice(index,1)
			},
			// 提交反馈
			bindSub(){
				if(!this.con){
					uni.showToast({
						title:'请输入反馈内容',
						icon:'none'
					})
					return
				}
				if(this.uploadImg.length == 0){
					uni.showToast({
						title:'请上传截图',
						icon:'none'
					})
					return
				}
				if(!this.name){
					uni.showToast({
						title:'请输入姓名',
						icon:'none'
					})
					return
				}
				if(!isEmailAvailable(this.phone) && !checkPhone(this.phone)){
					uni.showToast({
						title:'请输入电话或者邮箱',
						icon:'none'
					})
					return
				}
				feedback({
					type:this.qsArray[this.qsIndex].feedback_category_id,
					content:this.con,
					images:this.uploadImg,
					realname:this.name,
					contact:this.phone
				}).then(res=>{
					this.isShowbox = true
				}).catch(error=>{
					this.$util.Tips({
						title:error
					})
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		width: 100%;
		background-color: #fff;
	}

	.feedback-container {
		padding: 30rpx;

		.item-box {
			margin-bottom: 40rpx;
			.title-box {
				font-size: 28rpx;
				color: #222222;

				text {
					margin-right: 10rpx;
					color: $theme-color;
				}
				.des{
					margin-left: 10rpx;
					font-size: 22rpx;
					color: #999999;
				}
			}

			.tab-box {
				margin-top: 20rpx;
				display: flex;
				flex-wrap: wrap;

				.tab-item {
					overflow: hidden;
					-webkit-line-clamp: 1;
					-webkit-box-orient: vertical;
					display: -webkit-box;
					line-height: 28px;
					align-items: center;
					justify-content: center;
					width: 200rpx;
					height: 66rpx;
					margin-right: 20rpx;
					border: 1px solid #BFBFBF;
					border-radius: 33px;
					font-size: 28rpx;
					text-align: center;

					&.active {
						background: $theme-color;
						color: #fff;
						border-color: $theme-color;
					}
				}
			}
			.picker{
				margin-top: 30rpx;
				.picker-box{
					position: relative;
					width: 100%;
					height: 90rpx;
					line-height: 90rpx;
					padding: 0 30rpx;
					background-color: #F5F5F5;
					border-radius:10rpx;
					.iconfont{
						position: absolute;
						right: 30rpx;
						top:50%;
						transform: translateY(-50%);
						font-size: 22rpx;
						color: #282828;
					}
				}
			}
			.textarea-box{
				background: #F5F5F5;
				border-radius:10rpx;
				textarea{
					width: 100%;
					height: 300rpx;
					margin-top: 30rpx;
					padding: 20rpx 20rpx 0;
					font-size: 28rpx;
					line-height: 1.5;
					
				}
				.num{
					color: #999;
					text-align: right;
					padding: 20rpx;
				}	
			}
			.upload-img{
				display: flex;
				flex-wrap: wrap;
				.img-wrapper{
					position: relative;
					display: flex;
					flex-wrap: wrap;
					margin: 30rpx 20rpx 20rpx 0;
					image{
						width:158rpx;
						height: 158rpx;
					}
					.iconfont{
						position: absolute;
						right: -15rpx;
						top: -20rpx;
						font-size: 40rpx;
						color: $theme-color;
					}
				}
				.add-img{
					display: flex;
					align-items: center;
					justify-content: center;
					width: 158rpx;
					height: 158rpx;
					background: #F5F5F5;
					border-radius:10rpx;
					margin-top: 20rpx;
					margin-bottom: 20rpx;
					.iconfont{
						color: #B5B5B5;
						font-size: 55rpx;
					}
				}
			}
			.input-box{
				input{
					display: block;
					width: 100%;
					height: 90rpx;
					margin-top: 20rpx;
					padding-left: 20rpx;
					background: #f5f5f5;
					border-radius:10rpx;
					font-size: 28rpx;
				}
			}
			.sub-btn{
				height: 90rpx;
				line-height: 90rpx;
				background: $theme-color;
				color: #fff;
				font-size: 32rpx;
				text-align: center;
				border-radius:45rpx;
			}
			.link{
				display: flex;
				align-items: center;
				justify-content: center;
				margin-top: 20rpx;
				.iconfont{
					margin-top: 6rpx;
					font-size: 22rpx;
				}
			}
		}
		.success{
			z-index: 10;
			position: fixed;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			.bg{
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				background-color: rgba(0,0,0,.5);
			}
			.con{
				position: absolute;
				left: 50%;
				top: 50%;
				transform: translate(-50%,-50%);
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				width:500rpx;
				height:540rpx;
				background:#fff;
				border-radius:10rpx;
				font-size: 34rpx;
				color: #282828;
				image{
					width: 149rpx;
					height: 230rpx;
				}
				.btn{
					width:340rpx;
					height:90rpx;
					line-height: 90rpx;
					margin-top: 38rpx;
					text-align: center;
					color: #fff;
					background:linear-gradient(-90deg,$bg-end 0%,$bg-star 100%);
					border-radius:45rpx;
				}
			}
		}
	}
</style>
