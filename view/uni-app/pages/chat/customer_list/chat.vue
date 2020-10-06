<template>
	<div class="broadcast-details" :style="'height:'+windowH+'px'">
		<view class="hd-wrapper" :class="active === true ? 'on' : ''">
			<view class="store-wrapper" v-if="storeInfo">
				<view class="left">
					<image :src="storeInfo.mer_avatar" mode=""></image>
					<view class="txt">{{storeInfo.mer_name}}</view>
				</view>
				<navigator :url="'/pages/store/home/index?id='+storeInfo.mer_id" class="link" open-type="redirect">进店</navigator>
			</view>
			<div class="broadcast-details_order">
				<!-- 商品信息 -->
				<div class="broadcast-details_box" v-if="productId && productInfo.product_id">
					<div class="broadcast_details_img">
						<img :src="productInfo.image" />
					</div>
					<div class="broadcast_details_picBox">
						<div class="broadcast_details_tit" v-text="productInfo.store_name"></div>
						<div class="acea-row row-between">
							<div class="broadcast_details_pic">
								￥{{ productInfo.price
								}}<span class="broadcast_details_pic_num">￥{{ productInfo.ot_price }}</span>
							</div>
							<div class="broadcast_details_btn" @click="sendProduct">发送客服</div>
						</div>
					</div>
				</div>
				<!-- 订单信息 -->
				<div class="broadcast_box" v-if="orderId && orderInfo.order_id">
					<div class="broadcast-details_num broadcast_num">
						<span>订单号：{{ orderInfo.order_sn }}</span>
						<span>{{ orderInfo.pay_time }}</span>
					</div>
					<div class="broadcast-details_box">
						<div class="broadcast_details_img">
							<img :src="cartInfo.cart_info.product.image" />
							<div class="broadcast_details_model">
								{{ orderInfo.total_num }}件商品
							</div>
						</div>
						<div class="broadcast_details_picBox">
							<div class="broadcast_details_tit">
								{{ cartInfo.cart_info.product.store_name }}
							</div>
							<div class="acea-row row-between">
								<div class="broadcast_details_pic">
									￥{{ cartInfo.cart_info.productAttr.price
									}}<span class="broadcast_details_pic_num">￥{{ cartInfo.cart_info.productAttr.cost }}</span>
								</div>
								<div class="broadcast_details_btn" @click="sendOrder">
									发送客服
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- 退款订单 -->
				<div class="broadcast_box" v-if="refund_order_id && refundDetail.refund_order_id">
					<div class="broadcast-details_num broadcast_num">
						<span>订单号：{{ refundDetail.refund_order_sn }}</span>
						<span>{{ refundDetail.status_time }}</span>
					</div>
					<div class="broadcast-details_box">
						<div class="broadcast_details_img">
							<img :src="refundDetail.refundProduct[0].product.cart_info.product.image" />
							<div class="broadcast_details_model">
								{{ refundDetail.refund_num }}件商品
							</div>
						</div>
						<div class="broadcast_details_picBox">
							<div class="broadcast_details_tit">
								{{ refundDetail.refundProduct[0].product.cart_info.product.store_name }}
							</div>
							<div class="acea-row row-between">
								<div class="broadcast_details_pic">
									￥{{ refundDetail.refundProduct[0].product.cart_info.productAttr.price
									}}<span class="broadcast_details_pic_num">￥{{ refundDetail.refundProduct[0].product.cart_info.productAttr.ot_price}}</span>
								</div>
								<div class="broadcast_details_btn" @click="sendRefundOrder">
									发送客服
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<scroll-view scroll-y="true" style="height: 100%; overflow: hidden;" :scroll-top="scrollTop" scroll-with-animation="true"
			 @scrolltoupper="bindScroll">
				<div class="chat" ref="chat">
					<Loading :loaded="status" :loading="loading"></Loading>
					<!-- 客服聊天列表 -->
					<block v-for="(item,index) in history" v-if="userId != 0" :key="index">
						<!-- 左边 -->
						<div class="item acea-row row-top" v-if="item.send_type == 0">
							<div class="pictrue">
								<image :src="item.user.avatar" v-if="item.user.avatar"/>
								<image src="/static/images/f.png" mode="" v-else></image>
							</div>
							<div class="text">
								<div class="name">{{ item.user.nickname }}</div>
								<div class="acea-row">
									<!--退款订单链接-->
									<navigator v-if="item.msn_type === 6 && item.refundOrder.refund_order_id" :url="'/pages/admin/orderDetail/index?id='+item.refundOrder.refund_order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.refundOrder.refund_order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.refundOrder.refundProduct" :key="item.service_log_id">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.product.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.refund_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.product.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.product.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>
									<!--订单链接-->
									<navigator v-if="item.msn_type === 5 && item.orderInfo.order_id" :url="'/pages/admin/orderDetail/index?id='+item.orderInfo.order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.orderInfo.order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.orderInfo.orderProduct" :key="val.id">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.orderInfo.total_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>

									<!--商品链接-->
									<div class="conter acea-row row-middle" v-if="item.msn_type === 4 && item.product">
										<div class=" noPad">
											<navigator class="acea-row row-column-around noPad" v-if="item.product.product_id" :url="`/pages/goods_details/index?id=${item.product.product_id}`" open-type="redirect">
												<div class="broadcast_details_img_no">
													<img :src="item.product.image" />
												</div>
												<div class="broadcast_details_picBox_no noPad">
													<div class="broadcast_details_pic">
														￥{{ item.product.price }}
													</div>
													<div class="broadcast_details_tit_no" v-text="item.product.store_name"></div>
												</div>
											</navigator>
										</div>
									</div>

									<!-- <div class="conter acea-row row-middle" v-if="item.msn_type === 3">
										<img src="/static/images/signal2.gif" class="signal" style="margin-right: 0.27rem;" />
									</div> -->

									<div class="conter acea-row row-middle" v-if="item.msn_type === 3">
										<image :src="item.msn" mode="heightFix" />
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 2">
										<i class="em" :class="item.msn"></i>
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 1">
										{{ item.msn }}
									</div>
								</div>
							</div>
						</div>
						<!-- 右边 -->
						<div class="item acea-row row-top row-right" v-else>
							<div class="text textR">
								<div class="name">{{ item.service.nickname }}</div>
								<div class="acea-row ">
									<!--退款订单链接-->
									<navigator v-if="item.msn_type === 6 && item.refundOrder.refund_order_id" :url="'/pages/admin/orderDetail/index?id='+item.refundOrder.refund_order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.refundOrder.refund_order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.refundOrder.refundProduct" :key="val.id">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.product.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.refund_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.product.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.product.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>


									<!--订单链接-->
									<navigator v-if="item.msn_type === 5 && item.orderInfo.order_id" :url="'/pages/admin/orderDetail/index?id='+item.orderInfo.order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.orderInfo.order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.orderInfo.orderProduct" :key="val.id">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.orderInfo.total_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>

									<!--商品链接-->
									<div class="conter acea-row row-middle" v-if="item.msn_type === 4 && item.product">
										<div class=" acea-row row-column-around noPad">
											<navigator class="" v-if="item.product.product_id" :url="`/pages/goods_details/index?id=${item.product.product_id}`" open-type="redirect">
												<div class="broadcast_details_img_no">
													<img :src="item.product.image" />
												</div>
												<div class="broadcast_details_picBox_no noPad">
													<div class="broadcast_details_pic">
														￥{{ item.product.price }}
													</div>
													<div class="broadcast_details_tit_no" v-text="item.product.store_name"></div>
												</div>
											</navigator>
										</div>
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 3">
										<image :src="item.msn" mode="heightFix" />
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 2">
										<i class="em" :class="item.msn"></i>
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 1">
										{{ item.msn }}
									</div>
								</div>
							</div>
							<div class="pictrue">
								<image :src="item.service.avatar" v-if="item.service.avatar" />
								<image src="/static/images/f.png" mode="" v-else></image>
							</div>
						</div>
					</block>
					<!-- 客户聊天列表 -->
					<block v-for="(item,j) in history" v-if="userId == 0" :key="j">
						<!-- 左边 -->
						<div class="item acea-row row-top" v-if="item.send_type == 1">
							<div class="pictrue">
								<image :src="item.service.avatar" v-if="item.service.avatar" />
								<image src="/static/images/f.png" mode="" v-else></image>
							</div>
							<div class="text">
								<div class="name">{{ item.service.nickname }}</div>
								<div class="acea-row">
									<!--退款订单链接-->
									<navigator v-if="item.msn_type === 6 && item.refundOrder.refund_order_id" :url="'/pages/users/refund/detail?id='+item.refundOrder.refund_order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.refundOrder.refund_order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<!-- :key="item.service_log_id" -->
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.refundOrder.refundProduct">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.product.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.refund_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.product.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.product.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>
									<!--订单链接-->
									<navigator v-if="item.msn_type === 5 && item.orderInfo.order_id" :url="'/pages/order_details/index?order_id='+item.orderInfo.order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.orderInfo.order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<!-- :key="item.service_log_id" -->
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.orderInfo.orderProduct">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.orderInfo.total_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>

									<!--商品链接-->
									<div class="conter acea-row row-middle" v-if="item.msn_type === 4 && item.product">
										<div class=" noPad">
											<navigator class="acea-row row-column-around noPad" v-if="item.product.product_id" :url="`/pages/goods_details/index?id=${item.product.product_id}`" open-type="redirect">
												<div class="broadcast_details_img_no">
													<img :src="item.product.image" />
												</div>
												<div class="broadcast_details_picBox_no noPad">
													<div class="broadcast_details_pic">
														￥{{ item.product.price }}
													</div>
													<div class="broadcast_details_tit_no" v-text="item.product.store_name"></div>
												</div>
											</navigator>
										</div>
									</div>

									<!-- <div class="conter acea-row row-middle" v-if="item.msn_type === 3">
										<img src="/static/images/signal2.gif" class="signal" style="margin-right: 0.27rem;" />
									</div> -->

									<div class="conter acea-row row-middle" v-if="item.msn_type === 3">
										<image :src="item.msn" mode="heightFix" />
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 2">
										<i class="em" :class="item.msn"></i>
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 1">
										{{ item.msn }}
									</div>
								</div>
							</div>
						</div>
						<!-- 右边 -->
						<div class="item acea-row row-top row-right" v-else>
							<div class="text textR">
								<div class="name">{{ item.user.nickname }}</div>
								<div class="acea-row ">
									<!--退款订单链接-->
									<navigator v-if="item.msn_type === 6 && item.refundOrder.refund_order_id" :url="'/pages/users/refund/detail?id='+item.refundOrder.refund_order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.refundOrder.refund_order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<!-- :key="item.service_log_id" -->
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.refundOrder.refundProduct">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.product.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.refund_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.product.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.product.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>


									<!--订单链接-->
									<navigator v-if="item.msn_type === 5 && item.orderInfo.order_id" :url="'/pages/order_details/index?order_id='+item.orderInfo.order_id" open-type="redirect">
										<div class="broadcast-details_num">
											<span>订单号：{{ item.orderInfo.order_sn }}</span>
										</div>
										<div class="conter acea-row row-middle">
											<!-- :key="item.service_log_id" -->
											<div class="broadcast-details_order noPad" v-for="(val, inx) in item.orderInfo.orderProduct">
												<div class="broadcast-details_box noPad" v-if="inx == 0">
													<div class="broadcast_details_img">
														<img :src="val.cart_info.product.image" />
														<div class="broadcast_details_model">
															{{item.orderInfo.total_num}}件商品
														</div>
													</div>
													<div class="broadcast_details_picBox noPad">
														<div class="broadcast_details_tit" v-text="val.cart_info.product.store_name"></div>
														<div class="broadcast_details_pic">
															￥{{ val.cart_info.productAttr.price }}
														</div>
													</div>
												</div>
											</div>
										</div>
									</navigator>

									<!--商品链接-->
									<div class="conter acea-row row-middle" v-if="item.msn_type === 4 && item.product">
										<div class=" acea-row row-column-around noPad">
											<navigator class="" v-if="item.product.product_id" :url="`/pages/goods_details/index?id=${item.product.product_id}`" open-type="redirect">
												<div class="broadcast_details_img_no">
													<img :src="item.product.image" />
												</div>
												<div class="broadcast_details_picBox_no noPad">
													<div class="broadcast_details_pic">
														￥{{ item.product.price }}
													</div>
													<div class="broadcast_details_tit_no" v-text="item.product.store_name"></div>
												</div>
											</navigator>
										</div>
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 3">
										<image :src="item.msn" mode="heightFix" />
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 2">
										<i class="em" :class="item.msn"></i>
									</div>

									<div class="conter acea-row row-middle" v-if="item.msn_type === 1">
										{{ item.msn }}
									</div>
								</div>
							</div>
							<div class="pictrue">
								<image :src="item.user.avatar" v-if="item.user.avatar"/>
								<image src="/static/images/f.png" mode="" v-else></image>
							</div>
						</div>
					</block>
				</div>


				<div :style=" active === true
			      ? 'height:' + footerConH + 'rem;'
			      : 'height:' + footerH + 'rem;'
			  "></div>
			</scroll-view>
		</view>
		<div class="footerCon" :class="active === true ? 'on' : ''" :style="'transform: translate3d(0,' + percent + '%,0);'"
		 ref="footerCon">
			<form>
				<div class="footer acea-row row-between row-bottom" ref="footer">
					<!--<img-->
					<!--:src="-->
					<!--voice === true-->
					<!--? require('@assets/images/keyboard.png')-->
					<!--: require('@assets/images/voice.png')-->
					<!--"-->
					<!--@click="voiceBnt"-->
					<!--/>-->

					<img @click="uploadImg" src="/static/images/plus.png" />
					<img :src="
              active === true
                ? '/static/images/keyboard.png'
                : '/static/images/face.png'
            "
					 @click="emoticon" />
					<div class="voice acea-row row-center-wrapper" v-if="voice" @touchstart.prevent="start" @touchmove.prevent="move"
					 @touchend.prevent="end">
						{{ speak }}
					</div>
					<!-- <p contenteditable="true" class="input" ref="input" v-show="!voice" @keydown="keydown($event)" @keyup="keyup" @focus="focus"></p> -->
					<input type="text" placeholder="请输入内容" class="input" ref="input" v-show="!voice" @input="bindInput" @keyup="keyup"
					 @focus="focus" cursor-spacing="20" v-model="textCon">
					<div class="send" :class="sendColor === true ? 'font-color-red' : ''" @click="sendTest">
						发送
					</div>
				</div>
			</form>
			<div class="banner slider-banner">
				<swiper class="swiper-wrapper" :autoplay="autoplay" :circular="circular" :interval="interval" :duration="duration"
				 v-if="emojiGroup.length > 0">
					<block v-for="(emojiList, index) in emojiGroup" :key="index">
						<swiper-item>
							<i class="em" :class="emoji" v-for="emoji in emojiList" :key="emoji" @click="addEmoji(emoji)"></i>
							<img src="/static/images/del.png" class="emoji-outer" />
						</swiper-item>
					</block>
				</swiper>
			</div>
		</div>
		<div class="recording" v-if="recording">
			<img src="/static/images/recording.png" />
		</div>
	</div>
</template>
<script>
	import emojiList from "@/utils/emoji";
	import Socket from "@/libs/chat";
	import {
		getChatRecord,
		getMerHistory
	} from "@/api/user";
	import {
		getProductDetail,
		getStoreDetail
	} from "@/api/store";
	import {
		getOrderDetail,
		refundDetail
	} from "@/api/order";
	// import VueCoreImageUpload from "vue-core-image-upload";
	import easyUpload from '@/components/easy-upload/easy-upload.vue'
	import Loading from "@/components/Loading";
	import {
		TOKENNAME,
		HTTP_REQUEST_URL
	} from '@/config/app.js';

	const chunk = function(arr, num) {
		num = num * 1 || 1;
		var ret = [];
		arr.forEach(function(item, i) {
			if (i % num === 0) {
				ret.push([]);
			}
			ret[ret.length - 1].push(item);
		});
		return ret;
	};

	const NAME = "CustomerService";
	const PATH = 'pages/chat/customer_list/chat'
	export default {
		name: NAME,
		components: {
			// swiper,
			// swiperSlide,
			Loading,
			easyUpload
		},
		props: {
			couponList: {
				type: Array,
				default: () => []
			}
		},
		data: function() {
			return {
				url: `${HTTP_REQUEST_URL}/api/upload/image`,
				headers: {
					"Authori-zation": "Bearer " + this.$store.state.app.token
				},
				emojiGroup: chunk(emojiList, 20),
				active: false,
				voice: false,
				speak: "按住 说话",
				recording: false,
				swiperOption: {
					pagination: {
						el: ".swiper-pagination",
						clickable: true
					},
					speed: 1000,
					observer: true,
					observeParents: true
				},
				percent: 0,
				footerConH: 0,
				footerH: 1.08,
				socket: null,
				toUid: 0,
				page: 1,
				limit: 30,
				loading: false,
				loaded: false,
				history: [],
				sendColor: false,
				sendtxt: "",
				productId: 0,
				productInfo: {},
				orderId: "",
				orderInfo: {},
				cartInfo: {},
				autoplay: false,
				circular: true,
				interval: 3000,
				duration: 500,
				upload_max: 2, //图片大小
				//上传的图片地址
				uploadImages: [],
				//展示的图片地址
				uploads: [],
				// 超出限制数组
				exceeded_list: [],
				windowH: 0,
				isBQ: false,
				scrollTop: 0, //滚动距离
				textCon: '', //文字
				mer_id: 0, //商铺id
				refund_order_id: 0, //退款订单id
				refundDetail: '', //退款订单详情
				userId: 0, //客服----用户id
				pageType: 0, //页面区分
				status: false, //砍价列表是否获取完成 false 未完成 true 完成
				storeInfo: '',
				isBack: false
			};
		},
		watch: {
			// // #ifdef H5
			// $route(n) {
			// 	console.log(n)
			// 	if (n.path == PATH) {
			// 		if (n.query) {
			// 			this.orderId = n.query.order_id;
			// 			this.toUid = n.query.uid || 0;
			// 			this.productId = parseInt(n.query.productId) || 0;
			// 			this.orderId = n.query.order_id || ""
			// 			this.mer_id = n.query.mer_id || 0
			// 			this.refund_order_id = n.query.refund_order_id || 0
			// 			this.userId = n.query.userId || 0
			// 		}
			// 		this.wsStart();
			// 	} else {
			// 		if (this.userId == 0) {
			// 			this.socket.send({
			// 				data: {
			// 					mer_id: this.mer_id
			// 				},
			// 				type: "chat_end"
			// 			});
			// 		} else {
			// 			this.socket.send({
			// 				data: {
			// 					uid: this.userId
			// 				},
			// 				type: "service_chat_end"
			// 			});
			// 		}
			// 		this.socket && this.socket.onClose();
			// 	}
			// }
			// // #endif
		},
		beforeDestroy() {
			console.log('beforeDestroy')
			this.wsEnd();
		},
		onUnload() {
			console.log('onUnload')
			this.wsEnd();
		},
		// onHide() {
		// 	console.log('onHide')
		// 	this.wsEnd();
		// },
		onLoad(option) {
			let self = this
			this.toUid = option.uid || 0;
			this.productId = parseInt(option.productId) || 0;
			this.orderId = option.order_id || ""
			this.mer_id = option.mer_id || 0
			this.refund_order_id = option.refund_order_id || 0
			this.userId = option.userId || 0
			uni.getSystemInfo({
				success: function(res) {
					self.windowH = res.windowHeight
				}
			})
		},
		mounted: function() {
			this.height();
			if (this.userId == 0) {
				// 用户
				this.getHistory();
				this.getproductInfo();
				this.getOrderInfo();
				this.getRefundDetail();
				this.getStoreDetail();
			} else {
				// 客服
				this.getMerHistory();
			}
			uni.$on("socket_open", () => {
				if (this.userId == 0) {
					this.socket.send({
						data: {
							mer_id: this.mer_id
						},
						type: "chat_start"
					});
				} else {
					this.socket.send({
						data: {
							uid: this.userId
						},
						type: "service_chat_start"
					});
				}
			});
			uni.$on(["reply", "chat", "send_chat"], data => {
				this.history.push(data);
				this.height();
			});
			uni.$on("socket_error", () => {
				if (!this.isBack) {
					let self = this
					uni.showModal({
						title: '提示',
						content: '连接失败,是否重新连接',
						success: function(res) {
							if (res.confirm) {
								self.wsStart()
							} else if (res.cancel) {
								uni.navigateBack();
							}
						}
					});

				}

			});
			uni.$on("err_tip", data => {
				let pages = getCurrentPages();
				let curPage = pages[pages.length - 1]; // 当前页面路径
				let beforePage = pages[pages.length - 2]; // 前一个页面路径
				console.log(pages,'pages')
				console.log(curPage.route,'curPage')
				if (!this.isBack) {
					uni.showModal({
						title: '提示',
						content: data,
						success: function(res) {
							if (res.confirm) {
								console.log('用户点击确定');
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					});
				}
			});
			uni.$on("socket_close", () => {
				let pages = getCurrentPages();
				let curPage = pages[pages.length - 1]; // 当前页面路径
				let beforePage = pages[pages.length - 2]; // 前一个页面路径
				console.log(pages,'pages')
				console.log(curPage,'curPage')
				if (!this.isBack) {
					let curRoute = this.$mp.page.route;
					console.log(this.$mp.page, 'curRoute')
					let self = this
					uni.showModal({
						title: '提示',
						content: '连接断开，是否重新连接',
						success: function(res) {
							if (res.confirm) {
								self.wsStart()
							} else if (res.cancel) {
								uni.navigateBack();
							}
						}
					});
				}
			})
			this.wsStart();

		},
		methods: {
			// 获取店铺详情
			getStoreDetail() {
				getStoreDetail(this.mer_id).then(res => {
					this.storeInfo = res.data
				})
			},
			// ws连接
			wsStart() {
				this.socket = new Socket();
				this.height();
			},
			// ws关闭
			wsEnd() {
				if (this.userId == 0) {
					this.socket.send({
						data: {
							mer_id: this.mer_id
						},
						type: "chat_end"
					});
				} else {
					this.socket.send({
						data: {
							uid: this.userId
						},
						type: "service_chat_end"
					});
				}
				this.isBack = true
				this.socket && this.socket.onClose();
			},
			// 退款订单
			getRefundDetail() {
				if (this.refund_order_id) {
					refundDetail(this.refund_order_id).then(res => {
						this.refundDetail = res.data
					})
				}
			},
			uploadImg() {
				let self = this
				self.$util.uploadImageOne('upload/image', function(res) {
					if(res.status == 200){
						self.sendMsg(res.data.path, 3)
					}
				});
			},
			getOrderInfo() {
				if (!this.orderId) return;
				getOrderDetail(this.orderId).then(res => {
					this.orderInfo = res.data;
					// if (this.orderInfo.add_time_h) {
					// 	this.orderInfo.add_time_h = this.orderInfo.add_time_h.substring(
					// 		0,
					// 		this.orderInfo.add_time_h.lastIndexOf(":")
					// 	);
					// }
					if (this.orderInfo.orderProduct.length) {
						this.cartInfo = this.orderInfo.orderProduct[0];
					}
				});
			},
			getproductInfo() {
				let that = this;
				if (!this.productId) return;
				getProductDetail(this.productId).then(res => {
					that.productInfo = res.data;
				});
			},
			scroll() {
				if (window.scrollY < 300 && !this.loaded && !this.loading)
					this.getHistory();
			},
			imageuploaded(res) {
				console.log(res)
				if (res.status !== 200)
				return this.$util.Tips({
					title: res.msg || "上传图片失败"
				});
				this.sendMsg(res.data.url, 3);
			},
			// 用户聊天记录
			getHistory() {
				if (this.loading || this.loaded) return;
				this.loading = true;
				getChatRecord(this.mer_id, {
						page: this.page,
						limit: this.limit
					})
					.then(({
						data
					}) => {
						this.history = data.list.concat(this.history);
						if (this.page === 1) {
							this.$nextTick(function() {
								// window.scrollTo(0, document.documentElement.scrollHeight + 999);
								this.height();
							});
						}
						this.page++;
						this.loading = false;
						this.loaded = data.length < this.limit;
					})
					.catch(err => {
						this.$util.Tips({
							title: err.msg || "加载失败"
						});
					});
			},
			// 客服聊天记录
			getMerHistory() {
				if (this.loading || this.loaded) return;
				this.loading = true;
				getMerHistory(this.userId, this.mer_id, {
						page: this.page,
						limit: this.limit
					})
					.then(({
						data
					}) => {
						this.history = data.list.concat(this.history);
						if (this.page === 1) {
							this.$nextTick(function() {
								this.height();
							});
						}
						this.page++;
						this.loading = false;
						this.loaded = data.length < this.limit;
					})
					.catch(err => {
						this.$util.Tips({
							title: err.msg || "加载失败"
						});
					});
			},
			focus: function() {
				this.active = false;
			},
			keyup: function() {
				// console.log(this.$refs.input.innerHTML.length);
				if (this.$refs.input.innerHTML.length > 0) {
					this.sendColor = true;
				} else {
					this.sendColor = false;
				}
			},
			addEmoji(name) {
				this.sendMsg(name, 2);
			},
			sendMsg(msn, type) {
				let self = this
				if (this.userId == 0) {
					this.height();
					this.socket.send({
						data: {
							msn,
							msn_type: type,
							// to_uid: this.toUid,
							mer_id: self.mer_id
						},
						type: "send_chat",
					})
				} else {
					this.height();
					this.socket.send({
						data: {
							msn,
							msn_type: type,
							// to_uid: this.toUid,
							uid: self.userId
						},
						type: "service_chat",
					})
				}


			},
			sendTest() {
				this.sendMsg(this.textCon, 1);
				this.textCon = ''
				this.height();
			},
			sendProduct() {
				this.sendMsg(this.productId, 4);
				this.productId = 0;
				this.productInfo = {};
			},
			sendOrder() {
				this.sendMsg(this.orderId, 5);
				this.orderId = 0;
				this.orderInfo = {};
			},
			sendRefundOrder() {
				this.sendMsg(this.refund_order_id, 6);
				this.refund_order_id = 0;
				this.refundDetail = {};
			},
			bindInput: function(e) {
				if (e.detail.value) {
					this.sendColor = true
				} else {
					this.sendColor = false
				}
				// if ($event.keyCode === 13) {
				// 	$event.preventDefault();
				// 	if (this.$refs.input.innerHTML) {
				// 		this.sendMsg(this.$refs.input.innerHTML, 1);
				// 		this.$refs.input.innerHTML = "";
				// 	}
				// }
				this.height();
			},
			start() {
				var that = this;
				this.longClick = 0;
				this.timeOutEvent = setTimeout(function() {
					that.longClick = 1;
				}, 500);
				that.speak = "松开 结束";
				that.recording = true;
			},
			move() {
				clearTimeout(this.timeOutEvent);
				this.timeOutEvent = 0;
			},
			end() {
				clearTimeout(this.timeOutEvent);
				if (this.timeOutEvent !== 0 && this.longClick === 0) {
					//点击
					//此处为点击事件----在此处添加跳转详情页
				}
				this.speak = "按住 说话";
				this.recording = false;
				return false;
			},
			voiceBnt: function() {
				this.active = false;
				if (this.voice === true) {
					this.voice = false;
					this.$nextTick(function() {
						this.$refs.input.focus();
					});
				} else {
					this.voice = true;
				}
				// window.scrollTo(0, document.documentElement.scrollHeight);
				this.percent = 0;
				this.footerConH = 0;
				this.footerH = 0;
				this.$nextTick(function() {
					this.height();
				});
			},
			emoticon: function() {
				this.voice = false;
				if (this.active === true) {
					this.active = false;
					this.isBQ = false
					// this.$nextTick(function() {
					// 	this.$refs.input.focus();
					// });
				} else {
					this.active = true;
					this.isBQ = true
					// this.$nextTick(function() {
					// 	this.$refs.input.blur();
					// });
				}
				// this.$nextTick(function() {
				// 	window.scrollTo(0, document.documentElement.scrollHeight);
				// });
				this.height();
			},
			height() {
				let self = this
				var scrollTop = 0
				let info = uni.createSelectorQuery().select(".chat");
				// let footerConH = this.$refs.footerCon.offsetHeight;
				// let footerH = this.$refs.footer.offsetHeight;
				// let scale = 750 / window.screen.availWidth;
				// this.footerConH = (footerConH * scale) / 100;
				// this.footerH = (footerH * scale) / 100;
				// this.percent = ((this.footerConH - this.footerH) / this.footerConH) * 100;




				setTimeout(res => {
					info.boundingClientRect(function(data) { //data - 各种参数
						console.log(data.height, 'data.height') // 获取元素高度
						scrollTop = data.height
						if (self.active) {
							self.scrollTop = parseInt(scrollTop) + 500
						} else {
							self.scrollTop = parseInt(scrollTop) + 100
						}
					}).exec()
				}, 1000)
			},
			// 滚动到头部
			bindScroll() {
				console.log('滚动到头部')
				if (this.userId == 0) {
					this.getHistory();
				} else {
					this.getMerHistory();
				}
			}
		}
	};
</script>

<style lang="scss">
	page {
		width: 100%;
		height: 100%;
	}

	@import url("@/plugin/emoji-awesome/css/google.min.css");

	.broadcast_num {
		padding: 0 10rpx !important;
	}

	.noPad {
		padding: 0 !important;
		margin-bottom: 0 !important;
		height: auto !important;
	}

	.broadcast-details_num {
		width: 100%;
		height: 80rpx;
		line-height: 80rpx;
		color: #000000;
		font-size: 26rpx;
		display: flex;
		justify-content: space-between;
		background: #fff;
		border-bottom: 1px dashed rgba(0, 0, 0, 0.2);
		padding: 0 24rpx;
	}

	.broadcast-details_order {
		padding: 15rpx;
	}

	.broadcast-details_box {
		padding: 24rpx;
		display: flex;
		background: #fff;
		border-radius: 6px;
		margin-bottom: 24rpx;
	}

	.broadcast_details_model {
		width: 100%;
		height: 43rpx;
		background: rgba(0, 0, 0, 0.5);
		border-radius: 0px 0px 8px 8px;
		position: absolute;
		z-index: 2;
		bottom: 0;
		font-size: 22rpx;
		color: #fff;
		text-align: center;
		line-height: 43rpx;
	}

	.broadcast_details_img {
		width: 140rpx;
		height: 140rpx;
		border-radius: 8px;
		overflow: hidden;
		position: relative;
	}

	.broadcast_details_img img {
		width: 100%;
		height: 100%;
	}

	.broadcast_details_img_no {
		width: 100%;
		height: auto;
		border-radius: 8px 8px 0px 0px;
		overflow: hidden;
		margin-bottom: 10rpx;
	}

	.broadcast_details_picBox_no {
		width: 100%;
	}

	.broadcast_details_img_no img {
		width: 100%;
		height: 100%;
	}

	.broadcast_details_tit {
		font-size: 28rpx;
		color: #333333;
		height: 85rpx;
		font-weight: 800;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
		text-align: left !important;
	}

	.broadcast_details_tit_no {
		font-size: 28rpx;
		color: #333333;
		font-weight: 800;
		text-align: left;
		margin-top: 5rpx;
	}

	.broadcast_details_picBox {
		width: 75%;
		margin-left: 24rpx;
	}

	.broadcast_details_pic {
		font-size: 36rpx;
		color: #e93323;
		text-align: left;
	}

	.broadcast_details_pic_num {
		text-decoration: line-through;
		font-size: 28rpx;
		color: rgba(0, 0, 0, 0.5);
		margin-left: 0.1rem;
	}

	.broadcast_details_btn {
		width: 130rpx;
		height: 50rpx;
		background: #e83323;
		opacity: 1;
		border-radius: 125rpx;
		color: #fff;
		font-size: 24rpx;
		text-align: center;
		line-height: 50rpx;
	}

	.broadcast-details .chat {
		padding: 1rpx 23rpx 0 23rpx;
		margin-bottom: 3rpx;
	}

	.broadcast-details .chat .item {
		margin-top: 37rpx;
	}

	.broadcast-details .chat .item .pictrue {
		width: 80rpx;
		height: 80rpx;
		margin-top: 10rpx;
	}

	.broadcast-details .chat .item .pictrue image {
		width: 100%;
		height: 100%;
		border-radius: 50%;
	}

	.broadcast-details .chat .item .text {
		margin-left: 20rpx;
	}

	.broadcast-details .chat .item .text.textR {
		text-align: right;
		margin: 0 20rpx 0 0;
	}

	.broadcast-details .chat .item .text .name {
		font-size: 24rpx;
		color: #999;
	}

	.broadcast-details .chat .item .text .name .return {
		color: #509efb;
		margin-left: 17rpx;
	}

	.broadcast-details .chat .item .text.textR .name .return {
		margin: 0 0.17rem 0 0;
	}

	.broadcast-details .chat .item .text .conter {
		background-color: #fff;
		border-radius: 8rpx;
		padding: 16rpx 20rpx;
		font-size: 30rpx;
		color: #333;
		position: relative;
		max-width: 496rpx;
		margin-top: 2rpx;
		word-break: break-all;
		.em{
			margin: 0;
		}
	}

	.broadcast-details .chat .item .text .spot {
		width: 15rpx;
		height: 15rpx;
		background-color: #c00000;
		border-radius: 50%;
		margin-left: 20rpx;
	}

	.broadcast-details .chat .item .text .conter:before {
		position: absolute;
		content: "";
		width: 0;
		height: 0;
		border-bottom: 9rpx solid transparent;
		border-right: 14rpx solid #fff;
		border-top: 9rpx solid transparent;
		left: -14rpx;
		top: 25rpx;
	}

	.broadcast-details .chat .item .text.textR .conter:before {
		left: unset;
		right: -14rpx;
		transform: rotateY(180deg);
	}

	.broadcast-details .chat .item .text .conter img {
		/* #ifdef H5 */
		width: 100%;
		/* #endif */
		display: block;
	}

	.broadcast-details .chat .item .text .conter .signal {
		width: 48rpx;
		height: 48rpx;
	}

	.broadcast-details .chat .item .text .conter .signal.signalR {
		transform: rotate(180deg);
		-ms-transform: rotate(180deg);
		-webkit-transform: rotate(180deg);
	}

	.broadcast-details .footerCon {

		height: 100rpx;
		width: 100%;
		transition: all 0.005s cubic-bezier(0.25, 0.5, 0.5, 0.9);
		background-color: #fff;
	}

	.broadcast-details .footerCon.on {
		position: relative;
		top: -300rpx;
		transform: translate3d(0, 0, 0) !important;
	}

	.broadcast-details .footerCon .banner .swiper-slide {
		flex-wrap: wrap;
		-webkit-flex-wrap: wrap;
		background-color: #fff;
		padding-bottom: 50rpx;
		border-top: 1px solid #f5f5f5;
	}

	.broadcast-details .footerCon .banner .swiper-slide .emoji-outer,
	.swiper-slide .em {
		display: block;
		width: 50rpx;
		height: 50rpx;
		margin: 40rpx 0 0 50rpx;
	}

	.broadcast-details .footerCon .banner .swiper-container-horizontal>.swiper-pagination-bullets {
		bottom: 10rpx;
	}

	.broadcast-details .footerCon .slider-banner .swiper-pagination-bullet-active {
		background-color: #999;
	}

	.broadcast-details .recording {
		width: 300rpx;
		height: 300rpx;
		position: fixed;
		top: 40%;
		left: 50%;
		margin-left: -150rpx;
	}

	.broadcast-details .recording img {
		width: 100%;
		height: 100%;
	}

	.broadcast-details .footer {
		width: 100%;
		background-color: #fff;
		padding: 17rpx 26rpx;
	}

	.broadcast-details .footer img {
		width: 61rpx;
		height: 60rpx;
		display: block;
	}

	.broadcast-details .footer .input,
	.broadcast-details .footer .voice {
		width: 440rpx;
		border-radius: 10rpx;
		background-color: #e5e5e5;
		/* padding: 17rpx 30rpx; */
		height: 60rpx;
		padding-left: 20rpx;
	}

	.input {}

	.broadcast-details .footer .input {
		max-height: 150rpx;
		overflow-y: auto;
		overflow-x: hidden;
	}

	.broadcast-details .footer .send {
		width: 70rpx;
		text-align: center;
		height: 60rpx;
		line-height: 60rpx;
		font-weight: bold;
	}

	.em {
		display: inline-block;
		width: 50rpx;
		height: 50rpx;
		margin: 40rpx 0 0 50rpx;
	}

	.emoji-outer {
		position: absolute;
		right: 50rpx;
		bottom: 30rpx;
		width: 50rpx;
		height: 50rpx;
	}

	.broadcast-details {
		display: flex;
		flex-direction: column;
		width: 100%;
		overflow: hidden;

		.hd-wrapper {
			flex: 1;
			display: flex;
			flex-direction: column;
			overflow: hidden;

			&.on {
				padding-bottom: 300rpx;
			}
		}
	}

	.store-wrapper {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 15rpx 20rpx;
		background-color: #fff;

		image {
			width: 60rpx;
			height: 60rpx;
			margin-right: 10rpx;
		}

		.left {
			display: flex;
			align-items: center;
		}

		.link {
			width: 80rpx;
			height: 40rpx;
			line-height: 38rpx;
			border: 1px solid $theme-color;
			border-radius: 20px;
			font-size: 24rpx;
			color: $theme-color;
			text-align: center;
		}
	}
</style>
