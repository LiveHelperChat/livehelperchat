(window.webpackJsonpLHCReactAPPAdmin=window.webpackJsonpLHCReactAPPAdmin||[]).push([[2],{8:function(e,t,n){"use strict";n.r(t);var a=n(18),s=n.n(a),c=n(0),r=n.n(c),l=n(24),m=n.n(l),o=null;t.default=function(e){var t=Object(c.useState)([]),n=s()(t,2),a=n[0],l=n[1],u=Object(c.useState)(!1),i=s()(u,2),d=i[0],g=i[1],p=Object(c.useReducer)((function(e){return e+1}),0),f=s()(p,2),h=(f[0],f[1]),C=function(){d||m.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId).then((function(e){l(e.data),g(!0),I(null)}))},I=function(t){if(null!==t){var n=document.getElementById("chat-render-preview-"+e.chatId);n.innerHTML=t.msg,clearTimeout(o);var a=new FormData;a.append("msg",t.msg),a.append("msg_body",!0),o=setTimeout((function(){m.a.post(WWW_DIR_JAVASCRIPT+"chat/previewmessage/",a).then((function(e){n.innerHTML=e.data}))}),1e3)}else document.getElementById("chat-render-preview-"+e.chatId).innerHTML=""},v=function(t,n){if((13==t.keyCode||38==t.keyCode||40==t.keyCode)&&1==n)return t.preventDefault(),void t.stopPropagation();if(13==t.keyCode)a.map((function(t,n){return t.messages.map((function(t){t.current&&(document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus())}))})),t.preventDefault(),t.stopPropagation();else if(38==t.keyCode){var s=!1;if(1==a[0].messages[0].current){a[0].messages[0].current=!1;var c=a.length-1;a[c].messages[a[c].messages.length-1].current=!0,I(a[c].messages[a[c].messages.length-1])}else a.map((function(e,t,n){return n[n.length-1-t].messages.map((function(e,t,n){var a=n[n.length-1-t];1==s?(a.current=!0,s=!1,I(a)):a.current&&(a.current=!1,s=!0)}))}));l(a),h(),t.preventDefault(),t.stopPropagation()}else if(40==t.keyCode){s=!1;1==a[a.length-1].messages[a[a.length-1].messages.length-1].current?(a[a.length-1].messages[a[a.length-1].messages.length-1].current=!1,a[0].messages[0].current=!0,I(a[0].messages[0])):a.map((function(e,t){e.messages.map((function(e){1==s?(e.current=!0,I(e),s=!1):e.current&&(e.current=!1,s=!0)}))})),l(a),h(),t.preventDefault(),t.stopPropagation()}else!0===n&&m.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId+"?q="+encodeURIComponent(t.target.value)).then((function(e){l(e.data),I(null),a.map((function(e,t){e.messages.map((function(e){1==e.current&&I(e)}))}))}))};return r.a.createElement(r.a.Fragment,null,r.a.createElement("div",{className:"col-6"},r.a.createElement("input",{type:"text",onFocus:C,className:"form-control form-control-sm",onKeyUp:function(e){return v(e,!0)},onKeyDown:function(e){return v(e,!1)},defaultValue:"",placeholder:"Type to search"}),!d&&r.a.createElement("p",{className:"border mt-1"},r.a.createElement("a",{className:"fs13",onClick:C},r.a.createElement("span",{className:"material-icons"},"expand_more")," Canned messages")),d&&r.a.createElement("ul",{className:"list-unstyled fs13 border mt-1"},a.map((function(t,n){return r.a.createElement("li",null,r.a.createElement("a",{className:"font-weight-bold",key:n,onClick:function(){return s=n,(e=t).expanded=!e.expanded,void l(a.map((function(t,n){return s==n?e:t})));var e,s}},r.a.createElement("span",{className:"material-icons"},t.expanded?"expand_less":"expand_more"),t.title," [",t.messages.length,"]"),t.expanded&&r.a.createElement("ul",{className:"list-unstyled ml-4"},t.messages.map((function(t){return r.a.createElement("li",{key:t.id,className:t.current?"font-italic font-weight-bold":""},r.a.createElement("a",{title:"Send instantly",onClick:function(n){return function(t){setTimeout((function(){var n=new FormData;n.append("msg",t.msg),m.a.post(WWW_DIR_JAVASCRIPT+"chat/addmsgadmin/"+e.chatId,n,{headers:{"X-CSRFToken":confLH.csrf_token}}).then((function(t){return LHCCallbacks.addmsgadmin&&LHCCallbacks.addmsgadmin(e.chatId),ee.emitEvent("chatAddMsgAdmin",[e.chatId]),lhinst.syncadmincall(),!0}))}),t.delay)}(t)}},r.a.createElement("span",{className:"material-icons fs12"},"send")),r.a.createElement("a",{title:t.msg,onClick:function(n){return function(t){document.getElementById("CSChatMessage-"+e.chatId).value=t.msg,document.getElementById("CSChatMessage-"+e.chatId).focus()}(t)}},t.message_title))}))))})))),r.a.createElement("div",{className:"col-6 mx300",id:"chat-render-preview-"+e.chatId}))}}}]);