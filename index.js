(function () {function d(e,o){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);o&&(r=r.filter(function(o){return Object.getOwnPropertyDescriptor(e,o).enumerable})),n.push.apply(n,r)}return n}function f(e){for(var o=1;o<arguments.length;o++){var n=null!=arguments[o]?arguments[o]:{};o%2?d(Object(n),!0).forEach(function(o){h(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):d(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function h(e,o,n){return o in e?Object.defineProperty(e,o,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[o]=n,e}function g(r,e,t,n,o,a,u){try{var s=r[a](u),i=s.value}catch($){return void t($)}s.done?e(i):Promise.resolve(i).then(n,o)}function j(r){return function(){var e=this,t=arguments;return new Promise(function(n,o){var a=r.apply(e,t);function u(r){g(a,n,o,u,s,"next",r)}function s(r){g(a,n,o,u,s,"throw",r)}u(void 0)})}}var c={props:{providers:{type:Array,default:[]},error:{type:String,default:null}}};if(typeof c==="function"){c=c.options}Object.assign(c,function(){var render=function(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",[_vm.error!==null?_c("div",{staticClass:"k-error-details",staticStyle:{"margin-bottom":"2em"}},[_c("dl",[_c("dt",[_vm._v(_vm._s(_vm.error))])])]):_vm._e(),_vm._v(" "),_vm._m(0),_vm._v(" "),_c("ul",{staticClass:"k-draggable k-list",attrs:{"data-size":"auto"}},_vm._l(_vm.providers,function(provider){return _c("li",{key:provider.id,staticClass:"k-list-item",attrs:{"id":"thathoff-oauth-"+provider.id}},[_c("a",{staticClass:"k-link k-list-item-content",attrs:{"href":provider.href}},[_c("div",{staticClass:"k-list-item-text"},[_vm._v(_vm._s(provider.name))])]),_vm._v(" "),_c("nav",{staticClass:"k-list-item-options"},[_c("a",{staticClass:"k-button",attrs:{"href":provider.href}},[_c("span",{staticClass:"k-button-icon k-icon k-icon-check",attrs:{"aria-hidden":"true"}},[_c("svg",{attrs:{"xmlns:xlink":"http://www.w3.org/1999/xlink","viewBox":"0 0 16 16"}},[_c("use",{attrs:{"xlink:href":"#icon-check"}})])])])])])}),0)])};var staticRenderFns=[function(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("header",{staticClass:"k-field-header"},[_c("div",{staticClass:"k-field-label"},[_vm._v("Sign in with")])])}];return{render:render,staticRenderFns:staticRenderFns,_compiled:true,_scopeId:null,functional:undefined}}());var b={components:{"k-login-form":null,OAuth:c},data:function(){return{settings:{},providers:[],error:null}},created:function(){this.load()},methods:{load:function(){var r=this;return j(regeneratorRuntime.mark(function e(){return regeneratorRuntime.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,r.$api.get("oauth/settings");case 2:return r.settings=e.sent,e.next=5,r.$api.get("oauth/providers");case 5:return r.providers=e.sent,e.next=8,r.$api.get("oauth/oauthError");case 8:r.error=e.sent.msg;case 9:case"end":return e.stop();}},e)}))()}}};if(typeof b==="function"){b=b.options}Object.assign(b,function(){var render=function(){var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c("div",[_vm.settings.enabled===false||_vm.settings.onlyOauth===false?_c("k-login-form"):_vm._e(),_vm._v(" "),_vm.settings.enabled===true?_c("OAuth",{attrs:{"providers":_vm.providers,"error":_vm.error}}):_vm._e()],1)};var staticRenderFns=[];return{render:render,staticRenderFns:staticRenderFns,_compiled:true,_scopeId:null,functional:undefined}}());panel.plugin("thathoff/oauth",{use:[{install:function(e){var o=e.options.components["k-login-view"].options.components["k-login-form"];b.components["k-login-form"]=o,e.options.components["k-login-view"].options.components=f(f({},e.options.components["k-login-view"].options.components),{},{"k-login-form":b})}}]});})();