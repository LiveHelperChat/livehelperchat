(window.webpackJsonpLHCReactAPPAdmin=window.webpackJsonpLHCReactAPPAdmin||[]).push([[2],{8:function(e,t,n){"use strict";n.r(t);var a=n(18),s=n.n(a),c=n(0),r=n.n(c),o=n(24),l=n.n(o);t.default=function(e){var t=Object(c.useState)([]),n=s()(t,2),a=n[0],o=n[1],m=Object(c.useState)(!1),u=s()(m,2),i=u[0],d=u[1],g=Object(c.useReducer)((function(e){return e+1}),0),p=s()(g,2),f=(p[0],p[1]),h=function(){i||l.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId).then((function(e){o(e.data),d(!0),C("")}))},C=function(e){var t=new FormData;t.append("msg",e.msg),t.append("msg_body",!0),l.a.post(WWW_DIR_JAVASCRIPT+"chat/previewmessage/",t,(function(e){console.log()}))},I=function(t,n){if((13==t.keyCode||38==t.keyCode||40==t.keyCode)&&1==n)return t.preventDefault(),void t.stopPropagation();if(13==t.keyCode)a.map((function(t,n){return t.messages.map((function(t){t.current&&(document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus())}))})),t.preventDefault(),t.stopPropagation();else if(38==t.keyCode){var s=!1;if(1==a[0].messages[0].current){a[0].messages[0].current=!1;var c=a.length-1;a[c].messages[a[c].messages.length-1].current=!0}else a.map((function(e,t,n){return n[n.length-1-t].messages.map((function(e,t,n){var a=n[n.length-1-t];1==s?(a.current=!0,s=!1,C(a)):a.current&&(a.current=!1,s=!0)}))}));o(a),f(),t.preventDefault(),t.stopPropagation()}else if(40==t.keyCode){s=!1;1==a[a.length-1].messages[a[a.length-1].messages.length-1].current?(a[a.length-1].messages[a[a.length-1].messages.length-1].current=!1,a[0].messages[0].current=!0):a.map((function(e,t){e.messages.map((function(e){1==s?(e.current=!0,C(e),s=!1):e.current&&(e.current=!1,s=!0)}))})),o(a),f(),t.preventDefault(),t.stopPropagation()}else!0===n&&l.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId+"?q="+encodeURIComponent(t.target.value)).then((function(e){o(e.data),a.map((function(e,t){e.messages.map((function(e){e.current?C(e):C("")}))}))}))};return r.a.createElement(r.a.Fragment,null,r.a.createElement("div",{className:"col-6"},r.a.createElement("input",{type:"text",onFocus:h,className:"form-control form-control-sm",onKeyUp:function(e){return I(e,!0)},onKeyDown:function(e){return I(e,!1)},defaultValue:"",placeholder:"Type to search"}),!i&&r.a.createElement("p",{className:"border mt-1"},r.a.createElement("a",{className:"fs13",onClick:h},r.a.createElement("span",{className:"material-icons"},"expand_more")," Canned messages")),i&&r.a.createElement("ul",{className:"list-unstyled fs13 border mt-1"},a.map((function(t,n){return r.a.createElement("li",null,r.a.createElement("a",{className:"font-weight-bold",key:n,onClick:function(){return s=n,(e=t).expanded=!e.expanded,void o(a.map((function(t,n){return s==n?e:t})));var e,s}},r.a.createElement("span",{className:"material-icons"},t.expanded?"expand_less":"expand_more"),t.title," [",t.messages.length,"]"),t.expanded&&r.a.createElement("ul",{className:"list-unstyled ml-4"},t.messages.map((function(t){return r.a.createElement("li",{key:t.id,className:t.current?"font-italic font-weight-bold":""},r.a.createElement("a",{title:"Send instantly",onClick:function(n){return function(t){setTimeout((function(){var n=new FormData;n.append("msg",t.msg),l.a.post(WWW_DIR_JAVASCRIPT+"chat/addmsgadmin/"+e.chatId,n,{headers:{"X-CSRFToken":confLH.csrf_token}}).then((function(t){return LHCCallbacks.addmsgadmin&&LHCCallbacks.addmsgadmin(e.chatId),ee.emitEvent("chatAddMsgAdmin",[e.chatId]),lhinst.syncadmincall(),!0}))}),t.delay)}(t)}},r.a.createElement("span",{className:"material-icons fs12"},"send")),r.a.createElement("a",{title:t.msg,onClick:function(n){return function(t){document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus()}(t)}},t.message_title))}))))})))),r.a.createElement("div",{className:"col-6",id:"chat-render-preview-"+e.chatId}))}}}]);