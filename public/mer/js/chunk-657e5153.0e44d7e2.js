(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-657e5153"],{"3a81":function(t,e,a){"use strict";var s=a("e69f"),i=a.n(s);i.a},6269:function(t,e,a){},"71bf":function(t,e,a){"use strict";var s=a("cdcf"),i=a.n(s);i.a},"76f2":function(t,e,a){"use strict";var s=a("f729"),i=a.n(s);i.a},7736:function(t,e,a){"use strict";var s=a("6269"),i=a.n(s);i.a},9406:function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"dashboard-container"},[a(t.currentRole,{tag:"component"})],1)},i=[],n=a("db72"),l=a("2f62"),o=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"dashboard-editor-container"},[a("panel-group",{on:{handleSetLineChartData:t.handleSetLineChartData}}),t._v(" "),a("el-row",{staticStyle:{background:"#fff",margin:"20px 0"}},[a("div",{staticClass:"panel-title"},[a("el-col",{attrs:{span:12}},[a("span",[t._v("支付订单")]),t._v(" "),a("i",{staticClass:"el-icon-warning-outline el-icon-weibiaoti"})]),t._v(" "),a("el-col",{staticClass:"align-right",attrs:{span:12}},[a("i",{staticClass:"el-icon-date select-icon-date"}),t._v(" "),a("el-select",{staticClass:"filter-item selWidth",attrs:{placeholder:"请选择","popper-class":"rewriteStyle"},on:{change:function(e){t.getCurrentData(e)}},model:{value:t.time3,callback:function(e){t.time3=e},expression:"time3"}},t._l(t.timeList1,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1)],1)],1),t._v(" "),a("line-chart",{ref:"lineChart",attrs:{"chart-data":t.lineChartData,date:t.time3}})],1),t._v(" "),a("el-row",{staticClass:"panel-warp",staticStyle:{height:"380px"},attrs:{gutter:20}},[a("el-col",{attrs:{xs:24,sm:24,lg:16}},[a("el-row",{staticClass:"panel-title",staticStyle:{background:"#fff",padding:"20px"}},[a("el-col",{attrs:{span:12}},[a("span",[t._v("成交客户")]),t._v(" "),a("i",{staticClass:"el-icon-warning-outline el-icon-weibiaoti"})]),t._v(" "),a("el-col",{staticClass:"align-right",attrs:{span:12}},[a("i",{staticClass:"el-icon-date select-icon-date"}),t._v(" "),a("el-select",{staticClass:"filter-item selWidth",attrs:{placeholder:"请选择"},on:{change:t.getCustomerData},model:{value:t.time1,callback:function(e){t.time1=e},expression:"time1"}},t._l(t.timeList,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1)],1)],1),t._v(" "),a("div",{staticClass:"chart-wrapper"},[a("el-row",{staticStyle:{background:"#fff",height:"410px",padding:"0 20px",position:"relative"}},[a("span",{staticClass:"grid-floating",staticStyle:{position:"absolute"}},[t._v("\n            访客-下单转化率：\n            "),a("span",{staticClass:"grid-conversion-number"},[t._v(t._s(t.orderCustomer.orderRate)+"%")])]),t._v(" "),a("span",{staticClass:"grid-floating"},[t._v("\n            下单-支付转化率：\n            "),a("span",{staticClass:"grid-conversion-number"},[t._v(t._s(t.orderCustomer.payOrderRate)+"%")])]),t._v(" "),a("el-col",{attrs:{span:24}},[a("div",{staticClass:"grid-content"},[a("el-col",{staticClass:"bg-color bg-blue",attrs:{span:18}},[t._v("\n                访客人数\n                "),a("span",{staticClass:"grid-count"},[t._v(t._s(t.orderCustomer.visitUser))])]),t._v(" "),a("el-col",{staticClass:"blue-trapezoid bg-trapezoid",attrs:{span:10}},[a("span",[t._v("访客")])])],1)]),t._v(" "),a("el-col",{attrs:{span:24}},[a("div",{staticClass:"grid-content"},[a("el-col",{staticClass:"bg-color bg-green",attrs:{span:4}},[t._v("\n                下单人数\n                "),a("span",{staticClass:"grid-count"},[t._v(t._s(t.orderCustomer.payOrderUser))])]),t._v(" "),a("el-col",{staticClass:"bg-color bg-green",attrs:{span:4}},[t._v("\n                下单金额\n                "),a("span",{staticClass:"grid-count"},[t._v(t._s(t.orderCustomer.orderPrice))])]),t._v(" "),a("el-col",{staticClass:"bg-color bg-green",staticStyle:{height:"100px"},attrs:{span:8}}),t._v(" "),a("el-col",{staticClass:"green-trapezoid bg-trapezoid",attrs:{span:10}},[a("span",[t._v("下单")])])],1)]),t._v(" "),a("el-col",{attrs:{span:24}},[a("div",{staticClass:"grid-content"},[a("el-col",{staticClass:"bg-color bg-gray-dark",attrs:{span:4}},[t._v("\n                支付人数\n                "),a("span",{staticClass:"grid-count"},[t._v(t._s(t.orderCustomer.payOrderUser))])]),t._v(" "),a("el-col",{staticClass:"bg-color bg-gray-dark",attrs:{span:4}},[t._v("\n                支付金额\n                "),a("span",{staticClass:"grid-count"},[t._v(t._s(t.orderCustomer.payOrderPrice))])]),t._v(" "),a("el-col",{staticClass:"bg-color bg-gray-dark",attrs:{span:4}},[t._v("\n                客单价\n                "),a("span",{staticClass:"grid-count"},[t._v(t._s(t.orderCustomer.userRate))])]),t._v(" "),a("el-col",{staticClass:"bg-color bg-gray-dark",staticStyle:{height:"100px"},attrs:{span:2}}),t._v(" "),a("el-col",{staticClass:"gray-dark-trapezoid bg-trapezoid",attrs:{span:10}},[a("span",[t._v("支付")])])],1)])],1)],1)],1),t._v(" "),a("el-col",{attrs:{xs:24,sm:24,lg:8}},[a("el-row",{staticClass:"panel-title",staticStyle:{background:"#fff",padding:"20px 20px 50px"}},[a("el-col",{attrs:{span:8}},[a("span",[t._v("成交客户占比")]),t._v(" "),a("i",{staticClass:"el-icon-warning-outline el-icon-weibiaoti"})]),t._v(" "),a("el-col",{staticClass:"align-right",attrs:{span:16}},[a("i",{staticClass:"el-icon-date select-icon-date"}),t._v(" "),a("el-select",{staticClass:"filter-item selWidth",attrs:{placeholder:"请选择"},on:{change:function(e){t.getCustomerRatioData(e)}},model:{value:t.time2,callback:function(e){t.time2=e},expression:"time2"}},t._l(t.timeList,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1),t._v(" "),a("el-row",{staticClass:"pieChart-switch"},[a("el-button",{attrs:{type:t.isAmount?"primary":"",value:"金额"},nativeOn:{click:function(e){return t.chooseAmount(e)}}},[t._v("金额")]),t._v(" "),a("el-button",{attrs:{type:t.isAmount?"":"primary",value:"客户数"},nativeOn:{click:function(e){return t.chooseCustomers(e)}}},[t._v("客户数")])],1)],1)],1),t._v(" "),a("div",{staticClass:"chart-wrapper"},[a("pie-chart",{ref:"pieChart",attrs:{amount:t.isAmount,date:t.time2}})],1)],1)],1),t._v(" "),a("el-row",{attrs:{gutter:20}},[a("el-col",{staticStyle:{"margin-bottom":"30px"},attrs:{xs:{span:24},sm:{span:24},md:{span:12},lg:{span:8},xl:{span:8}}},[a("el-row",{staticClass:"panel-title",staticStyle:{background:"#fff"}},[a("el-col",{attrs:{span:8}},[a("span",[t._v("商品支付排行")]),t._v(" "),a("i",{staticClass:"el-icon-warning-outline el-icon-weibiaoti"})]),t._v(" "),a("el-col",{staticClass:"align-right",attrs:{span:16}},[a("i",{staticClass:"el-icon-date select-icon-date"}),t._v(" "),a("el-select",{staticClass:"filter-item selWidth",attrs:{placeholder:"请选择"},on:{change:function(e){t.getRankingData(e)}},model:{value:t.rankingTime1,callback:function(e){t.rankingTime1=e},expression:"rankingTime1"}},t._l(t.timeList,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1)],1)],1),t._v(" "),a("div",{staticClass:"grid-title-count"},[a("el-row",{staticClass:"grid-title"},[a("el-col",{staticStyle:{"padding-left":"20px"},attrs:{span:4}},[t._v("排名")]),t._v(" "),a("el-col",{attrs:{span:16}},[t._v("名称")]),t._v(" "),a("el-col",{attrs:{span:4}},[t._v("支付数")])],1)],1),t._v(" "),a("div",{staticClass:"grid-list-content"},t._l(t.commodityPaymentList,(function(e,s){return a("el-row",{key:s,staticClass:"grid-count"},[a("el-col",{staticClass:"grid-list",attrs:{span:4}},[a("span",{class:s>2?"gray":"navy-blue"},[t._v(t._s(s+1))])]),t._v(" "),a("el-col",{staticClass:"grid-list",attrs:{span:16}},[a("img",{attrs:{src:e.picSrc,alt:""}}),t._v(" "),a("span",[t._v(t._s(e.name))])]),t._v(" "),a("el-col",{staticClass:"grid-list",attrs:{span:4}},[t._v(t._s(e.count))])],1)})),1)],1),t._v(" "),a("el-col",{staticStyle:{"margin-bottom":"30px"},attrs:{xs:{span:24},sm:{span:24},md:{span:12},lg:{span:8},xl:{span:8}}},[a("el-row",{staticClass:"panel-title",staticStyle:{background:"#fff"}},[a("el-col",{attrs:{span:8}},[a("span",[t._v("商品访客排行")]),t._v(" "),a("i",{staticClass:"el-icon-warning-outline el-icon-weibiaoti"})]),t._v(" "),a("el-col",{staticClass:"align-right",attrs:{span:16}},[a("i",{staticClass:"el-icon-date select-icon-date"}),t._v(" "),a("el-select",{staticClass:"filter-item selWidth",attrs:{placeholder:"请选择"},on:{change:function(e){t.getVisitorRankingData(e)}},model:{value:t.rankingTime2,callback:function(e){t.rankingTime2=e},expression:"rankingTime2"}},t._l(t.timeList,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1)],1)],1),t._v(" "),a("div",{staticClass:"grid-title-count"},[a("el-row",{staticClass:"grid-title"},[a("el-col",{staticStyle:{"padding-left":"20px"},attrs:{span:4}},[t._v("排名")]),t._v(" "),a("el-col",{attrs:{span:16}},[t._v("名称")]),t._v(" "),a("el-col",{attrs:{span:4}},[t._v("支付数")])],1)],1),t._v(" "),a("div",{staticClass:"grid-list-content"},t._l(t.visitorRankingList,(function(e,s){return a("el-row",{key:s,staticClass:"grid-count"},[a("el-col",{staticClass:"grid-list",attrs:{span:4}},[a("span",{class:s>2?"gray":"navy-blue"},[t._v(t._s(s+1))])]),t._v(" "),a("el-col",{staticClass:"grid-list",attrs:{span:16}},[a("img",{attrs:{src:e.image,alt:""}}),t._v(" "),a("span",[t._v(t._s(e.store_name))])]),t._v(" "),a("el-col",{staticClass:"grid-list",attrs:{span:4}},[t._v(t._s(e.total))])],1)})),1)],1),t._v(" "),a("el-col",{staticStyle:{"margin-bottom":"30px"},attrs:{xs:{span:24},sm:{span:24},md:{span:12},lg:{span:8},xl:{span:8}}},[a("el-row",{staticClass:"panel-title",staticStyle:{background:"#fff"}},[a("el-col",{attrs:{span:8}},[a("span",[t._v("商品加购排行")]),t._v(" "),a("i",{staticClass:"el-icon-warning-outline el-icon-weibiaoti"})]),t._v(" "),a("el-col",{staticClass:"align-right",attrs:{span:16}},[a("i",{staticClass:"el-icon-date select-icon-date"}),t._v(" "),a("el-select",{staticClass:"filter-item selWidth",attrs:{placeholder:"请选择"},on:{change:function(e){t.getProductPlusData(e)}},model:{value:t.rankingTime3,callback:function(e){t.rankingTime3=e},expression:"rankingTime3"}},t._l(t.timeList,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1)],1)],1),t._v(" "),a("div",{staticClass:"grid-title-count"},[a("el-row",{staticClass:"grid-title"},[a("el-col",{staticStyle:{"padding-left":"20px"},attrs:{span:4}},[t._v("排名")]),t._v(" "),a("el-col",{attrs:{span:16}},[t._v("名称")]),t._v(" "),a("el-col",{attrs:{span:4}},[t._v("支付数")])],1)],1),t._v(" "),a("div",{staticClass:"grid-list-content"},t._l(t.productPlusList,(function(e,s){return a("el-row",{key:s,staticClass:"grid-count"},[a("el-col",{staticClass:"grid-list",attrs:{span:4}},[a("span",{class:s>2?"gray":"navy-blue"},[t._v(t._s(s+1))])]),t._v(" "),a("el-col",{staticClass:"grid-list",attrs:{span:16}},[a("img",{attrs:{src:e.image,alt:""}}),t._v(" "),a("span",[t._v(t._s(e.store_name))])]),t._v(" "),a("el-col",{staticClass:"grid-list",attrs:{span:4}},[t._v(t._s(e.count))])],1)})),1)],1)],1)],1)},r=[],c=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"panel-container"},[a("el-row",{staticClass:"panel-title"},[a("el-col",{attrs:{span:12}},[a("span",{staticStyle:{"font-weight":"bold"}},[t._v("主要数据")])]),t._v(" "),a("el-col",{staticClass:"align-right",staticStyle:{color:"#8c8c8c","font-family":"cursive"},attrs:{span:12}},[t._v(t._s(t.mainData.day))])],1),t._v(" "),a("el-row",{staticClass:"panel-group"},[a("el-col",{staticClass:"card-panel-col el-col-sm-4-8"},[a("div",{staticClass:"card-panel",on:{click:function(e){return t.handleSetLineChartData("newVisitis")}}},[a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("支付订单数")]),t._v(" "),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.mainData.today.orderNum,duration:2600}}),t._v(" "),a("div",{staticClass:"card-panel-compared"},[a("p",[t._v("\n              昨日：\n              "),a("span",[t._v(t._s(t.mainData.yesterday.orderNum))])]),t._v(" "),a("p",[t._v("\n              同比上周\n              "),a("span",{class:{isdecline:t.mainData.lastWeekRate.orderNum>0}},[t._v("\n                "+t._s(t.mainData.lastWeekRate.orderNum)+"%\n                "),a("i")])])])],1)])]),t._v(" "),a("el-col",{staticClass:"card-panel-col el-col-sm-4-8"},[a("div",{staticClass:"card-panel",on:{click:function(e){return t.handleSetLineChartData("messages")}}},[a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("支付金额")]),t._v(" "),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.mainData.today.payPrice,duration:3e3}}),t._v(" "),a("div",{staticClass:"card-panel-compared"},[a("p",[t._v("\n              昨日：\n              "),a("span",[t._v(t._s(t.mainData.yesterday.payPrice))])]),t._v(" "),a("p",[t._v("\n              同比上周\n              "),a("span",{class:{isdecline:t.mainData.lastWeekRate.payPrice>0}},[t._v("\n                "+t._s(t.mainData.lastWeekRate.payPrice)+"%\n                "),a("i")])])])],1)])]),t._v(" "),a("el-col",{staticClass:"card-panel-col el-col-sm-4-8"},[a("div",{staticClass:"card-panel",on:{click:function(e){return t.handleSetLineChartData("purchases")}}},[a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("支付人数")]),t._v(" "),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.mainData.today.payUser,duration:3200}}),t._v(" "),a("div",{staticClass:"card-panel-compared"},[a("p",[t._v("\n              昨日：\n              "),a("span",[t._v(t._s(t.mainData.yesterday.payUser))])]),t._v(" "),a("p",[t._v("\n              同比上周\n              "),a("span",{class:{isdecline:t.mainData.lastWeekRate.payUser>0}},[t._v("\n                "+t._s(t.mainData.lastWeekRate.payUser)+"%\n                "),a("i")])])])],1)])]),t._v(" "),a("el-col",{staticClass:"card-panel-col el-col-sm-4-8"},[a("div",{staticClass:"card-panel",on:{click:function(e){return t.handleSetLineChartData("shoppings")}}},[a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("访客数")]),t._v(" "),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.mainData.today.visitNum,duration:3600}}),t._v(" "),a("div",{staticClass:"card-panel-compared"},[a("p",[t._v("\n              昨日：\n              "),a("span",[t._v(t._s(t.mainData.yesterday.visitNum))])]),t._v(" "),a("p",[t._v("\n              同比上周\n              "),a("span",{class:{isdecline:t.mainData.lastWeekRate.visitNum>0}},[t._v("\n                "+t._s(t.mainData.lastWeekRate.visitNum)+"%\n                "),a("i")])])])],1)])]),t._v(" "),a("el-col",{staticClass:"card-panel-col el-col-sm-4-8"},[a("div",{staticClass:"card-panel",on:{click:function(e){return t.handleSetLineChartData("followers")}}},[a("div",{staticClass:"card-panel-description"},[a("div",{staticClass:"card-panel-text"},[t._v("关注店铺")]),t._v(" "),a("count-to",{staticClass:"card-panel-num",attrs:{"start-val":0,"end-val":t.mainData.today.likeStore,duration:3600}}),t._v(" "),a("div",{staticClass:"card-panel-compared"},[a("p",[t._v("\n              昨日：\n              "),a("span",[t._v(t._s(t.mainData.yesterday.likeStore))])]),t._v(" "),a("p",[t._v("\n              同比上周\n              "),a("span",{class:{isdecline:t.mainData.lastWeekRate.likeStore>0}},[t._v("\n                "+t._s(t.mainData.lastWeekRate.likeStore)+"%\n                "),a("i")])])])],1)])])],1)],1)},d=[],u=a("ec1b"),p=a.n(u),v=a("0c6d");function m(){return v["a"].get("statistics/main")}function h(t){return v["a"].get("statistics/order",t)}function g(t){return v["a"].get("statistics/user",t)}function f(t){return v["a"].get("statistics/user_rate",t)}function _(t){return v["a"].get("statistics/product",t)}function y(t){return v["a"].get("statistics/product_visit",t)}function C(t){return v["a"].get("statistics/product_cart",t)}var b={data:function(){return{pickerOptions:{disabledDate:function(t){return t.getTime()>Date.now()}},value1:"",value2:"",decline:1,mainData:{yesterday:{},today:{},lastWeekRate:{}},today:{},lastWeekRate:{},yesterday:{}}},components:{CountTo:p.a},mounted:function(){this.getMainData()},methods:{handleSetLineChartData:function(t){this.$emit("handleSetLineChartData",t)},getMainData:function(){var t=this;m().then((function(e){t.mainData=e.data})).catch((function(e){t.$message.error(e.message)}))}}},x=b,k=(a("3a81"),a("2877")),w=Object(k["a"])(x,c,d,!1,null,"72a8db14",null),D=w.exports,S=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{class:t.className,style:{height:t.height,width:t.width}})},T=[],$=a("313e"),L=a.n($),E=a("ed08"),R={data:function(){return{$_sidebarElm:null,$_resizeHandler:null}},mounted:function(){var t=this;this.$_resizeHandler=Object(E["b"])((function(){t.chart&&t.chart.resize()}),100),this.$_initResizeEvent(),this.$_initSidebarResizeEvent()},beforeDestroy:function(){this.$_destroyResizeEvent(),this.$_destroySidebarResizeEvent()},activated:function(){this.$_initResizeEvent(),this.$_initSidebarResizeEvent()},deactivated:function(){this.$_destroyResizeEvent(),this.$_destroySidebarResizeEvent()},methods:{$_initResizeEvent:function(){window.addEventListener("resize",this.$_resizeHandler)},$_destroyResizeEvent:function(){window.removeEventListener("resize",this.$_resizeHandler)},$_sidebarResizeHandler:function(t){"width"===t.propertyName&&this.$_resizeHandler()},$_initSidebarResizeEvent:function(){this.$_sidebarElm=document.getElementsByClassName("sidebar-container")[0],this.$_sidebarElm&&this.$_sidebarElm.addEventListener("transitionend",this.$_sidebarResizeHandler)},$_destroySidebarResizeEvent:function(){this.$_sidebarElm&&this.$_sidebarElm.removeEventListener("transitionend",this.$_sidebarResizeHandler)}}};a("817d");var O={mixins:[R],props:{className:{type:String,default:"chart"},width:{type:String,default:"100%"},height:{type:String,default:"350px"},autoResize:{type:Boolean,default:!0},chartData:{type:Object,required:!0},date:{type:String,default:"lately7"}},data:function(){return{chart:null,horizontalAxis:[],PaymentAmount:[],orderNumber:[],user:[]}},watch:{chartData:{deep:!0,handler:function(t){this.setOptions(t)}},date:{deep:!0,handler:function(t){this.date=t;var e={date:this.date};this.getOrderData(e)}}},mounted:function(){var t=this;this.$nextTick((function(){t.initChart()}))},beforeDestroy:function(){this.chart&&(this.chart.dispose(),this.chart=null)},methods:{initChart:function(){this.chart=L.a.init(this.$el,"macarons")},getOrderData:function(t){var e=this,a=this;h(t).then((function(t){a.horizontalAxis.splice(0,a.horizontalAxis.length),a.PaymentAmount.splice(0,a.PaymentAmount.length),a.orderNumber.splice(0,a.orderNumber.length),a.user.splice(0,a.user.length),t.data.map((function(t){a.horizontalAxis.push(t.day),a.PaymentAmount.push(t.pay_price),a.orderNumber.push(t.total),a.user.push(t.user)}));var e=a.horizontalAxis,s=a.PaymentAmount,i=a.orderNumber,n=a.user;a.chart.setOption({xAxis:{data:e,axisLine:{lineStyle:{color:"#dfdfdf"}},boundaryGap:!1,axisTick:{show:!1},axisLabel:{interval:0}},grid:{left:50,right:50,bottom:20,top:60,containLabel:!0},tooltip:{trigger:"axis",axisPointer:{type:"cross"},padding:[5,10]},yAxis:{type:"value",axisLine:{show:!1,lineStyle:{color:"#8c8c8c"}},axisLabel:{formatter:"{value}K"},axisTick:{show:!1}},legend:{data:["订单数","支付金额","支付人数"],left:10},series:[{name:"订单数",markPoint:{data:[{type:"max",name:"峰值"}]},itemStyle:{normal:{color:"#5b8ff9",lineStyle:{color:"#5b8ff9",width:2}}},smooth:!0,type:"line",data:i,animationDuration:2800,animationEasing:"cubicInOut"},{name:"支付金额",smooth:!0,type:"line",markPoint:{data:[{type:"max",name:"峰值"}]},itemStyle:{normal:{color:"#5ad8a6",lineStyle:{color:"#5ad8a6",width:2},areaStyle:{color:"rgba(255,255,255,.4)"}}},data:s,animationDuration:2800,animationEasing:"quadraticOut"},{name:"支付人数",smooth:!0,type:"line",markPoint:{data:[{type:"max",name:"峰值"}]},itemStyle:{normal:{color:"#5d7092",lineStyle:{color:"#5d7092",width:2},areaStyle:{color:"rgba(255,255,255,.4)"}}},data:n,animationDuration:2800,animationEasing:"quadraticOut"}]})})).catch((function(t){e.$message.error(t.message)}))},setOptions:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};t.expectedData,t.actualData,t.payer}}},z=O,P=Object(k["a"])(z,S,T,!1,null,null,null),A=P.exports,N=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{class:t.className,style:{height:t.height,width:t.width}})},W=[];a("817d");var j=3e3,B={mixins:[R],props:{className:{type:String,default:"chart"},width:{type:String,default:"100%"},height:{type:String,default:"300px"}},data:function(){return{chart:null}},mounted:function(){var t=this;this.$nextTick((function(){t.initChart()}))},beforeDestroy:function(){this.chart&&(this.chart.dispose(),this.chart=null)},methods:{initChart:function(){this.chart=L.a.init(this.$el,"macarons"),this.chart.setOption({tooltip:{trigger:"axis",axisPointer:{type:"shadow"}},radar:{radius:"66%",center:["50%","42%"],splitNumber:8,splitArea:{areaStyle:{color:"rgba(127,95,132,.3)",opacity:1,shadowBlur:45,shadowColor:"rgba(0,0,0,.5)",shadowOffsetX:0,shadowOffsetY:15}},indicator:[{name:"Sales",max:1e4},{name:"Administration",max:2e4},{name:"Information Technology",max:2e4},{name:"Customer Support",max:2e4},{name:"Development",max:2e4},{name:"Marketing",max:2e4}]},legend:{left:"center",bottom:"10",data:["Allocated Budget","Expected Spending","Actual Spending"]},series:[{type:"radar",symbolSize:0,areaStyle:{normal:{shadowBlur:13,shadowColor:"rgba(0,0,0,.2)",shadowOffsetX:0,shadowOffsetY:10,opacity:1}},data:[{value:[5e3,7e3,12e3,11e3,15e3,14e3],name:"Allocated Budget"},{value:[4e3,9e3,15e3,15e3,13e3,11e3],name:"Expected Spending"},{value:[5500,11e3,12e3,15e3,12e3,12e3],name:"Actual Spending"}],animationDuration:j}]})}}},U=B,H=Object(k["a"])(U,N,W,!1,null,null,null),V=H.exports,I=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{class:t.className,style:{height:t.height,width:t.width}})},J=[];a("817d");var M={mixins:[R],props:{className:{type:String,default:"chart"},width:{type:String,default:"100%"},height:{type:String,default:"300px"},amount:{type:Boolean,default:!0},date:{type:String,default:"lately7"}},data:function(){return{chart:null,newData:"",oldData:"",Comment:[]}},watch:{amount:{deep:!0,handler:function(t){this.amount=t,this.getTurnoverRatio()}},date:{deep:!0,handler:function(t){this.date=t,this.getTurnoverRatio()}}},mounted:function(){this.$nextTick((function(){}))},beforeDestroy:function(){this.chart&&(this.chart.dispose(),this.chart=null)},methods:{getTurnoverRatio:function(){var t=this;f({date:this.date}).then((function(e){t.orderCustomer=e.data,t.newData=t.amount?e.data.newTotalPrice:e.data.newUser,t.oldData=t.amount?e.data.oldTotalPrice:e.data.oldUser,t.chart=L.a.init(t.$el,"macarons"),t.chart.setOption({title:{text:"成交金额占比",left:"center",textStyle:{fontSize:14,fontStyle:"normal",fontWeight:"bold",color:"#262626"}},tooltip:{trigger:"item",formatter:"{a} <br/>{b} : {c} ({d}%)"},legend:{bottom:0,left:"center",data:["新成交客户","老客户"]},series:[{name:"成交比",type:"pie",radius:"55%",center:["50%","50%"],avoidLabelOverlap:!1,label:{show:!1,position:"center"},emphasis:{label:{show:!0,fontSize:"20",fontWeight:"bold"}},labelLine:{show:!1},data:[{value:t.newData,name:"新成交客户",itemStyle:{color:"#39a1ff"}},{value:t.oldData,name:"老客户",itemStyle:{color:"#4ecb73"}}],animationEasing:"cubicInOut",animationDuration:2600}]})})).catch((function(e){t.$message.error(e.message)}))}}},q=M,F=Object(k["a"])(q,I,J,!1,null,null,null),G=F.exports,X=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{class:t.className,style:{height:t.height,width:t.width}})},Y=[];a("817d");var K=6e3,Q={mixins:[R],props:{className:{type:String,default:"chart"},width:{type:String,default:"100%"},height:{type:String,default:"300px"}},data:function(){return{chart:null}},mounted:function(){var t=this;this.$nextTick((function(){t.initChart()}))},beforeDestroy:function(){this.chart&&(this.chart.dispose(),this.chart=null)},methods:{initChart:function(){this.chart=L.a.init(this.$el,"macarons"),this.chart.setOption({tooltip:{trigger:"axis",axisPointer:{type:"shadow"}},grid:{top:10,left:"2%",right:"2%",bottom:"3%",containLabel:!0},xAxis:[{type:"category",data:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],axisTick:{alignWithLabel:!0}}],yAxis:[{type:"value",axisTick:{show:!1}}],series:[{name:"pageA",type:"bar",stack:"vistors",barWidth:"60%",data:[79,52,200,334,390,330,220],animationDuration:K},{name:"pageB",type:"bar",stack:"vistors",barWidth:"60%",data:[80,52,200,334,390,330,220],animationDuration:K},{name:"pageC",type:"bar",stack:"vistors",barWidth:"60%",data:[30,52,200,334,390,330,220],animationDuration:K}]})}}},Z=Q,tt=Object(k["a"])(Z,X,Y,!1,null,null,null),et=tt.exports,at=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("section",{staticClass:"todoapp"},[a("header",{staticClass:"header"},[a("input",{staticClass:"new-todo",attrs:{autocomplete:"off",placeholder:"Todo List"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.addTodo(e)}}})]),t._v(" "),a("section",{directives:[{name:"show",rawName:"v-show",value:t.todos.length,expression:"todos.length"}],staticClass:"main"},[a("input",{staticClass:"toggle-all",attrs:{id:"toggle-all",type:"checkbox"},domProps:{checked:t.allChecked},on:{change:function(e){return t.toggleAll({done:!t.allChecked})}}}),t._v(" "),a("label",{attrs:{for:"toggle-all"}}),t._v(" "),a("ul",{staticClass:"todo-list"},t._l(t.filteredTodos,(function(e,s){return a("todo",{key:s,attrs:{todo:e},on:{toggleTodo:t.toggleTodo,editTodo:t.editTodo,deleteTodo:t.deleteTodo}})})),1)]),t._v(" "),a("footer",{directives:[{name:"show",rawName:"v-show",value:t.todos.length,expression:"todos.length"}],staticClass:"footer"},[a("span",{staticClass:"todo-count"},[a("strong",[t._v(t._s(t.remaining))]),t._v("\n      "+t._s(t._f("pluralize")(t.remaining,"item"))+" left\n    ")]),t._v(" "),a("ul",{staticClass:"filters"},t._l(t.filters,(function(e,s){return a("li",{key:s},[a("a",{class:{selected:t.visibility===s},on:{click:function(e){e.preventDefault(),t.visibility=s}}},[t._v(t._s(t._f("capitalize")(s)))])])})),0)])])},st=[],it=(a("ac6a"),function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("li",{staticClass:"todo",class:{completed:t.todo.done,editing:t.editing}},[a("div",{staticClass:"view"},[a("input",{staticClass:"toggle",attrs:{type:"checkbox"},domProps:{checked:t.todo.done},on:{change:function(e){return t.toggleTodo(t.todo)}}}),t._v(" "),a("label",{domProps:{textContent:t._s(t.todo.text)},on:{dblclick:function(e){t.editing=!0}}}),t._v(" "),a("button",{staticClass:"destroy",on:{click:function(e){return t.deleteTodo(t.todo)}}})]),t._v(" "),a("input",{directives:[{name:"show",rawName:"v-show",value:t.editing,expression:"editing"},{name:"focus",rawName:"v-focus",value:t.editing,expression:"editing"}],staticClass:"edit",domProps:{value:t.todo.text},on:{keyup:[function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.doneEdit(e)},function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"esc",27,e.key,["Esc","Escape"])?null:t.cancelEdit(e)}],blur:t.doneEdit}})])}),nt=[],lt={name:"Todo",directives:{focus:function(t,e,a){var s=e.value,i=a.context;s&&i.$nextTick((function(){t.focus()}))}},props:{todo:{type:Object,default:function(){return{}}}},data:function(){return{editing:!1}},methods:{deleteTodo:function(t){this.$emit("deleteTodo",t)},editTodo:function(t){var e=t.todo,a=t.value;this.$emit("editTodo",{todo:e,value:a})},toggleTodo:function(t){this.$emit("toggleTodo",t)},doneEdit:function(t){var e=t.target.value.trim(),a=this.todo;e?this.editing&&(this.editTodo({todo:a,value:e}),this.editing=!1):this.deleteTodo({todo:a})},cancelEdit:function(t){t.target.value=this.todo.text,this.editing=!1}}},ot=lt,rt=Object(k["a"])(ot,it,nt,!1,null,null,null),ct=rt.exports,dt="todos",ut={all:function(t){return t},active:function(t){return t.filter((function(t){return!t.done}))},completed:function(t){return t.filter((function(t){return t.done}))}},pt=[{text:"star this repository",done:!1},{text:"fork this repository",done:!1},{text:"follow author",done:!1},{text:"vue-element-admin",done:!0},{text:"vue",done:!0},{text:"element-ui",done:!0},{text:"axios",done:!0},{text:"webpack",done:!0}],vt={components:{Todo:ct},filters:{pluralize:function(t,e){return 1===t?e:e+"s"},capitalize:function(t){return t.charAt(0).toUpperCase()+t.slice(1)}},data:function(){return{visibility:"all",filters:ut,todos:pt}},computed:{allChecked:function(){return this.todos.every((function(t){return t.done}))},filteredTodos:function(){return ut[this.visibility](this.todos)},remaining:function(){return this.todos.filter((function(t){return!t.done})).length}},methods:{setLocalStorage:function(){window.localStorage.setItem(dt,JSON.stringify(this.todos))},addTodo:function(t){var e=t.target.value;e.trim()&&(this.todos.push({text:e,done:!1}),this.setLocalStorage()),t.target.value=""},toggleTodo:function(t){t.done=!t.done,this.setLocalStorage()},deleteTodo:function(t){this.todos.splice(this.todos.indexOf(t),1),this.setLocalStorage()},editTodo:function(t){var e=t.todo,a=t.value;e.text=a,this.setLocalStorage()},clearCompleted:function(){this.todos=this.todos.filter((function(t){return!t.done})),this.setLocalStorage()},toggleAll:function(t){var e=this,a=t.done;this.todos.forEach((function(t){t.done=a,e.setLocalStorage()}))}}},mt=vt,ht=(a("76f2"),Object(k["a"])(mt,at,st,!1,null,null,null)),gt=ht.exports,ft=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("el-card",{staticClass:"box-card-component",staticStyle:{"margin-left":"8px"}},[a("div",{staticClass:"box-card-header",attrs:{slot:"header"},slot:"header"},[a("img",{attrs:{src:"https://wpimg.wallstcn.com/e7d23d71-cf19-4b90-a1cc-f56af8c0903d.png"}})]),t._v(" "),a("div",{staticStyle:{position:"relative"}},[a("pan-thumb",{staticClass:"panThumb",attrs:{image:t.avatar}}),t._v(" "),a("mallki",{attrs:{"class-name":"mallki-text",text:"vue-element-admin"}}),t._v(" "),a("div",{staticClass:"progress-item",staticStyle:{"padding-top":"35px"}},[a("span",[t._v("Vue")]),t._v(" "),a("el-progress",{attrs:{percentage:70}})],1),t._v(" "),a("div",{staticClass:"progress-item"},[a("span",[t._v("JavaScript")]),t._v(" "),a("el-progress",{attrs:{percentage:18}})],1),t._v(" "),a("div",{staticClass:"progress-item"},[a("span",[t._v("Css")]),t._v(" "),a("el-progress",{attrs:{percentage:12}})],1),t._v(" "),a("div",{staticClass:"progress-item"},[a("span",[t._v("ESLint")]),t._v(" "),a("el-progress",{attrs:{percentage:100,status:"success"}})],1)],1)])},_t=[],yt={filters:{statusFilter:function(t){var e={success:"success",pending:"danger"};return e[t]}},data:function(){return{statisticsData:{article_count:1024,pageviews_count:1024}}},computed:Object(n["a"])({},Object(l["b"])(["name","avatar","roles"]))},Ct=yt,bt=(a("7736"),a("bee3"),Object(k["a"])(Ct,ft,_t,!1,null,"25810eea",null)),xt=bt.exports,kt={newVisitis:{expectedData:[100,120,161,134,105,160,165],actualData:[120,82,91,154,162,140,145],payer:[100,120,98,130,150,140,180]},messages:{expectedData:[200,192,120,144,160,130,140],actualData:[180,160,151,106,145,150,130],payer:[150,90,98,130,150,140,180]},purchases:{expectedData:[80,100,121,104,105,90,100],actualData:[120,90,100,138,142,130,130],payer:[150,90,98,130,150,140,180]},shoppings:{expectedData:[130,140,141,142,145,150,160],actualData:[120,82,91,154,162,140,130],payer:[150,90,98,130,150,140,180]},followers:{expectedData:[150,90,98,130,150,140,180],actualData:[120,82,91,154,162,140,130],payer:[130,140,141,142,145,150,160]}},wt={name:"DashboardAdmin",components:{PanelGroup:D,LineChart:A,RaddarChart:V,PieChart:G,BarChart:et,TodoList:gt,BoxCard:xt},data:function(){return{value1:"",value2:"",time1:"lately7",time2:"lately7",time3:"lately7",rankingTime1:"lately7",rankingTime2:"lately7",rankingTime3:"lately7",lineChartData:kt.newVisitis,isAmount:!0,timeList:[{value:"lately7",label:"近7天"},{value:"lately30",label:"近30天"},{value:"month",label:"本月"},{value:"year",label:"本年"}],timeList1:[{value:"lately7",label:"近7天"},{value:"lately30",label:"近30天"},{value:"month",label:"本月"}],commodityPaymentList:[],visitorRankingList:[],productPlusList:[],orderCustomer:{}}},mounted:function(){this.getCurrentData({date:this.time3}),this.getCustomerData({date:this.time1}),this.getCustomerRatioData({date:this.time2}),this.getRankingData(this.rankingTime1),this.getVisitorRankingData(this.rankingTime2),this.getProductPlusData(this.rankingTime3)},methods:{chooseAmount:function(){this.isAmount||(this.isAmount=!0)},chooseCustomers:function(){this.isAmount&&(this.isAmount=!1)},handleSetLineChartData:function(t){this.lineChartData=kt[t]},getCurrentData:function(t){this.$refs.lineChart.getOrderData({date:this.time3})},getCustomerData:function(t){var e=this,a={date:t};g(a).then((function(t){e.orderCustomer=t.data})).catch((function(t){e.$message.error(t.message)}))},getCustomerRatioData:function(t){this.$refs.pieChart.getTurnoverRatio()},getRankingData:function(t){var e=this,a={date:t};_(a).then((function(t){e.commodityPaymentList.length=0,t.data.map((function(t){e.commodityPaymentList.push({name:t.cart_info.product.store_name,picSrc:t.cart_info.product.image,count:t.total})}))})).catch((function(t){e.$message.error(t.message)}))},getVisitorRankingData:function(t){var e=this,a={date:t};y(a).then((function(t){e.visitorRankingList=t.data})).catch((function(t){e.$message.error(t.message)}))},getProductPlusData:function(t){var e=this,a={date:t};C(a).then((function(t){e.productPlusList=t.data})).catch((function(t){e.$message.error(t.message)}))}}},Dt=wt,St=(a("71bf"),Object(k["a"])(Dt,o,r,!1,null,"4672adb4",null)),Tt=St.exports,$t={name:"Dashboard",components:{adminDashboard:Tt},data:function(){return{currentRole:"adminDashboard"}},computed:Object(n["a"])({},Object(l["b"])(["roles"])),created:function(){}},Lt=$t,Et=Object(k["a"])(Lt,s,i,!1,null,null,null);e["default"]=Et.exports},bee3:function(t,e,a){"use strict";var s=a("c6d3"),i=a.n(s);i.a},c6d3:function(t,e,a){},cdcf:function(t,e,a){},e69f:function(t,e,a){},f729:function(t,e,a){}}]);