(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-users-user_address_list-index"],{"1bfe":function(t,e,i){"use strict";var s,n=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("v-uni-view",[s("v-uni-view",{staticClass:"address-management",class:t.addressList.length<1&&t.page>1?"fff":""},[s("v-uni-view",{staticClass:"line"},[t.addressList.length?s("v-uni-image",{attrs:{src:i("744d")}}):t._e()],1),t.addressList.length?s("v-uni-radio-group",{staticClass:"radio-group",on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.radioChange.apply(void 0,arguments)}}},t._l(t.addressList,(function(e,i){return s("v-uni-view",{key:i,staticClass:"item"},[s("v-uni-view",{staticClass:"address",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.goOrder(e.address_id)}}},[s("v-uni-view",{staticClass:"consignee"},[t._v("收货人："+t._s(e.real_name)),s("v-uni-text",{staticClass:"phone"},[t._v(t._s(e.phone))])],1),s("v-uni-view",[t._v("收货地址："+t._s(e.province)+t._s(e.city)+t._s(e.district)+t._s(e.detail))])],1),s("v-uni-view",{staticClass:"operation acea-row row-between-wrapper"},[s("v-uni-radio",{staticClass:"radio",attrs:{value:i.toString(),checked:!!e.is_default}},[s("v-uni-text",[t._v("设为默认")])],1),s("v-uni-view",{staticClass:"acea-row row-middle"},[s("v-uni-view",{on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.editAddress(e.address_id)}}},[s("v-uni-text",{staticClass:"iconfont icon-bianji"}),t._v("编辑")],1),s("v-uni-view",{on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.delAddress(i)}}},[s("v-uni-text",{staticClass:"iconfont icon-shanchu"}),t._v("删除")],1)],1)],1)],1)})),1):t._e(),t.addressList.length?s("v-uni-view",{staticClass:"loadingicon acea-row row-center-wrapper"},[s("v-uni-text",{staticClass:"loading iconfont icon-jiazai",attrs:{hidden:0==t.loading}}),t._v(t._s(t.loadTitle))],1):t._e(),t.addressList.length<1&&t.page>1?s("v-uni-view",{staticClass:"noCommodity"},[s("v-uni-view",{staticClass:"pictrue"},[s("v-uni-image",{attrs:{src:i("264a")}})],1)],1):t._e(),s("v-uni-view",{staticStyle:{height:"120rpx"}}),s("v-uni-view",{staticClass:"footer acea-row row-between-wrapper"},[s("v-uni-view",{staticClass:"addressBnt bg-color",class:this.$wechat.isWeixin()?"":"on",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.addAddress.apply(void 0,arguments)}}},[s("v-uni-text",{staticClass:"iconfont icon-tianjiadizhi"}),t._v("添加新地址")],1),this.$wechat.isWeixin()?s("v-uni-view",{staticClass:"addressBnt wxbnt",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.getAddress.apply(void 0,arguments)}}},[s("v-uni-text",{staticClass:"iconfont icon-weixin2"}),t._v("导入微信地址")],1):t._e()],1)],1)],1)},a=[];i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return a})),i.d(e,"a",(function(){return s}))},"264a":function(t,e,i){t.exports=i.p+"static/img/noAddress.d76a1cef.png"},"27de":function(t,e,i){"use strict";i.r(e);var s=i("c9d9"),n=i.n(s);for(var a in s)"default"!==a&&function(t){i.d(e,t,(function(){return s[t]}))}(a);e["default"]=n.a},"41e6":function(t,e,i){"use strict";var s=i("e114"),n=i.n(s);n.a},"5f5b":function(t,e,i){var s=i("24fb");e=s(!1),e.push([t.i,".address-management.fff[data-v-e393fa70]{background-color:#fff;height:%?1300?%}.address-management .line[data-v-e393fa70]{width:100%;height:%?3?%}.address-management .line uni-image[data-v-e393fa70]{width:100%;height:100%;display:block}.address-management .item[data-v-e393fa70]{background-color:#fff;padding:0 %?30?%;margin-bottom:%?12?%}.address-management .item .address[data-v-e393fa70]{padding:%?30?% 0;border-bottom:%?1?% solid #eee;font-size:%?28?%;color:#282828}.address-management .item .address .consignee[data-v-e393fa70]{font-size:%?28?%;font-weight:700;margin-bottom:%?8?%}.address-management .item .address .consignee .phone[data-v-e393fa70]{margin-left:%?25?%}.address-management .item .operation[data-v-e393fa70]{height:%?83?%;font-size:%?28?%;color:#282828}.address-management .item .operation .radio uni-text[data-v-e393fa70]{margin-left:%?13?%}.address-management .item .operation .iconfont[data-v-e393fa70]{color:#2c2c2c;font-size:%?35?%;vertical-align:%?-2?%;margin-right:%?10?%}.address-management .item .operation .iconfont.icon-shanchu[data-v-e393fa70]{margin-left:%?40?%;font-size:%?38?%}.address-management .footer[data-v-e393fa70]{position:fixed;width:100%;background-color:#fff;bottom:0;height:%?106?%;padding:0 %?30?%;box-sizing:border-box}.address-management .footer .addressBnt[data-v-e393fa70]{width:%?330?%;height:%?76?%;border-radius:%?50?%;text-align:center;line-height:%?76?%;font-size:%?30?%;color:#fff}.address-management .footer .addressBnt.on[data-v-e393fa70]{width:%?690?%;margin:0 auto}.address-management .footer .addressBnt .iconfont[data-v-e393fa70]{font-size:%?35?%;margin-right:%?8?%;vertical-align:%?-1?%}.address-management .footer .addressBnt.wxbnt[data-v-e393fa70]{background-color:#fe960f}",""]),t.exports=e},"744d":function(t,e,i){t.exports=i.p+"static/img/line.05bf1c84.jpg"},c9d9:function(t,e,i){"use strict";i("a434"),i("e25e"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var s=i("4654"),n=i("5e22"),a=i("c496"),d=i("2f62"),o=i("79eb"),r={components:{},data:function(){return{addressList:[],cartId:"",pinkId:0,couponId:0,loading:!1,loadend:!1,loadTitle:"加载更多",page:1,limit:20,isAuto:!1,isShowAuth:!1}},computed:(0,d.mapGetters)(["isLogin"]),onLoad:function(t){this.isLogin?(this.cartId=t.cartId||"",this.pinkId=t.pinkId||0,this.couponId=t.couponId||0,(0,s.getWechatConfig)().then((function(t){o.config({debug:!1,appId:t.data.appId,timestamp:t.data.timestamp,nonceStr:t.data.nonceStr,signature:t.data.signature,jsApiList:t.data.jsApiList})})),this.getAddressList(!0)):(0,a.toLogin)()},onShow:function(){var t=this;t.getAddressList(!0)},methods:{onLoadFun:function(){this.isShowAuth=!1,this.getAddressList()},authColse:function(t){this.isShowAuth=t},getWxAddress:function(){var t=this;uni.authorize({scope:"scope.address",success:function(e){uni.chooseAddress({success:function(e){var i={};i.province=e.provinceName,i.city=e.cityName,i.district=e.countyName,(0,n.editAddress)({is_default:1,real_name:e.userName,phone:e.telNumber,detail:e.detailInfo,province:e.provinceName,district:e.countyName,city:e.cityName,city_id:""}).then((function(e){t.$util.Tips({title:"添加成功",icon:"success"},(function(){t.getAddressList(!0)}))})).catch((function(e){return t.$util.Tips({title:e})}))},fail:function(e){if("chooseAddress:cancel"==e.errMsg)return t.$util.Tips({title:"取消选择"})}})},fail:function(e){uni.showModal({title:"您已拒绝导入微信地址权限",content:"是否进入权限管理，调整授权？",success:function(e){if(e.confirm)uni.openSetting({success:function(t){}});else if(e.cancel)return t.$util.Tips({title:"已取消！"})}})}})},getAddress:function(){var t=this;o.openAddress({success:function(e){(0,n.editAddress)({real_name:e.userName,phone:e.telNumber,province:e.provinceName,city:e.cityName,district:e.countryName,detail:e.detailInfo,post_code:e.postalCode,is_default:1,city_id:""}).then((function(){t.$util.Tips({title:"添加成功",icon:"success"},(function(){t.getAddressList(!0)}))})).catch((function(e){return t.$util.Tips({title:e||"添加失败"})}))},fail:function(t){},cancel:function(){},trigger:function(t){}})},getAddressList:function(t){var e=this;t&&(e.loadend=!1,e.page=1,e.$set(e,"addressList",[])),e.loading||e.loadend||(e.loading=!0,e.loadTitle="",(0,n.getAddressList)({page:e.page,limit:e.limit}).then((function(t){var i=t.data.list,s=i.length<e.limit;e.addressList=e.$util.SplitArray(i,e.addressList),e.$set(e,"addressList",e.addressList),e.loadend=s,e.loadTitle=s?"我也是有底线的":"加载更多",e.page=e.page+1,e.loading=!1})).catch((function(t){e.loading=!1,e.loadTitle="加载更多"})))},radioChange:function(t){var e=parseInt(t.detail.value),i=this,s=this.addressList[e];if(void 0==s)return i.$util.Tips({title:"您设置的默认地址不存在!"});(0,n.setAddressDefault)(s.address_id).then((function(t){for(var s=0,n=i.addressList.length;s<n;s++)i.addressList[s].is_default=s==e;i.$util.Tips({title:"设置成功",icon:"success"},(function(){i.$set(i,"addressList",i.addressList)}))})).catch((function(t){return i.$util.Tips({title:t})}))},editAddress:function(t){var e=this.cartId,i=this.pinkId,s=this.couponId;this.cartId="",this.pinkId="",this.couponId="",uni.navigateTo({url:"/pages/users/user_address/index?id="+t+"&cartId="+e+"&pinkId="+i+"&couponId="+s})},delAddress:function(t){var e=this,i=this.addressList[t];if(void 0==i)return e.$util.Tips({title:"您删除的地址不存在!"});(0,n.delAddress)(i.address_id).then((function(i){e.$util.Tips({title:"删除成功",icon:"success"},(function(){e.addressList.splice(t,1),e.$set(e,"addressList",e.addressList)}))})).catch((function(t){return e.$util.Tips({title:t})}))},addAddress:function(){var t=this.cartId;this.pinkId,this.couponId;this.cartId="",this.pinkId="",this.couponId="",uni.navigateTo({url:"/pages/users/user_address/index?cartId="+t})},goOrder:function(t){var e="",i="",s="";this.cartId&&t&&(e=this.cartId,i=this.pinkId,s=this.couponId,this.cartId="",this.pinkId="",this.couponId="",uni.redirectTo({url:"/pages/users/order_confirm/index?is_address=1&cartId="+e+"&addressId="+t+"&pinkId="+i+"&couponId="+s}))}},onReachBottom:function(){this.getAddressList()}};e.default=r},e114:function(t,e,i){var s=i("5f5b");"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var n=i("4f06").default;n("3c13e40a",s,!0,{sourceMap:!1,shadowMode:!1})},fc7f:function(t,e,i){"use strict";i.r(e);var s=i("1bfe"),n=i("27de");for(var a in n)"default"!==a&&function(t){i.d(e,t,(function(){return n[t]}))}(a);i("41e6");var d,o=i("f0c5"),r=Object(o["a"])(n["default"],s["b"],s["c"],!1,null,"e393fa70",null,!1,s["a"],d);e["default"]=r.exports}}]);