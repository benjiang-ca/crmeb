(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-f59a05f0","chunk-2d0da983"],{"0e45":function(e,t,i){},4553:function(e,t,i){"use strict";i.r(t);var o=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",[i("div",{staticClass:"mt20 ml20"},[i("el-input",{staticStyle:{width:"35%"},attrs:{placeholder:"请输入视频连接"},model:{value:e.videoLink,callback:function(t){e.videoLink=t},expression:"videoLink"}}),e._v(" "),i("input",{ref:"refid",staticStyle:{display:"none"},attrs:{type:"file"},on:{change:e.zh_uploadFile_change}}),e._v(" "),i("el-button",{staticClass:"ml10",attrs:{type:"primary",icon:"ios-cloud-upload-outline"},on:{click:e.zh_uploadFile}},[e._v(e._s(e.videoLink?"确认添加":"上传视频"))]),e._v(" "),e.upload.videoIng?i("el-progress",{attrs:{percent:e.progress,"text-inside":!0,"stroke-width":26}}):e._e(),e._v(" "),e.formValidate.video_link?i("div",{staticClass:"iview-video-style"},[i("video",{staticStyle:{width:"100%",height:"100%!important","border-radius":"10px"},attrs:{src:e.formValidate.video_link,controls:"controls"}},[e._v("\n                您的浏览器不支持 video 标签。\n            ")]),e._v(" "),i("div",{staticClass:"mark"}),e._v(" "),i("i",{staticClass:"iconv el-icon-delete",on:{click:e.delVideo}})]):e._e()],1),e._v(" "),i("div",{staticClass:"mt50 ml20"},[i("el-button",{attrs:{type:"primary"},on:{click:e.uploads}},[e._v("确认")])],1)])},n=[],a=(i("7f7f"),i("c4c8")),s=(i("6bef"),{name:"vide11o",data:function(){return{upload:{videoIng:!1},progress:0,videoLink:"",formValidate:{video_link:""}}},methods:{delVideo:function(){var e=this;e.$set(e.formValidate,"video_link","")},zh_uploadFile:function(){this.videoLink?this.formValidate.video_link=this.videoLink:this.$refs.refid.click()},zh_uploadFile_change:function(e){var t=this,i=e.target.files[0].name.substr(e.target.files[0].name.indexOf("."));if(".mp4"!==i)return t.$Message.error("只能上传MP4文件");Object(a["z"])().then((function(i){t.$videoCloud.videoUpload({type:i.data.type,evfile:e,res:i,uploading:function(e,o){t.upload.videoIng=e,console.log(e,o),t.$message.error(i)}}).then((function(e){t.formValidate.video_link=e.url,t.$message.success("视频上传成功")})).catch((function(e){t.$message.error(e)}))}))},uploads:function(){nowEditor.dialog.close(!0),nowEditor.editor.setContent("<video src='"+this.formValidate.video_link+"' controls='controls'></video>",!0)}}}),r=s,l=(i("790d"),i("2877")),d=Object(l["a"])(r,o,n,!1,null,"1cdec4d6",null);t["default"]=d.exports},"6bef":function(e,t,i){"use strict";i.r(t);i("28a5"),i("a481");(function(){if(window.frameElement.id){var e=window.parent,t=e.$EDITORUI[window.frameElement.id.replace(/_iframe$/,"")],i=t.editor,o=e.UE,n=o.dom.domUtils,a=o.utils,s=(o.browser,o.ajax,function(e){return document.getElementById(e)});window.nowEditor={editor:i,dialog:t},a.loadFile(document,{href:i.options.themePath+i.options.theme+"/dialogbase.css?cache="+Math.random(),tag:"link",type:"text/css",rel:"stylesheet"});var r=i.getLang(t.className.split("-")[2]);r&&n.on(window,"load",(function(){var e=i.options.langPath+i.options.lang+"/images/";for(var t in r["static"]){var o=s(t);if(o){var l=o.tagName,d=r["static"][t];switch(d.src&&(d=a.extend({},d,!1),d.src=e+d.src),d.style&&(d=a.extend({},d,!1),d.style=d.style.replace(/url\s*\(/g,"url("+e)),l.toLowerCase()){case"var":o.parentNode.replaceChild(document.createTextNode(d),o);break;case"select":for(var c,u=o.options,v=0;c=u[v];)c.innerHTML=d.options[v++];for(var f in d)"options"!=f&&o.setAttribute(f,d[f]);break;default:n.setAttributes(o,d)}}}}))}})()},"790d":function(e,t,i){"use strict";var o=i("0e45"),n=i.n(o);n.a}}]);