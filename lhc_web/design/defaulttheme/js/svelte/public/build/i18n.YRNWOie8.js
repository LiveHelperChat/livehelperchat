function t(){}function e(t,e){for(const n in e)t[n]=e[n];return t}function n(t){return t()}function o(){return Object.create(null)}function s(t){t.forEach(n)}function r(t){return"function"==typeof t}function i(t,e){return t!=t?e==e:t!==e||t&&"object"==typeof t||"function"==typeof t}let c,u;function a(t,e){return t===e||(c||(c=document.createElement("a")),c.href=e,t===c.href)}function d(e,...n){if(null==e){for(const t of n)t(void 0);return t}const o=e.subscribe(...n);return o.unsubscribe?()=>o.unsubscribe():o}function l(t,e,n){t.$$.on_destroy.push(d(e,n))}function $(t){const e={};for(const n in t)"$"!==n[0]&&(e[n]=t[n]);return e}function f(t,e,n){return t.set(n),e}function p(e){return e&&r(e.destroy)?e.destroy:t}function h(t,e){t.appendChild(e)}function m(t,e,n){const o=function(t){if(!t)return document;const e=t.getRootNode?t.getRootNode():t.ownerDocument;if(e&&e.host)return e;return t.ownerDocument}(t);if(!o.getElementById(e)){const t=y("style");t.id=e,t.textContent=n,function(t,e){h(t.head||t,e),e.sheet}(o,t)}}function g(t,e,n){t.insertBefore(e,n||null)}function _(t){t.parentNode&&t.parentNode.removeChild(t)}function b(t,e){for(let n=0;n<t.length;n+=1)t[n]&&t[n].d(e)}function y(t){return document.createElement(t)}function v(t){return document.createTextNode(t)}function w(){return v(" ")}function E(){return v("")}function x(t,e,n,o){return t.addEventListener(e,n,o),()=>t.removeEventListener(e,n,o)}function k(t,e,n){null==n?t.removeAttribute(e):t.getAttribute(e)!==n&&t.setAttribute(e,n)}function N(t,e,n){const o=new Set;for(let e=0;e<t.length;e+=1)t[e].checked&&o.add(t[e].__value);return n||o.delete(e),Array.from(o)}function j(t){let e;return{p(...n){e=n,e.forEach((e=>t.push(e)))},r(){e.forEach((e=>t.splice(t.indexOf(e),1)))}}}function A(t,e){e=""+e,t.data!==e&&(t.data=e)}function L(t,e){t.value=null==e?"":e}function C(t,e,n,o){null==n?t.style.removeProperty(e):t.style.setProperty(e,n,o?"important":"")}function O(t,e,n){for(let n=0;n<t.options.length;n+=1){const o=t.options[n];if(o.__value===e)return void(o.selected=!0)}n&&void 0===e||(t.selectedIndex=-1)}function S(t){const e=t.querySelector(":checked");return e&&e.__value}function P(t,e,n){t.classList.toggle(e,!!n)}function T(t){u=t}function D(){if(!u)throw new Error("Function called outside component initialization");return u}function I(t){D().$$.on_mount.push(t)}function M(){const t=D();return(e,n,{cancelable:o=!1}={})=>{const s=t.$$.callbacks[e];if(s){const r=function(t,e,{bubbles:n=!1,cancelable:o=!1}={}){return new CustomEvent(t,{detail:e,bubbles:n,cancelable:o})}(e,n,{cancelable:o});return s.slice().forEach((e=>{e.call(t,r)})),!r.defaultPrevented}return!0}}const R=[],B=[];let H=[];const F=[],G=Promise.resolve();let W=!1;function z(t){H.push(t)}const J=new Set;let q=0;function V(){if(0!==q)return;const t=u;do{try{for(;q<R.length;){const t=R[q];q++,T(t),X(t.$$)}}catch(t){throw R.length=0,q=0,t}for(T(null),R.length=0,q=0;B.length;)B.pop()();for(let t=0;t<H.length;t+=1){const e=H[t];J.has(e)||(J.add(e),e())}H.length=0}while(R.length);for(;F.length;)F.pop()();W=!1,J.clear(),T(t)}function X(t){if(null!==t.fragment){t.update(),s(t.before_update);const e=t.dirty;t.dirty=[-1],t.fragment&&t.fragment.p(t.ctx,e),t.after_update.forEach(z)}}const K=new Set;let Q,U;function Y(){Q={r:0,c:[],p:Q}}function Z(){Q.r||s(Q.c),Q=Q.p}function tt(t,e){t&&t.i&&(K.delete(t),t.i(e))}function et(t,e,n,o){if(t&&t.o){if(K.has(t))return;K.add(t),Q.c.push((()=>{K.delete(t),o&&(n&&t.d(1),o())})),t.o(e)}else o&&o()}function nt(t){return void 0!==t?.length?t:Array.from(t)}function ot(t,e){t.d(1),e.delete(t.key)}function st(t,e,n,o,r,i,c,u,a,d,l,$){let f=t.length,p=i.length,h=f;const m={};for(;h--;)m[t[h].key]=h;const g=[],_=new Map,b=new Map,y=[];for(h=p;h--;){const t=$(r,i,h),s=n(t);let u=c.get(s);u?o&&y.push((()=>u.p(t,e))):(u=d(s,t),u.c()),_.set(s,g[h]=u),s in m&&b.set(s,Math.abs(h-m[s]))}const v=new Set,w=new Set;function E(t){tt(t,1),t.m(u,l),c.set(t.key,t),l=t.first,p--}for(;f&&p;){const e=g[p-1],n=t[f-1],o=e.key,s=n.key;e===n?(l=e.first,f--,p--):_.has(s)?!c.has(o)||v.has(o)?E(e):w.has(s)?f--:b.get(o)>b.get(s)?(w.add(o),E(e)):(v.add(s),f--):(a(n,c),f--)}for(;f--;){const e=t[f];_.has(e.key)||a(e,c)}for(;p;)E(g[p-1]);return s(y),g}function rt(t){t&&t.c()}function it(t,e,o){const{fragment:i,after_update:c}=t.$$;i&&i.m(e,o),z((()=>{const e=t.$$.on_mount.map(n).filter(r);t.$$.on_destroy?t.$$.on_destroy.push(...e):s(e),t.$$.on_mount=[]})),c.forEach(z)}function ct(t,e){const n=t.$$;null!==n.fragment&&(!function(t){const e=[],n=[];H.forEach((o=>-1===t.indexOf(o)?e.push(o):n.push(o))),n.forEach((t=>t())),H=e}(n.after_update),s(n.on_destroy),n.fragment&&n.fragment.d(e),n.on_destroy=n.fragment=null,n.ctx=[])}function ut(t,e){-1===t.$$.dirty[0]&&(R.push(t),W||(W=!0,G.then(V)),t.$$.dirty.fill(0)),t.$$.dirty[e/31|0]|=1<<e%31}function at(e,n,r,i,c,a,d=null,l=[-1]){const $=u;T(e);const f=e.$$={fragment:null,ctx:[],props:a,update:t,not_equal:c,bound:o(),on_mount:[],on_destroy:[],on_disconnect:[],before_update:[],after_update:[],context:new Map(n.context||($?$.$$.context:[])),callbacks:o(),dirty:l,skip_bound:!1,root:n.target||$.$$.root};d&&d(f.root);let p=!1;if(f.ctx=r?r(e,n.props||{},((t,n,...o)=>{const s=o.length?o[0]:n;return f.ctx&&c(f.ctx[t],f.ctx[t]=s)&&(!f.skip_bound&&f.bound[t]&&f.bound[t](s),p&&ut(e,t)),n})):[],f.update(),p=!0,s(f.before_update),f.fragment=!!i&&i(f.ctx),n.target){if(n.hydrate){const t=function(t){return Array.from(t.childNodes)}(n.target);f.fragment&&f.fragment.l(t),t.forEach(_)}else f.fragment&&f.fragment.c();n.intro&&tt(e.$$.fragment),it(e,n.target,n.anchor),V()}T($)}function dt(t,e,n,o){const s=n[t]?.type;if(e="Boolean"===s&&"boolean"!=typeof e?null!=e:e,!o||!n[t])return e;if("toAttribute"===o)switch(s){case"Object":case"Array":return null==e?null:JSON.stringify(e);case"Boolean":return e?"":null;case"Number":return null==e?null:e;default:return e}else switch(s){case"Object":case"Array":return e&&JSON.parse(e);case"Boolean":default:return e;case"Number":return null!=e?+e:e}}function lt(t,e,n,o,s,r){let i=class extends U{constructor(){super(t,n,s),this.$$p_d=e}static get observedAttributes(){return Object.keys(e).map((t=>(e[t].attribute||t).toLowerCase()))}};return Object.keys(e).forEach((t=>{Object.defineProperty(i.prototype,t,{get(){return this.$$c&&t in this.$$c?this.$$c[t]:this.$$d[t]},set(n){n=dt(t,n,e),this.$$d[t]=n,this.$$c?.$set({[t]:n})}})})),o.forEach((t=>{Object.defineProperty(i.prototype,t,{get(){return this.$$c?.[t]}})})),r&&(i=r(i)),t.element=i,i}"function"==typeof HTMLElement&&(U=class extends HTMLElement{$$ctor;$$s;$$c;$$cn=!1;$$d={};$$r=!1;$$p_d={};$$l={};$$l_u=new Map;constructor(t,e,n){super(),this.$$ctor=t,this.$$s=e,n&&this.attachShadow({mode:"open"})}addEventListener(t,e,n){if(this.$$l[t]=this.$$l[t]||[],this.$$l[t].push(e),this.$$c){const n=this.$$c.$on(t,e);this.$$l_u.set(e,n)}super.addEventListener(t,e,n)}removeEventListener(t,e,n){if(super.removeEventListener(t,e,n),this.$$c){const t=this.$$l_u.get(e);t&&(t(),this.$$l_u.delete(e))}}async connectedCallback(){if(this.$$cn=!0,!this.$$c){if(await Promise.resolve(),!this.$$cn)return;function t(t){return()=>{let e;return{c:function(){e=y("slot"),"default"!==t&&k(e,"name",t)},m:function(t,n){g(t,e,n)},d:function(t){t&&_(e)}}}}const e={},n=function(t){const e={};return t.childNodes.forEach((t=>{e[t.slot||"default"]=!0})),e}(this);for(const s of this.$$s)s in n&&(e[s]=[t(s)]);for(const r of this.attributes){const i=this.$$g_p(r.name);i in this.$$d||(this.$$d[i]=dt(i,r.value,this.$$p_d,"toProp"))}this.$$c=new this.$$ctor({target:this.shadowRoot||this,props:{...this.$$d,$$slots:e,$$scope:{ctx:[]}}});const o=()=>{this.$$r=!0;for(const t in this.$$p_d)if(this.$$d[t]=this.$$c.$$.ctx[this.$$c.$$.props[t]],this.$$p_d[t].reflect){const e=dt(t,this.$$d[t],this.$$p_d,"toAttribute");null==e?this.removeAttribute(this.$$p_d[t].attribute||t):this.setAttribute(this.$$p_d[t].attribute||t,e)}this.$$r=!1};this.$$c.$$.after_update.push(o),o();for(const c in this.$$l)for(const u of this.$$l[c]){const a=this.$$c.$on(c,u);this.$$l_u.set(u,a)}this.$$l={}}}attributeChangedCallback(t,e,n){this.$$r||(t=this.$$g_p(t),this.$$d[t]=dt(t,n,this.$$p_d,"toProp"),this.$$c?.$set({[t]:this.$$d[t]}))}disconnectedCallback(){this.$$cn=!1,Promise.resolve().then((()=>{this.$$cn||(this.$$c.$destroy(),this.$$c=void 0)}))}$$g_p(t){return Object.keys(this.$$p_d).find((e=>this.$$p_d[e].attribute===t||!this.$$p_d[e].attribute&&e.toLowerCase()===t))||t}});class $t{$$=void 0;$$set=void 0;$destroy(){ct(this,1),this.$destroy=t}$on(e,n){if(!r(n))return t;const o=this.$$.callbacks[e]||(this.$$.callbacks[e]=[]);return o.push(n),()=>{const t=o.indexOf(n);-1!==t&&o.splice(t,1)}}$set(t){var e;this.$$set&&(e=t,0!==Object.keys(e).length)&&(this.$$.skip_bound=!0,this.$$set(t),this.$$.skip_bound=!1)}}"undefined"!=typeof window&&(window.__svelte||(window.__svelte={v:new Set})).v.add("4");const ft=[];function pt(e,n=t){let o;const s=new Set;function r(t){if(i(e,t)&&(e=t,o)){const t=!ft.length;for(const t of s)t[1](),ft.push(t,e);if(t){for(let t=0;t<ft.length;t+=2)ft[t][0](ft[t+1]);ft.length=0}}}function c(t){r(t(e))}return{set:r,update:c,subscribe:function(i,u=t){const a=[i,u];return s.add(a),1===s.size&&(o=n(r,c)||t),i(e),()=>{s.delete(a),0===s.size&&o&&(o(),o=null)}}}}const ht=pt({onlineusers:{list:[]},onlineusersGrouped:[],onlineusers_tt:0,department_online:[],department_online_dpgroups:[],department_onlineNames:[],lhcCoreLoaded:!1,lhcVersion:0,lhcNotice:{message:"",level:"primary"},last_actions_index:0,last_actions:[],userDepartments:[],userProductNames:[],userDepartmentsGroups:[],userGroups:[],userList:[],widgets:[],additionalColumns:[],excludeIcons:[],notifIcons:[],departmentd:[],departmentd_dpgroups:[],departmentdNames:[],operatord:[],operatord_dpgroups:[],operatord_ugroups:[],operatordNames:[],actived:[],actived_products:[],actived_dpgroups:[],actived_ugroups:[],activedNames:[],mcd:[],mcd_products:[],mcd_dpgroups:[],mcdNames:[],unreadd:[],unreadd_products:[],unreadd_dpgroups:[],unreaddNames:[],pendingd:[],pendingd_products:[],pendingd_dpgroups:[],pendingd_ugroups:[],pendingdNames:[],botd:[],botd_products:[],botd_dpgroups:[],botd_ugroups:[],botdNames:[],subjectd:[],subjectd_products:[],subjectd_dpgroups:[],subjectd_ugroups:[],subjectdNames:[],closedd:[],closedd_products:[],closedd_dpgroups:[],closeddNames:[],statusNotifications:[],toggleWidgetData:[],isListLoaded:!1,activeu:[],pendingu:[],subjectu:[],oopu:[],custom_extension_filter:"",depFilterText:"",userFilterText:"",limitb:"10",limita:"10",limitu:"10",limitp:"10",limito:String(confLH.dlist.op_n),limitc:"10",limitd:"10",limitmc:"10",limitgc:"10",limits:"10",new_group_type:"1",bot_st:{},departmentd_hide_dep:!1,departmentd_hide_dgroup:!1,lmtoggle:!1,rmtoggle:!1,current_user_id:confLH.user_id,limitpm:"10",limitam:"10",limitalm:"10",limitmm:"10",pendingmd:[],pendingmd_products:[],pendingmd_dpgroups:[],pendingmd_ugroups:[],pendingmdNames:[],activemd:[],activemd_products:[],activemd_dpgroups:[],activemd_ugroups:[],activemdNames:[],alarmmd:[],alarmmd_products:[],alarmmd_dpgroups:[],alarmmd_ugroups:[],alarmmdNames:[],mmd:[],mmd_dpgroups:[],mmdNames:[],pendingmu:[],activemu:[],alarmmu:[],onlineusersPreviousID:[]}),mt=pt({});const gt=await fetch(WWW_DIR_JAVASCRIPT+"restapi/lang/lhcbo/v2",{method:"GET",headers:{Accept:"application/json","Content-Type":"application/json","X-CSRFToken":confLH.csrf_token}}).catch((t=>{console.log("Translations could not be loaded!")})),_t=await gt.json();const bt=function(e,n,o){const i=!Array.isArray(e),c=i?[e]:e;if(!c.every(Boolean))throw new Error("derived() expects stores as input, got a falsy value");const u=n.length<2;return a=(e,o)=>{let a=!1;const l=[];let $=0,f=t;const p=()=>{if($)return;f();const s=n(i?l[0]:l,e,o);u?e(s):f=r(s)?s:t},h=c.map(((t,e)=>d(t,(t=>{l[e]=t,$&=~(1<<e),a&&p()}),(()=>{$|=1<<e}))));return a=!0,p(),function(){s(h),f(),a=!1}},{subscribe:pt(o,a).subscribe};var a}(pt("lhcbo"),(t=>(e,n={})=>function(t,e,n){if(!e)throw new Error("no key provided to $t()");if(!t)throw new Error(`no translation for key "${e}"`);let o=_t[e];return o?(Object.keys(n).map((t=>{const e=new RegExp(`{{${t}}}`,"g");o=o.replace(e,n[t])})),o):(console.log(`no translation found for ${e}`),e)}(t,e,n)));export{L as A,M as B,tt as C,et as D,Z as E,Y as F,rt as G,it as H,ct as I,ht as J,r as K,P as L,d as M,S as N,z as O,O as P,a as Q,e as R,$t as S,$ as T,m as U,p as V,pt as W,B as X,j as Y,N as Z,w as a,g as b,lt as c,_ as d,E as e,V as f,l as g,nt as h,at as i,b as j,y as k,k as l,h as m,t as n,I as o,A as p,x as q,mt as r,i as s,v as t,bt as u,f as v,C as w,st as x,s as y,ot as z};
//# sourceMappingURL=i18n.YRNWOie8.js.map
