(window.webpackJsonpLHCReactAPPAdmin=window.webpackJsonpLHCReactAPPAdmin||[]).push([[2],{8:function(e,t,a){"use strict";a.r(t);var n=a(18),c=a.n(n),s=a(0),l=a.n(s),m=a(24),o=a.n(m);t.default=function(e){var t=Object(s.useState)([]),a=c()(t,2),n=a[0],m=a[1],r=Object(s.useState)(!1),d=c()(r,2),i=d[0],u=d[1],f=function(){i||o.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId).then((function(e){m(e.data),u(!0)}))};return l.a.createElement(l.a.Fragment,null,l.a.createElement("div",{className:"col-6"},l.a.createElement("input",{type:"text",onFocus:f,className:"form-control form-control-sm",onKeyUp:function(t){return function(t){if(13==t.keyCode)n.map((function(t,a){return t.messages.map((function(t){t.current&&(document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus())}))}));else if(38==t.keyCode)n.map((function(e,t){return e.messages.map((function(e){e.current&&(e.current=!1)}))}));else if(40==t.keyCode){var a=!1;n.map((function(e,t){e.messages.map((function(e){1==a?(e.current=!0,a=!1):e.current&&(e.current=!1,a=!0)}))})),m(n),console.log("--AFTER--"),console.log(n),console.log("--AFTER--")}else o.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId+"?q="+encodeURIComponent(t.target.value)).then((function(e){m(e.data)}))}(t)},defaultValue:"",placeholder:"Type to search"}),!i&&l.a.createElement("p",{className:"border mt-1"},l.a.createElement("a",{className:"fs13",onClick:f},l.a.createElement("span",{className:"material-icons"},"expand_more")," Canned messages")),i&&l.a.createElement("ul",{className:"list-unstyled fs13 border mt-1"},n.map((function(t,a){return l.a.createElement("li",null,l.a.createElement("a",{className:"font-weight-bold",key:a,onClick:function(){return c=a,(e=t).expanded=!e.expanded,void m(n.map((function(t,a){return c==a?e:t})));var e,c}},l.a.createElement("span",{className:"material-icons"},t.expanded?"expand_less":"expand_more"),t.title," [",t.messages.length,"]"),t.expanded&&l.a.createElement("ul",{className:"list-unstyled ml-4"},t.messages.map((function(t){return l.a.createElement("li",{key:t.id,className:t.current?"font-italic font-weight-bold":""},l.a.createElement("a",{title:"Send instantly",onClick:function(a){return function(t){setTimeout((function(){var a=new FormData;a.append("msg",t.msg),o.a.post(WWW_DIR_JAVASCRIPT+"chat/addmsgadmin/"+e.chatId,a,{headers:{"X-CSRFToken":confLH.csrf_token}}).then((function(t){return LHCCallbacks.addmsgadmin&&LHCCallbacks.addmsgadmin(e.chatId),ee.emitEvent("chatAddMsgAdmin",[e.chatId]),lhinst.syncadmincall(),!0}))}),t.delay)}(t)}},l.a.createElement("span",{className:"material-icons fs12"},"send")),l.a.createElement("a",{title:t.msg,onClick:function(a){return function(t){document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus()}(t)}},t.message_title))}))))})))),l.a.createElement("div",{className:"col-6"},"Preview rendered..."))}}}]);