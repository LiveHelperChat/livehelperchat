"use strict";(self.webpackChunkLHCReactAPP=self.webpackChunkLHCReactAPP||[]).push([[277],{1277:function(e,t,n){n.r(t),n.d(t,{nodeJSChat:function(){return g}});var a=n(467),r=n(3029),s=n(2901),i=n(4756),c=n.n(i),o=n(7912),h=n(5580),u=n(5923),d=n(9677);function l(e){var t,n,a,r=2;for("undefined"!=typeof Symbol&&(n=Symbol.asyncIterator,a=Symbol.iterator);r--;){if(n&&null!=(t=e[n]))return t.call(e);if(a&&null!=(t=e[a]))return new p(t.call(e));n="@@asyncIterator",a="@@iterator"}throw new TypeError("Object is not async iterable")}function p(e){function t(e){if(Object(e)!==e)return Promise.reject(new TypeError(e+" is not an object."));var t=e.done;return Promise.resolve(e.value).then((function(e){return{value:e,done:t}}))}return p=function(e){this.s=e,this.n=e.next},p.prototype={s:null,n:null,next:function(){return t(this.n.apply(this.s,arguments))},return:function(e){var n=this.s.return;return void 0===n?Promise.resolve({value:e,done:!0}):t(n.apply(this.s,arguments))},throw:function(e){var n=this.s.return;return void 0===n?Promise.reject(e):t(n.apply(this.s,arguments))}},new p(e)}var v=function(){return(0,s.A)((function e(){var t=this;(0,r.A)(this,e),this.socket=null,o.q.eventEmitter.addListener("endedChat",(function(){null!==t.socket&&t.socket.disconnect()}))}),[{key:"bootstrap",value:function(e,t,n){var r=n(),s=r.chatwidget.getIn(["chatData","id"]),i=(r.chatwidget.getIn(["chatData","hash"]),r.chatwidget.getIn(["chat_ui","sync_interval"])),p=null,v={protocolVersion:1,hostname:e.hostname,path:e.path,autoReconnectOptions:{initialDelay:5e3,randomness:5e3}};""!=e.port&&(v.port=parseInt(e.port)),1==e.secure&&(v.secure=!0),e.instance_id>0&&e.instance_id;var g=this.socket=u.create(v),m=null;function f(e){e.isAuthenticated&&s>0?y():_()}function _(){var t=n(),r=t.chatwidget.getIn(["chatData","id"]);window.lhcAxios.post(window.lhcChat.base_url+"nodejshelper/tokenvisitor/"+r+"/"+t.chatwidget.getIn(["chatData","hash"]),null,{headers:{"Content-Type":"application/x-www-form-urlencoded"}}).then(function(){var t=(0,a.A)(c().mark((function t(n){return c().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,Promise.all([g.invoke("login",{hash:n.data,chanelName:e.instance_id>0?"chat_"+e.instance_id+"_"+r:"chat_"+r}),g.listener("authenticate").once()]);case 2:y();case 3:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}())}function w(t){1==t.status?e.instance_id>0?g.transmitPublish("chat_"+e.instance_id+"_"+s,{op:"vt",msg:t.msg}):g.transmitPublish("chat_"+s,{op:"vt",msg:t.msg}):e.instance_id>0?g.transmitPublish("chat_"+e.instance_id+"_"+s,{op:"vts"}):g.transmitPublish("chat_"+s,{op:"vts"})}function x(t){e.instance_id>0?g.transmitPublish("chat_"+e.instance_id+"_"+s,{op:"vt",msg:"✉️ "+t.msg}):g.transmitPublish("chat_"+s,{op:"vt",msg:"✉️ "+t.msg})}function b(t){e.instance_id>0?g.transmitPublish("chat_"+e.instance_id+"_"+s,{op:"vt",msg:"📕️ error happened while sending visitor message, please inform your administrator!"}):g.transmitPublish("chat_"+s,{op:"vt",msg:"📕️ error happened while sending visitor message, please inform your administrator!"})}function k(){if(null!==m)try{m.unsubscribe()}catch(e){}o.q.eventEmitter.removeListener("visitorTyping",w),o.q.eventEmitter.removeListener("messageSend",x),o.q.eventEmitter.removeListener("messageSendError",b),t({type:"CHAT_UI_UPDATE",data:{sync_interval:i}}),t({type:"CHAT_REMOVE_OVERRIDE",data:"typing"})}function y(){var r=null==m;m=e.instance_id>0?g.subscribe("chat_"+e.instance_id+"_"+s):g.subscribe("chat_"+s),o.q.eventEmitter.addListener("visitorTyping",w),o.q.eventEmitter.addListener("messageSend",x),o.q.eventEmitter.addListener("messageSendError",b),t({type:"CHAT_ADD_OVERRIDE",data:"typing"}),1==r&&((0,a.A)(c().mark((function n(){var a,r,i,o,h;return c().wrap((function(n){for(;;)switch(n.prev=n.next){case 0:n.prev=0,a=!1,r=!1,n.prev=3,o=l(m.listener("subscribe"));case 5:return n.next=7,o.next();case 7:if(!(a=!(h=n.sent).done)){n.next=14;break}h.value,g.transmitPublish(e.instance_id>0?"chat_"+e.instance_id+"_"+s:"chat_"+s,{op:"vi_online",status:!0}),t({type:"CHAT_UI_UPDATE",data:{sync_interval:1e4}});case 11:a=!1,n.next=5;break;case 14:n.next=20;break;case 16:n.prev=16,n.t0=n.catch(3),r=!0,i=n.t0;case 20:if(n.prev=20,n.prev=21,!a||null==o.return){n.next=25;break}return n.next=25,o.return();case 25:if(n.prev=25,!r){n.next=28;break}throw i;case 28:return n.finish(25);case 29:return n.finish(20);case 30:n.next=38;break;case 32:return n.prev=32,n.t1=n.catch(0),n.next=36,m.listener("subscribe").once();case 36:n.sent,g.transmitPublish(e.instance_id>0?"chat_"+e.instance_id+"_"+s:"chat_"+s,{op:"vi_online",status:!0});case 38:case"end":return n.stop()}}),n,null,[[0,32],[3,16,20,30],[21,,25,29]])})))(),(0,a.A)(c().mark((function a(){var r,s,i,o,u,v,f,_,w,x;return c().wrap((function(a){for(;;)switch(a.prev=a.next){case 0:a.prev=0,r=!1,s=!1,a.prev=3,o=l(m);case 5:return a.next=7,o.next();case 7:if(!(r=!(u=a.sent).done)){a.next=13;break}"ot"==(v=u.value).op?1==v.data.status?t({type:"chat_status_changed",data:{text:v.data.typer?v.data.typer+" "+d.A.t("chat.typing"):v.data.ttx}}):t({type:"chat_status_changed",data:{text:""}}):"sflow"==v.op?(null===p&&(p=document.querySelector("#messages-scroll > div.message-row-typing > .msg-body"))&&(p.innerText=""),console.log(v.msg),p&&(p.innerText+=v.msg,p.scrollIntoView())):"cmsg"==v.op||"schange"==v.op?(p=null,(f=n()).chatwidget.hasIn(["chatData","id"])&&t((0,h.lj)({chat_id:f.chatwidget.getIn(["chatData","id"]),hash:f.chatwidget.getIn(["chatData","hash"]),lmgsid:f.chatwidget.getIn(["chatLiveData","lmsgid"]),theme:f.chatwidget.get("theme"),active_widget:(f.chatwidget.get("shown")&&"widget"==f.chatwidget.get("mode")||"widget"!=f.chatwidget.get("mode")&&document.hasFocus())&&1==window.lhcChat.is_focused}))):"umsg"==v.op?(_=n()).chatwidget.hasIn(["chatData","id"])&&(0,h.nc)({msg_id:v.msid,id:_.chatwidget.getIn(["chatData","id"]),hash:_.chatwidget.getIn(["chatData","hash"])})(t,n):"schange"==v.op||"cclose"==v.op?(w=n()).chatwidget.hasIn(["chatData","id"])&&t((0,h.d1)({chat_id:w.chatwidget.getIn(["chatData","id"]),hash:w.chatwidget.getIn(["chatData","hash"]),mode:w.chatwidget.get("mode"),theme:w.chatwidget.get("theme")})):"vo"==v.op&&(x=n()).chatwidget.hasIn(["chatData","id"])&&g.transmitPublish(e.instance_id>0?"chat_"+e.instance_id+"_"+x.chatwidget.getIn(["chatData","id"]):"chat_"+x.chatwidget.getIn(["chatData","id"]),{op:"vi_online",status:!0});case 10:r=!1,a.next=5;break;case 13:a.next=19;break;case 15:a.prev=15,a.t0=a.catch(3),s=!0,i=a.t0;case 19:if(a.prev=19,a.prev=20,!r||null==o.return){a.next=24;break}return a.next=24,o.return();case 24:if(a.prev=24,!s){a.next=27;break}throw i;case 27:return a.finish(24);case 28:return a.finish(19);case 29:a.next=33;break;case 31:a.prev=31,a.t1=a.catch(0);case 33:case"end":return a.stop()}}),a,null,[[0,31],[3,15,19,29],[20,,24,28]])})))())}(0,a.A)(c().mark((function e(){var t,n,a,r,s;return c().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:e.prev=0,t=!1,n=!1,e.prev=3,r=l(g.listener("connect"));case 5:return e.next=7,r.next();case 7:if(!(t=!(s=e.sent).done)){e.next=13;break}f(s.value);case 10:t=!1,e.next=5;break;case 13:e.next=19;break;case 15:e.prev=15,e.t0=e.catch(3),n=!0,a=e.t0;case 19:if(e.prev=19,e.prev=20,!t||null==r.return){e.next=24;break}return e.next=24,r.return();case 24:if(e.prev=24,!n){e.next=27;break}throw a;case 27:return e.finish(24);case 28:return e.finish(19);case 29:e.next=37;break;case 31:return e.prev=31,e.t1=e.catch(0),e.next=35,g.listener("connect").once();case 35:f(e.sent);case 37:case"end":return e.stop()}}),e,null,[[0,31],[3,15,19,29],[20,,24,28]])})))(),(0,a.A)(c().mark((function e(){var t,n,a,r,s;return c().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:e.prev=0,t=!1,n=!1,e.prev=3,r=l(g.listener("disconnect"));case 5:return e.next=7,r.next();case 7:if(!(t=!(s=e.sent).done)){e.next=13;break}s.value,k();case 10:t=!1,e.next=5;break;case 13:e.next=19;break;case 15:e.prev=15,e.t0=e.catch(3),n=!0,a=e.t0;case 19:if(e.prev=19,e.prev=20,!t||null==r.return){e.next=24;break}return e.next=24,r.return();case 24:if(e.prev=24,!n){e.next=27;break}throw a;case 27:return e.finish(24);case 28:return e.finish(19);case 29:e.next=37;break;case 31:return e.prev=31,e.t1=e.catch(0),e.next=35,g.listener("disconnect").once();case 35:e.sent,k();case 37:case"end":return e.stop()}}),e,null,[[0,31],[3,15,19,29],[20,,24,28]])})))(),(0,a.A)(c().mark((function e(){var t,n,a,r,s;return c().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:e.prev=0,t=!1,n=!1,e.prev=3,r=l(g.listener("deauthenticate"));case 5:return e.next=7,r.next();case 7:if(!(t=!(s=e.sent).done)){e.next=13;break}s.value,_();case 10:t=!1,e.next=5;break;case 13:e.next=19;break;case 15:e.prev=15,e.t0=e.catch(3),n=!0,a=e.t0;case 19:if(e.prev=19,e.prev=20,!t||null==r.return){e.next=24;break}return e.next=24,r.return();case 24:if(e.prev=24,!n){e.next=27;break}throw a;case 27:return e.finish(24);case 28:return e.finish(19);case 29:e.next=37;break;case 31:return e.prev=31,e.t1=e.catch(0),e.next=35,g.listener("deauthenticate").once();case 35:e.sent,_();case 37:case"end":return e.stop()}}),e,null,[[0,31],[3,15,19,29],[20,,24,28]])})))()}}])}(),g=new v}}]);
//# sourceMappingURL=277.7ba26039bed805e1a5b6.js.map