(window.webpackJsonpLHCReactAPPAdmin=window.webpackJsonpLHCReactAPPAdmin||[]).push([[2],{8:function(e,t,n){"use strict";n.r(t);var a=n(18),c=n.n(a),s=n(0),r=n.n(s),l=n(24),o=n.n(l);t.default=function(e){var t=Object(s.useState)([]),n=c()(t,2),a=n[0],l=n[1],m=Object(s.useState)(!1),u=c()(m,2),d=u[0],i=u[1],f=Object(s.useReducer)((function(e){return e+1}),0),p=c()(f,2),g=(p[0],p[1]),h=function(){d||o.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId).then((function(e){l(e.data),i(!0)}))},C=function(t,n){if(13==t.keyCode&&!1===n)a.map((function(t,n){return t.messages.map((function(t){t.current&&(document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus())}))})),t.preventDefault(),t.stopPropagation();else if(38==t.keyCode&&!1===n){var c=!1;a.map((function(e,t,n){return n[n.length-1-t].messages.map((function(e,t,n){var a=n[n.length-1-t];1==c?(a.current=!0,c=!1):a.current&&(a.current=!1,c=!0)}))})),l(a),g(),t.preventDefault(),t.stopPropagation()}else if(40==t.keyCode&&!1===n){c=!1;a.map((function(e,t){e.messages.map((function(e){1==c?(e.current=!0,c=!1):e.current&&(e.current=!1,c=!0)}))})),l(a),g(),t.preventDefault(),t.stopPropagation()}else!0===n&&o.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId+"?q="+encodeURIComponent(t.target.value)).then((function(e){l(e.data)}))};return r.a.createElement(r.a.Fragment,null,r.a.createElement("div",{className:"col-6"},r.a.createElement("input",{type:"text",onFocus:h,className:"form-control form-control-sm",onKeyUp:function(e){return C(e,!0)},onKeyDown:function(e){return C(e,!1)},defaultValue:"",placeholder:"Type to search"}),!d&&r.a.createElement("p",{className:"border mt-1"},r.a.createElement("a",{className:"fs13",onClick:h},r.a.createElement("span",{className:"material-icons"},"expand_more")," Canned messages")),d&&r.a.createElement("ul",{className:"list-unstyled fs13 border mt-1"},a.map((function(t,n){return r.a.createElement("li",null,r.a.createElement("a",{className:"font-weight-bold",key:n,onClick:function(){return c=n,(e=t).expanded=!e.expanded,void l(a.map((function(t,n){return c==n?e:t})));var e,c}},r.a.createElement("span",{className:"material-icons"},t.expanded?"expand_less":"expand_more"),t.title," [",t.messages.length,"]"),t.expanded&&r.a.createElement("ul",{className:"list-unstyled ml-4"},t.messages.map((function(t){return r.a.createElement("li",{key:t.id,className:t.current?"font-italic font-weight-bold":""},r.a.createElement("a",{title:"Send instantly",onClick:function(n){return function(t){setTimeout((function(){var n=new FormData;n.append("msg",t.msg),o.a.post(WWW_DIR_JAVASCRIPT+"chat/addmsgadmin/"+e.chatId,n,{headers:{"X-CSRFToken":confLH.csrf_token}}).then((function(t){return LHCCallbacks.addmsgadmin&&LHCCallbacks.addmsgadmin(e.chatId),ee.emitEvent("chatAddMsgAdmin",[e.chatId]),lhinst.syncadmincall(),!0}))}),t.delay)}(t)}},r.a.createElement("span",{className:"material-icons fs12"},"send")),r.a.createElement("a",{title:t.msg,onClick:function(n){return function(t){document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus()}(t)}},t.message_title))}))))})))),r.a.createElement("div",{className:"col-6"},"Preview rendered..."))}}}]);