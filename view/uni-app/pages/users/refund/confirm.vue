<template>
	<view class="refund-wrapper">
		<view class="item" v-for="(item,index) in productData.product" :key="index">
			<view class="img-box">
				<image :src="item.cart_info.product.image"></image>
			</view>
			<view class="info">
				<view class="name line1">{{item.cart_info.product.store_name}}</view>
				<view class="price">￥{{item.cart_info.productAttr.price}} ×{{item.refund_num}}</view>
			</view>
		</view>
		<view class="form-box">
			<view class="form-item item-txt" v-if="type==1">
				<text class="label">商品件数</text>
				<view class="picker">
					<picker @change="bindNumChange" :value="numIndex" :range="numArray">
						<view class="picker-box">
							{{numArray[numIndex]}}
							<text class="iconfont icon-jiantou"></text>
						</view>
					</picker>
				</view>
			</view>
			<view class="form-item item-txt">
				<text class="label">退款金额</text>
				<input style="text-align: right;" type="number" @blur="limitAamount(item)" placeholder="请输入金额" v-model="refund_price">
			</view>
			<view class="form-item item-txt">
				<text class="label">退款原因</text>
				<view class="picker">
					<picker @change="bindPickerChange" :value="qsIndex" :range="qsArray">
						<view class="picker-box">
							{{qsArray[qsIndex]}}
							<text class="iconfont icon-jiantou"></text>
						</view>
					</picker>
				</view>
			</view>
			<view class="form-item item-txtarea">
				<text class="label">备注说明</text>
				<view class="txtarea"><textarea v-model="con" value="" placeholder="填写备注信息，100字以内" /></view>
			</view>
		</view>
		<!-- <view class="upload-box">
			<view class="title">
				<view class="txt">上传凭证</view>
				<view class="des">( 最多可上传9张 )</view>
			</view>
			<view class="upload-img">
				<view class="img-item" v-for="(item,index) in uploadImg" :key="index">
					<image :src="item" mode=""></image>
					<view class="iconfont icon-guanbi1" @click="deleteImg(index)"></view>
				</view>
				<view class="add-img" @click="uploadpic">
					<text class="iconfont icon-icon25201"></text>
					<text class="txt">上传凭证</text>
				</view>
			</view>
		</view> -->
		<view class="btn-box" @click="bindComfirm">申请退款</view>
		<alertBox :msg="msg" v-if="isShowBox" @bindClose="bindClose"></alertBox>
	</view>
</template>

<script>
	import { refundProduct,refundApply,refundMessage } from '@/api/order.js'
	import alertBox from '@/components/alert/index.vue'
	export default{
		components:{
			alertBox
		},
		data(){
			return {
				order_id:0,
				isShowBox:false,
				// 图片上传
				uploadImg:[],
				// 选择问题
				qsArray:[],
				// 问题index
				qsIndex:0,
				// 选择个数
				numArray:[],
				//个数index
				numIndex:0,
				//商品id
				ids:'',
				// 退款方式:1:退款 2:退款退货
				refund_type:'',
				//退款类型 1:单个 2:批量
				type:'',
				productData:[],
				con:'',
				refund_price:'',
				postage_price: '',
				maxRefundPrice: '',
				// 单价
				unitPrice:0,
				msg:'',
				refund_order_id:''//退款id
			}
		},
		onLoad(optios) {
			this.ids = optios.ids
			this.refund_type = optios.refund_type
			this.type = optios.type,
			this.order_id = optios.order_id
			Promise.all([this.refundProduct(),this.refundMessage()])
		},
		methods:{
			// 限制退款金额
			limitAamount(item){
				if(Number(this.refund_price) > this.maxRefundPrice) this.refund_price = this.maxRefundPrice				
			},
			// 退款理由
			refundMessage(){
				refundMessage().then(res=>{
					this.qsArray = res.data
				})
			},
			// 退款商品
			refundProduct(){
				refundProduct(this.order_id,{ids:this.ids}).then(({data})=>{
					this.productData = data
					this.refund_price = data.total_refund_price
					this.postage_price = data.postage_price
					this.maxRefundPrice = data.postage_price + Number(data.total_refund_price)
					
					if(this.type == 1){
						this.unitPrice = this.$util.$h.Div(data.total_refund_price,data.product[0].refund_num)
						for (let i=1;i<=data.product[0].refund_num;i++){
							this.numArray.unshift(i)
						}
						this.refund_price = this.$util.$h.Mul(this.unitPrice, this.numArray[0])
					}
				})
			},
			// 下拉选中
			bindPickerChange(e){
				this.qsIndex = e.target.value
			},
			bindNumChange(e){
				this.numIndex = e.target.value
				this.refund_price = this.$util.$h.Mul(this.unitPrice, this.numArray[e.target.value])
				this.maxRefundPrice = this.refund_price + this.postage_price;
			},
			// 删除图片
			deleteImg(index){
				this.uploadImg.splice(index,1)
			},
			/**
			 * 上传文件
			*/
			uploadpic: function () {
				if(this.uploadImg.length <9){
					let that = this;
					that.$util.uploadImageOne('upload/image', function (res) {
					  that.uploadImg.push(res.data.path);
						that.$set(that,'uploadImg',that.uploadImg);
					});
				}else{
					uni.showToast({
						title:'最多可上传9张',
						icon:'none'
					})
				}
			},
			// 提交
			async bindComfirm(){
				try {
					const data = await refundApply(this.order_id,{
						type:this.type,
						refund_type:this.refund_type,
						num:this.type == 1?this.numArray[this.numIndex]:'',
						ids:this.ids,
						refund_message:this.qsArray[this.qsIndex],
						mark:this.con,
						refund_price:this.refund_price,
						// pics:this.uploadImg
					})
					this.msg = data.message
					this.refund_order_id = data.data.refund_order_id
					this.isShowBox = true
				}catch(err){
					uni.showToast({
						title:err,
						icon:'none'
					})
				}
			},
			// 弹窗关闭
			bindClose(){
				this.isShowBox = false
				uni.navigateTo({
					url:'/pages/users/refund/detail?id='+this.refund_order_id
				})
			}
		}
	}
</script>

<style lang="scss">
	.refund-wrapper{
		
		.item{
			position: relative;
			display: flex;
			padding: 25rpx 30rpx;
			background-color: #fff;
			&:after{
				content: ' ';
				position: absolute;
				right: 0;
				bottom: 0;
				width: 657rpx;
				height: 1px;
				background: #F0F0F0;
			}
			.img-box{
				width: 130rpx;
				height: 130rpx;
				image{
					width: 130rpx;
					height: 130rpx;
					border-radius:16rpx;
				}
			}
			.info{
				display: flex;
				flex-direction: column;
				width: 440rpx;
				margin-left: 26rpx;
				.tips{
					color: #868686;
					font-size: 20rpx;
				}
				.price{
					margin-top: 15rpx;
					font-size: 26rpx;
				}
			}
			.check-box{
				display: flex;
				align-items: center;
				justify-content: center;
				flex: 1;
				.iconfont{
					font-size: 40rpx;
					color: #CCCCCC;
				}
				.icon-xuanzhong1{
					color: $theme-color;
				}
			}
		}
		.form-box{
			padding-left: 30rpx;
			margin-top: 18rpx;
			background-color: #fff;
			.form-item{
				display: flex;
				justify-content: space-between;
				border-bottom: 1px solid #f0f0f0;
				font-size: 30rpx;
			}
			.item-txt{
				align-items: center;
				width: 100%;
				padding:30rpx 30rpx 30rpx 0;
				
			}
			.item-txtarea{
				padding:30rpx 30rpx 30rpx 0;
				textarea{
					display: block;
					width: 400rpx;
					height: 100rpx;
					font-size: 30rpx;
					text-align: right;
				}
			}
			.icon-jiantou{
				margin-left: 10rpx;
				font-size: 28rpx;
				color: #BBBBBB;
			}
		}
		.upload-box{
			padding: 30rpx;
			background-color: #fff;
			.title{
				display: flex;
				align-items: center;
				justify-content: space-between;
				font-size: 30rpx;
				.des{
					color: #BBBBBB;
				}
			}
			.upload-img{
				display: flex;
				flex-wrap: wrap;
				margin-top: 20rpx;
				.img-item{
					position: relative;
					width: 156rpx;
					height: 156rpx;
					margin-right: 24rpx;
					margin-top: 20rpx;
					image{
						width: 156rpx;
						height: 156rpx;
						border-radius: 8rpx;
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
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 156rpx;
					height: 156rpx;
					margin-top: 20rpx;
					border: 1px solid #DDDDDD;
					border-radius: 3rpx;
					color: #BBBBBB;
					font-size: 24rpx;
					.iconfont{
						margin-bottom: 10rpx;
						font-size: 50rpx;
					}
				}
			}
		}
		.btn-box{
			width:690rpx;
			height:86rpx;
			margin: 70rpx auto;
			line-height: 86rpx;
			text-align: center;
			color: #fff;
			background:$theme-color;
			border-radius:43rpx;
			font-size: 32rpx;
		}
	}
</style>
