(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2a37144d"],{a650:function(e,t,i){},cb21:function(e,t,i){"use strict";i.r(t);var l=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"divBox"},[i("div",{staticClass:"header clearfix"},[i("div",{staticClass:"filter-container"},[i("div",{staticClass:"demo-input-suffix acea-row"},[e.singleChoice?e._e():i("span",{staticClass:"seachTiele"},[e._v("商品分类：")]),e._v(" "),e.singleChoice?e._e():i("el-select",{staticClass:"filter-item selWidth mr20",attrs:{placeholder:"请选择",clearable:""},on:{change:e.getList},model:{value:e.tableFrom.mer_cate_id,callback:function(t){e.$set(e.tableFrom,"mer_cate_id",t)},expression:"tableFrom.mer_cate_id"}},e._l(e.merCateList,(function(e){return i("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1),e._v(" "),i("span",{staticClass:"seachTiele"},[e._v("商品搜索：")]),e._v(" "),i("el-input",{staticClass:"selWidth",attrs:{placeholder:"请输入商品名称，关键字，产品编号",clearable:""},on:{change:e.getList},model:{value:e.tableFrom.keyword,callback:function(t){e.$set(e.tableFrom,"keyword",t)},expression:"tableFrom.keyword"}},[i("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:e.getList},slot:"append"})],1)],1)])]),e._v(" "),i("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],ref:"table",staticStyle:{width:"100%"},attrs:{data:e.tableData.data,size:"samll","highlight-current-row":""},on:{"selection-change":e.handleSelectionChange}},[1!=e.singleChoice?i("el-table-column",{attrs:{type:"selection",width:"55"}}):e._e(),e._v(" "),1==e.singleChoice?i("el-table-column",{attrs:{width:"50"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-radio",{attrs:{label:t.row.product_id},nativeOn:{change:function(i){return e.getTemplateRow(t.row)}},model:{value:e.templateRadio,callback:function(t){e.templateRadio=t},expression:"templateRadio"}},[e._v(" ")])]}}],null,!1,3465899556)}):e._e(),e._v(" "),i("el-table-column",{attrs:{prop:"product_id",label:"ID","min-width":"50"}}),e._v(" "),i("el-table-column",{attrs:{label:"商品图","min-width":"80"},scopedSlots:e._u([{key:"default",fn:function(e){return[i("div",{staticClass:"demo-image__preview"},[i("el-image",{staticStyle:{width:"36px",height:"36px"},attrs:{src:e.row.image,"preview-src-list":[e.row.image]}})],1)]}}])}),e._v(" "),i("el-table-column",{attrs:{prop:"store_name",label:"商品名称","min-width":"200"}})],1),e._v(" "),i("div",{staticClass:"block mb20"},[i("el-pagination",{attrs:{"page-sizes":[5,10,20],"page-size":e.tableFrom.limit,"current-page":e.tableFrom.page,layout:"total, sizes, prev, pager, next, jumper",total:e.tableData.total},on:{"size-change":e.handleSizeChange,"current-change":e.pageChange}})],1)],1)},a=[],n=(i("ac6a"),i("c4c8")),o=i("83d6"),s={name:"GoodList",data:function(){return{templateRadio:0,idKey:"product_id",merCateList:[],roterPre:o["roterPre"],listLoading:!0,tableData:{data:[],total:0},tableFrom:{page:1,limit:5,mer_cate_id:"",type:"1",is_gift_bag:"",cate_id:"",store_name:"",keyword:""},checked:[],multipleSelection:[],multipleSelectionAll:window.form_create_helper.get(this.$route.query.field)||[],nextPageFlag:!1,singleChoice:0,singleSelection:{}}},mounted:function(){var e=this;if(this.singleChoice=sessionStorage.getItem("singleChoice"),console.log(this.singleChoice),this.getList(),this.getCategorySelect(),1!=this.singleChoice){var t=window.form_create_helper.get(this.$route.query.field).map((function(e){return{product_id:e.id,image:e.src}}))||[];this.multipleSelectionAll=t}window.addEventListener("unload",(function(t){return e.unloadHandler(t)}))},destroyed:function(){sessionStorage.setItem("singleChoice",0)},methods:{getTemplateRow:function(e){this.singleSelection={src:e.image,id:e.product_id}},unloadHandler:function(){1!=this.singleChoice?this.multipleSelectionAll.length>0?this.$route.query.field&&(form_create_helper.set(this.$route.query.field,this.multipleSelectionAll.map((function(e){return{id:e.product_id,src:e.image}}))),form_create_helper.close(this.$route.query.field)):this.$message.warning("请先选择商品"):this.singleSelection&&this.singleSelection.src&&this.singleSelection.id?this.$route.query.field&&(form_create_helper.set(this.$route.query.field,this.singleSelection),form_create_helper.close(this.$route.query.field)):this.$message.warning("请先选择商品")},handleSelectionChange:function(e){var t=this;this.multipleSelection=e,setTimeout((function(){t.changePageCoreRecordData()}),50)},setSelectRow:function(){if(this.multipleSelectionAll&&!(this.multipleSelectionAll.length<=0)){var e=this.idKey,t=[];this.multipleSelectionAll.forEach((function(i){t.push(i[e])})),this.$refs.table.clearSelection();for(var i=0;i<this.tableData.data.length;i++)t.indexOf(this.tableData.data[i][e])>=0&&this.$refs.table.toggleRowSelection(this.tableData.data[i],!0)}},changePageCoreRecordData:function(){var e=this.idKey,t=this;if(this.multipleSelectionAll.length<=0)this.multipleSelectionAll=this.multipleSelection;else{var i=[];this.multipleSelectionAll.forEach((function(t){i.push(t[e])}));var l=[];this.multipleSelection.forEach((function(a){l.push(a[e]),i.indexOf(a[e])<0&&t.multipleSelectionAll.push(a)}));var a=[];this.tableData.data.forEach((function(t){l.indexOf(t[e])<0&&a.push(t[e])})),a.forEach((function(l){if(i.indexOf(l)>=0)for(var a=0;a<t.multipleSelectionAll.length;a++)if(t.multipleSelectionAll[a][e]==l){t.multipleSelectionAll.splice(a,1);break}}))}},getCategorySelect:function(){var e=this;Object(n["f"])().then((function(t){e.merCateList=t.data})).catch((function(t){e.$message.error(t.message)}))},getList:function(){var e=this;this.listLoading=!0,Object(n["p"])(this.tableFrom).then((function(t){e.tableData.data=t.data.list,e.tableData.total=t.data.count,e.$nextTick((function(){this.setSelectRow()})),e.listLoading=!1})).catch((function(t){e.listLoading=!1,e.$message.error(t.message)}))},pageChange:function(e){this.changePageCoreRecordData(),this.tableFrom.page=e,this.getList()},handleSizeChange:function(e){this.changePageCoreRecordData(),this.tableFrom.limit=e,this.getList()}}},r=s,c=(i("d376"),i("2877")),h=Object(c["a"])(r,l,a,!1,null,"6b454c26",null);t["default"]=h.exports},d376:function(e,t,i){"use strict";var l=i("a650"),a=i.n(l);a.a}}]);