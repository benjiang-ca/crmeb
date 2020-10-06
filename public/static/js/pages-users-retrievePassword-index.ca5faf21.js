(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-users-retrievePassword-index"],{"172d":function(t,e,a){"use strict";a.r(e);var i=a("2334"),s=a.n(i);for(var n in i)"default"!==n&&function(t){a.d(e,t,(function(){return i[t]}))}(n);e["default"]=s.a},2334:function(t,e,a){"use strict";var i=a("ee27");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,a("96cf");var s=i(a("c964")),n=i(a("4aab")),c=a("5e22"),r={name:"RetrievePassword",data:function(){return{account:"",password:"",captcha:"",keyCode:"",codeUrl:"",codeVal:"",isShowCode:!1}},mixins:[n.default],mounted:function(){this.getCode()},methods:{back:function(){uni.navigateBack()},again:function(){this.codeUrl=VUE_APP_API_URL+"/captcha?"+this.keyCode+Date.parse(new Date)},getCode:function(){var t=this;(0,c.getCodeApi)().then((function(e){t.keyCode=e.data.key})).catch((function(e){t.$dialog.error(e.msg)}))},registerReset:function(){var t=this;return(0,s.default)(regeneratorRuntime.mark((function e(){var a;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(a=t,a.account){e.next=3;break}return e.abrupt("return",a.$util.Tips({title:"请填写手机号码"}));case 3:if(/^1(3|4|5|7|8|9|6)\d{9}$/i.test(a.account)){e.next=5;break}return e.abrupt("return",a.$util.Tips({title:"请输入正确的手机号码"}));case 5:if(a.captcha){e.next=7;break}return e.abrupt("return",a.$util.Tips({title:"请填写验证码"}));case 7:(0,c.registerReset)({account:a.account,captcha:a.captcha,password:a.password,code:a.codeVal}).then((function(t){a.$util.Tips({title:t.msg},{tab:3})})).catch((function(t){a.$util.Tips({title:t})}));case 8:case"end":return e.stop()}}),e)})))()},code:function(){var t=this;return(0,s.default)(regeneratorRuntime.mark((function e(){var a;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(a=t,a.account){e.next=3;break}return e.abrupt("return",a.$util.Tips({title:"请填写手机号码"}));case 3:if(/^1(3|4|5|7|8|9|6)\d{9}$/i.test(a.account)){e.next=5;break}return e.abrupt("return",a.$util.Tips({title:"请输入正确的手机号码"}));case 5:return 2==a.formItem&&(a.type="register"),e.next=8,(0,c.registerVerify)({phone:a.account,type:a.type,key:a.keyCode,code:a.codeVal}).then((function(t){a.$dialog.success(t.msg),a.sendCode()})).catch((function(t){a.$util.Tips({title:t})}));case 8:case"end":return e.stop()}}),e)})))()}}};e.default=r},"4aab":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={data:function(){return{disabled:!1,text:"获取验证码"}},methods:{sendCode:function(){var t=this;if(!this.disabled){this.disabled=!0;var e=60;this.text="剩余 "+e+"s";var a=setInterval((function(){e-=1,e<0&&clearInterval(a),t.text="剩余 "+e+"s",t.text<"剩余 0s"&&(t.disabled=!1,t.text="重新获取")}),1e3)}}}};e.default=i},"4aed":function(t,e,a){"use strict";a.r(e);var i=a("61c2"),s=a("172d");for(var n in s)"default"!==n&&function(t){a.d(e,t,(function(){return s[t]}))}(n);a("86d2");var c,r=a("f0c5"),o=Object(r["a"])(s["default"],i["b"],i["c"],!1,null,"ddbfae14",null,!1,i["a"],c);e["default"]=o.exports},"51f3":function(t,e,a){var i=a("5807");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=a("4f06").default;s("b1f1a784",i,!0,{sourceMap:!1,shadowMode:!1})},5807:function(t,e,a){var i=a("24fb");e=i(!1),e.push([t.i,".code img[data-v-ddbfae14]{width:100%;height:100%}",""]),t.exports=e},"61c2":function(t,e,a){"use strict";var i,s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"register absolute"},[a("div",{staticClass:"shading"},[a("div",{staticClass:"pictrue acea-row row-center-wrapper"},[a("v-uni-image",{attrs:{src:"/static/images/logo2.png"}})],1)]),a("div",{staticClass:"whiteBg"},[a("div",{staticClass:"title"},[t._v("找回密码")]),a("div",{staticClass:"list"},[a("div",{staticClass:"item"},[a("div",{staticClass:"acea-row row-middle"},[a("v-uni-image",{attrs:{src:"/static/images/phone_1.png"}}),a("v-uni-input",{attrs:{type:"text",placeholder:"输入手机号码"},model:{value:t.account,callback:function(e){t.account=e},expression:"account"}})],1)]),a("div",{staticClass:"item"},[a("div",{staticClass:"acea-row row-middle"},[a("v-uni-image",{attrs:{src:"/static/images/code_2.png"}}),a("v-uni-input",{staticClass:"codeIput",attrs:{type:"text",placeholder:"填写验证码"},model:{value:t.captcha,callback:function(e){t.captcha=e},expression:"captcha"}}),a("v-uni-button",{staticClass:"code",class:!0===t.disabled?"on":"",attrs:{disabled:t.disabled},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.code.apply(void 0,arguments)}}},[t._v(t._s(t.text))])],1)]),a("div",{staticClass:"item"},[a("div",{staticClass:"acea-row row-middle"},[a("v-uni-image",{attrs:{src:"/static/images/code_2.png"}}),a("v-uni-input",{attrs:{type:"password",placeholder:"填写您的新密码"},model:{value:t.password,callback:function(e){t.password=e},expression:"password"}})],1)]),t.isShowCode?a("div",{staticClass:"item"},[a("div",{staticClass:"align-left"},[a("v-uni-input",{staticClass:"codeIput",attrs:{type:"text",placeholder:"填写验证码"},model:{value:t.codeVal,callback:function(e){t.codeVal=e},expression:"codeVal"}}),a("div",{staticClass:"code",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.again.apply(void 0,arguments)}}},[a("img",{attrs:{src:t.codeUrl}})])],1)]):t._e()]),a("div",{staticClass:"logon",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.registerReset.apply(void 0,arguments)}}},[t._v("确认")]),a("div",{staticClass:"tip"},[a("span",{staticClass:"font-color-red",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.back.apply(void 0,arguments)}}},[t._v("立即登录")])])]),a("div",{staticClass:"bottom"})])},n=[];a.d(e,"b",(function(){return s})),a.d(e,"c",(function(){return n})),a.d(e,"a",(function(){return i}))},"86d2":function(t,e,a){"use strict";var i=a("51f3"),s=a.n(i);s.a}}]);