(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-60a77366"],{8695:function(t,e,n){},"8df4c":function(t,e,n){"use strict";var r=n("8695"),a=n.n(r);a.a},c4c8:function(t,e,n){"use strict";n.d(e,"H",(function(){return a})),n.d(e,"F",(function(){return o})),n.d(e,"J",(function(){return c})),n.d(e,"G",(function(){return i})),n.d(e,"I",(function(){return u})),n.d(e,"c",(function(){return s})),n.d(e,"a",(function(){return l})),n.d(e,"e",(function(){return d})),n.d(e,"b",(function(){return f})),n.d(e,"d",(function(){return p})),n.d(e,"h",(function(){return m})),n.d(e,"f",(function(){return g})),n.d(e,"j",(function(){return h})),n.d(e,"g",(function(){return b})),n.d(e,"i",(function(){return _})),n.d(e,"q",(function(){return v})),n.d(e,"B",(function(){return w})),n.d(e,"k",(function(){return y})),n.d(e,"p",(function(){return k})),n.d(e,"A",(function(){return C})),n.d(e,"s",(function(){return L})),n.d(e,"D",(function(){return S})),n.d(e,"n",(function(){return x})),n.d(e,"y",(function(){return F})),n.d(e,"w",(function(){return $})),n.d(e,"u",(function(){return z})),n.d(e,"v",(function(){return D})),n.d(e,"m",(function(){return R})),n.d(e,"o",(function(){return j})),n.d(e,"z",(function(){return q})),n.d(e,"r",(function(){return E})),n.d(e,"C",(function(){return O})),n.d(e,"t",(function(){return T})),n.d(e,"E",(function(){return H})),n.d(e,"l",(function(){return J})),n.d(e,"x",(function(){return N}));var r=n("0c6d");function a(){return r["a"].get("store/category/lst")}function o(){return r["a"].get("store/category/create/form")}function c(t){return r["a"].get("store/category/update/form/".concat(t))}function i(t){return r["a"].delete("store/category/delete/".concat(t))}function u(t,e){return r["a"].post("store/category/status/".concat(t),{status:e})}function s(t){return r["a"].get("store/brand/category/lst",t)}function l(){return r["a"].get("store/brand/category/create/form")}function d(t){return r["a"].get("store/brand/category/update/form/".concat(t))}function f(t){return r["a"].delete("store/brand/category/delete/".concat(t))}function p(t,e){return r["a"].post("store/brand/category/status/".concat(t),{status:e})}function m(t){return r["a"].get("store/brand/lst",t)}function g(){return r["a"].get("store/brand/create/form")}function h(t){return r["a"].get("store/brand/update/form/".concat(t))}function b(t){return r["a"].delete("store/brand/delete/".concat(t))}function _(t,e){return r["a"].post("store/brand/status/".concat(t),{status:e})}function v(t){return r["a"].get("store/product/lst",t)}function w(t){return r["a"].get("seckill/product/lst",t)}function y(){return r["a"].get("store/category/list")}function k(t){return r["a"].get("store/product/detail/".concat(t))}function C(t){return r["a"].get("seckill/product/detail/".concat(t))}function L(t){return r["a"].post("store/product/status",t)}function S(t){return r["a"].post("seckill/product/status",t)}function x(){return r["a"].get("store/product/lst_filter")}function F(){return r["a"].get("seckill/product/lst_filter")}function $(t){return r["a"].get("store/reply/lst",t)}function z(t){return r["a"].get(t?"store/reply/create/form/".concat(t):"store/reply/create/form")}function D(t){return r["a"].delete("store/reply/delete/".concat(t))}function R(t){return r["a"].get("store/product/list",t)}function j(){return r["a"].get("store/product/mer_select")}function q(){return r["a"].get("seckill/product/mer_select")}function E(t){return r["a"].post("store/product/status",t)}function O(t){return r["a"].post("seckill/product/status",t)}function T(t,e){return r["a"].post("store/product/update/".concat(t),e)}function H(t,e){return r["a"].post("seckill/product/update/".concat(t),e)}function J(t,e){return r["a"].post("store/product/change/".concat(t),{status:e})}function N(t,e){return r["a"].post("seckill/product/change/".concat(t),{status:e})}},cb21:function(t,e,n){"use strict";n.r(e);var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"divBox"},[n("div",{staticClass:"header clearfix"},[n("div",{staticClass:"filter-container"},[n("div",{staticClass:"demo-input-suffix acea-row"},[n("el-form",{attrs:{inline:"",size:"small"}},[n("el-form-item",{attrs:{label:"商户分类："}},[n("el-cascader",{staticClass:"selWidth",attrs:{options:t.merCateList,props:t.props,clearable:""},on:{change:function(e){return t.getList(1)}},model:{value:t.tableFrom.cate_id,callback:function(e){t.$set(t.tableFrom,"cate_id",e)},expression:"tableFrom.cate_id"}})],1),t._v(" "),n("el-form-item",{attrs:{label:"商品搜索："}},[n("el-input",{staticClass:"selWidth",attrs:{placeholder:"请输入商品名称，关键字，产品编号"},model:{value:t.tableFrom.store_name,callback:function(e){t.$set(t.tableFrom,"store_name",e)},expression:"tableFrom.store_name"}},[n("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:function(e){return t.getList()}},slot:"append"})],1)],1)],1)],1)])]),t._v(" "),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:t.tableData.data,size:"mini"}},[n("el-table-column",{attrs:{width:"55"},scopedSlots:t._u([{key:"default",fn:function(e){return[n("el-radio",{attrs:{label:e.row.product_id},nativeOn:{change:function(n){return t.getTemplateRow(e.row)}},model:{value:t.templateRadio,callback:function(e){t.templateRadio=e},expression:"templateRadio"}},[t._v(" ")])]}}])}),t._v(" "),n("el-table-column",{attrs:{prop:"product_id",label:"ID","min-width":"50"}}),t._v(" "),n("el-table-column",{attrs:{label:"商品图","min-width":"80"},scopedSlots:t._u([{key:"default",fn:function(t){return[n("div",{staticClass:"demo-image__preview"},[n("el-image",{staticStyle:{width:"36px",height:"36px"},attrs:{src:t.row.image,"preview-src-list":[t.row.image]}})],1)]}}])}),t._v(" "),n("el-table-column",{attrs:{prop:"store_name",label:"商品名称","min-width":"200"}})],1),t._v(" "),n("div",{staticClass:"block mb20"},[n("el-pagination",{attrs:{"page-sizes":[20,40,60,80],"page-size":t.tableFrom.limit,"current-page":t.tableFrom.page,layout:"total, sizes, prev, pager, next, jumper",total:t.tableData.total},on:{"size-change":t.handleSizeChange,"current-change":t.pageChange}})],1)],1)},a=[],o=(n("c5f6"),n("ac6a"),n("c4c8")),c=n("83d6"),i={name:"GoodList",data:function(){return{props:{emitPath:!1},templateRadio:0,merCateList:[],merSelect:[],roterPre:c["roterPre"],listLoading:!0,tableData:{data:[],total:0},tableFrom:{page:1,limit:20,cate_id:""},multipleSelection:{},checked:[]}},mounted:function(){var t=this;this.getList(),this.getCategorySelect(),window.addEventListener("unload",(function(e){return t.unloadHandler(e)}))},methods:{unloadHandler:function(){this.multipleSelection?this.$route.query.field&&this.multipleSelection.src&&this.multipleSelection.id&&(form_create_helper.set(this.$route.query.field,this.multipleSelection),form_create_helper.close(this.$route.query.field)):this.$message.warning("请先选择商品")},getTemplateRow:function(t){this.multipleSelection={src:t.image,id:t.product_id}},getCategorySelect:function(){var t=this;Object(o["k"])().then((function(e){t.merCateList=e.data})).catch((function(e){t.$message.error(e.message)}))},getList:function(){var t=this;this.listLoading=!0,Object(o["m"])(this.tableFrom).then((function(e){t.tableData.data=e.data.list,t.tableData.total=e.data.count,t.checked=window.form_create_helper.get(t.$route.query.field)||[],t.tableData.data.forEach((function(e){t.checked.forEach((function(n){Number(e.product_id)===Number(n.id)&&t.$nextTick((function(){t.$refs.multipleTable.toggleRowSelection(e,!0)}))}))})),t.listLoading=!1})).catch((function(e){t.listLoading=!1,t.$message.error(e.message)}))},pageChange:function(t){this.tableFrom.page=t,this.getList()},handleSizeChange:function(t){this.tableFrom.limit=t,this.getList()}}},u=i,s=(n("8df4c"),n("2877")),l=Object(s["a"])(u,r,a,!1,null,"619976b2",null);e["default"]=l.exports}}]);