(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6dd5d2a6"],{2014:function(t,e,a){},"4c4c":function(t,e,a){"use strict";a.r(e);var l=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"divBox"},[a("el-card",{staticClass:"box-card"},[a("div",{staticClass:"clearfix",attrs:{slot:"header"},slot:"header"},[a("div",{staticClass:"container"},[a("el-form",{attrs:{size:"small","label-width":"100px"}},[a("span",{staticClass:"seachTiele"},[t._v("时间选择：")]),t._v(" "),a("el-radio-group",{staticClass:"mr20",attrs:{type:"button",size:"small",clearable:""},on:{change:function(e){return t.selectChange(t.tableFrom.date)}},model:{value:t.tableFrom.date,callback:function(e){t.$set(t.tableFrom,"date",e)},expression:"tableFrom.date"}},t._l(t.fromList.fromTxt,(function(e,l){return a("el-radio-button",{key:l,attrs:{label:e.val}},[t._v(t._s(e.text))])})),1),t._v(" "),a("el-date-picker",{staticStyle:{width:"250px"},attrs:{"value-format":"yyyy/MM/dd",format:"yyyy/MM/dd",size:"small",type:"daterange",placement:"bottom-end",placeholder:"自定义时间",clearable:""},on:{change:t.onchangeTime},model:{value:t.timeVal,callback:function(e){t.timeVal=e},expression:"timeVal"}}),t._v(" "),a("div",{staticClass:"mt20"},[a("span",{staticClass:"seachTiele"},[t._v("评价分类：")]),t._v(" "),a("el-select",{staticClass:"filter-item selWidth mr20",attrs:{placeholder:"请选择",clearable:""},on:{change:function(e){return t.getList(1)}},model:{value:t.tableFrom.is_reply,callback:function(e){t.$set(t.tableFrom,"is_reply",e)},expression:"tableFrom.is_reply"}},t._l(t.evaluationStatusList,(function(t){return a("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})})),1),t._v(" "),a("span",{staticClass:"seachTiele"},[t._v("商品信息：")]),t._v(" "),a("el-input",{staticClass:"selWidth mr20",attrs:{placeholder:"请输入商品ID或者商品信息",clearable:""},model:{value:t.tableFrom.keyword,callback:function(e){t.$set(t.tableFrom,"keyword",e)},expression:"tableFrom.keyword"}}),t._v(" "),a("span",{staticClass:"seachTiele"},[t._v("用户名称：")]),t._v(" "),a("el-input",{staticClass:"selWidth mr20",attrs:{placeholder:"请输入用户名称"},model:{value:t.tableFrom.nickname,callback:function(e){t.$set(t.tableFrom,"nickname",e)},expression:"tableFrom.nickname"}}),t._v(" "),a("el-button",{attrs:{size:"small",type:"primary",icon:"el-icon-search"},on:{click:function(e){return t.getList(1)}}},[t._v("搜索")])],1)],1)],1)]),t._v(" "),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:t.tableData.data,size:"mini"}},[a("el-table-column",{attrs:{prop:"product_id",label:"商品ID","min-width":"50"}}),t._v(" "),a("el-table-column",{attrs:{label:"商品信息","min-width":"180"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("div",{staticClass:"tabBox acea-row row-middle"},[a("div",{staticClass:"demo-image__preview"},[a("el-image",{attrs:{src:e.row.image,"preview-src-list":[e.row.image]}})],1),t._v(" "),a("span",{staticClass:"tabBox_tit"},[t._v(t._s(e.row.store_name))])])]}}])}),t._v(" "),a("el-table-column",{attrs:{prop:"nickname",label:"用户名称","min-width":"80"}}),t._v(" "),a("el-table-column",{attrs:{prop:"product_score",label:"产品评分","min-width":"50",sortable:""}}),t._v(" "),a("el-table-column",{attrs:{prop:"service_score",label:"服务评分","min-width":"50",sortable:""}}),t._v(" "),a("el-table-column",{attrs:{prop:"postage_score",label:"物流评分","min-width":"50",sortable:""}}),t._v(" "),a("el-table-column",{attrs:{prop:"comment",label:"\t评价内容","min-width":"90"}}),t._v(" "),a("el-table-column",{attrs:{prop:"merchant_reply_content",label:"回复内容","min-width":"100"}}),t._v(" "),a("el-table-column",{attrs:{prop:"create_time",label:"评价时间","min-width":"100",sortable:""}}),t._v(" "),a("el-table-column",{attrs:{label:"操作","min-width":"80",fixed:"right"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-button",{attrs:{type:"text",size:"small"},on:{click:function(a){return t.handleReply(e.row.reply_id)}}},[t._v("回复")])]}}])})],1),t._v(" "),a("div",{staticClass:"block"},[a("el-pagination",{attrs:{"page-sizes":[20,40,60,80],"page-size":t.tableFrom.limit,"current-page":t.tableFrom.page,layout:"total, sizes, prev, pager, next, jumper",total:t.tableData.total},on:{"size-change":t.handleSizeChange,"current-change":t.pageChange}})],1)],1)],1)},i=[],s=a("c4c8"),n={data:function(){return{tableData:{data:[],total:0},listLoading:!0,tableFrom:{is_reply:"",nickname:"",keyword:"",order_sn:"",status:"",date:"",page:1,limit:20},timeVal:[],fromList:{title:"选择时间",custom:!0,fromTxt:[{text:"全部",val:""},{text:"今天",val:"today"},{text:"昨天",val:"yesterday"},{text:"最近7天",val:"lately7"},{text:"最近30天",val:"lately30"},{text:"本月",val:"month"},{text:"本年",val:"year"}]},selectionList:[],ids:"",tableFromLog:{page:1,limit:10},tableDataLog:{data:[],total:0},LogLoading:!1,dialogVisible:!1,evaluationStatusList:[{value:"",label:"全部"},{value:1,label:"已回复"},{value:0,label:"未回复"}],orderDatalist:null}},mounted:function(){this.getList(1)},methods:{handleReply:function(t){var e=this;this.$modalForm(Object(s["t"])(t)).then((function(){return e.getList(1)}))},handleSelectionChange:function(t){this.selectionList=t;var e=[];this.selectionList.map((function(t){e.push(t.id)})),this.ids=e.join(",")},selectChange:function(t){this.tableFrom.date=t,this.timeVal=[],this.getList(1)},onchangeTime:function(t){this.timeVal=t,this.tableFrom.date=t?this.timeVal.join("-"):"",this.getList(1)},getList:function(t){var e=this;this.listLoading=!0,this.tableFrom.page=t||this.tableFrom.page,Object(s["s"])(this.tableFrom).then((function(t){e.tableData.data=t.data.list,e.tableData.total=t.data.count,e.listLoading=!1})).catch((function(t){e.$message.error(t.message),e.listLoading=!1}))},pageChange:function(t){this.tableFrom.page=t,this.getList("")},handleSizeChange:function(t){this.tableFrom.limit=t,this.getList("")}}},o=n,r=(a("8029"),a("2877")),c=Object(r["a"])(o,l,i,!1,null,"7051c4b9",null);e["default"]=c.exports},8029:function(t,e,a){"use strict";var l=a("2014"),i=a.n(l);i.a}}]);