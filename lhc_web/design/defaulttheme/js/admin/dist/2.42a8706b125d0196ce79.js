(window.webpackJsonpLHCReactAPPAdmin=window.webpackJsonpLHCReactAPPAdmin||[]).push([[2],{8:function(e,n,t){"use strict";t.r(n);var a=t(18),s=t.n(a),c=t(0),r=t.n(c),l=t(24),o=t.n(l),m=null;n.default=function(e){var n=Object(c.useState)([]),t=s()(n,2),a=t[0],l=t[1],d=Object(c.useState)(!1),u=s()(d,2),i=u[0],g=u[1],p=Object(c.useReducer)((function(e){return e+1}),0),f=s()(p,2),h=(f[0],f[1]),C=function(){i||o.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId).then((function(e){l(e.data),g(!0),I(null)}))},I=function(n){if(clearTimeout(m),console.log("starting render"),console.log(n),console.log("ending render"),null!==n){var t=document.getElementById("chat-render-preview-"+e.chatId);t.innerHTML=n.msg;var a=new FormData;a.append("msg",n.msg),a.append("msg_body",!0),m=setTimeout((function(){o.a.post(WWW_DIR_JAVASCRIPT+"chat/previewmessage/",a).then((function(e){t.innerHTML=e.data}))}),100)}else document.getElementById("chat-render-preview-"+e.chatId).innerHTML=""},v=function(n,t){if((13==n.keyCode||38==n.keyCode||40==n.keyCode)&&1==t)return n.preventDefault(),void n.stopPropagation();if(13==n.keyCode)a.map((function(n,t){return n.messages.map((function(n){n.current&&(document.getElementById("CSChatMessage-"+e.chatId).value=n.msg,document.getElementById("CSChatMessage-"+e.chatId).focus())}))})),n.preventDefault(),n.stopPropagation();else if(38==n.keyCode){var s=!1;if(1==a[0].messages[0].current){a[0].messages[0].current=!1;var c=a.length-1;a[c].messages[a[c].messages.length-1].current=!0,I(a[c].messages[a[c].messages.length-1])}else a.map((function(e,n,t){return t[t.length-1-n].messages.map((function(e,n,t){var a=t[t.length-1-n];1==s?(a.current=!0,s=!1,I(a)):a.current&&(a.current=!1,s=!0)}))}));l(a),h(),n.preventDefault(),n.stopPropagation()}else if(40==n.keyCode){s=!1;1==a[a.length-1].messages[a[a.length-1].messages.length-1].current?(a[a.length-1].messages[a[a.length-1].messages.length-1].current=!1,a[0].messages[0].current=!0,I(a[0].messages[0])):a.map((function(e,n){e.messages.map((function(e){1==s?(e.current=!0,I(e),s=!1):e.current&&(e.current=!1,s=!0)}))})),l(a),h(),n.preventDefault(),n.stopPropagation()}else!0===t&&o.a.get(WWW_DIR_JAVASCRIPT+"cannedmsg/filter/"+e.chatId+"?q="+encodeURIComponent(n.target.value)).then((function(e){l(e.data),console.log(a),I(null),a.map((function(e,n){e.messages.map((function(e){1==e.current&&(console.log("ddd"),console.log(e),console.log("ddd"),I(e))}))}))}))};return r.a.createElement(r.a.Fragment,null,r.a.createElement("div",{className:"col-6"},r.a.createElement("input",{type:"text",onFocus:C,className:"form-control form-control-sm",onKeyUp:function(e){return v(e,!0)},onKeyDown:function(e){return v(e,!1)},defaultValue:"",placeholder:"Type to search"}),!i&&r.a.createElement("p",{className:"border mt-1"},r.a.createElement("a",{className:"fs13",onClick:C},r.a.createElement("span",{className:"material-icons"},"expand_more")," Canned messages")),i&&r.a.createElement("ul",{className:"list-unstyled fs13 border mt-1"},a.map((function(n,t){return r.a.createElement("li",null,r.a.createElement("a",{className:"font-weight-bold",key:t,onClick:function(){return s=t,(e=n).expanded=!e.expanded,void l(a.map((function(n,t){return s==t?e:n})));var e,s}},r.a.createElement("span",{className:"material-icons"},n.expanded?"expand_less":"expand_more"),n.title," [",n.messages.length,"]"),n.expanded&&r.a.createElement("ul",{className:"list-unstyled ml-4"},n.messages.map((function(n){return r.a.createElement("li",{key:n.id,className:n.current?"font-italic font-weight-bold":""},r.a.createElement("a",{title:"Send instantly",onClick:function(t){return function(n){setTimeout((function(){var t=new FormData;t.append("msg",n.msg),o.a.post(WWW_DIR_JAVASCRIPT+"chat/addmsgadmin/"+e.chatId,t,{headers:{"X-CSRFToken":confLH.csrf_token}}).then((function(n){return LHCCallbacks.addmsgadmin&&LHCCallbacks.addmsgadmin(e.chatId),ee.emitEvent("chatAddMsgAdmin",[e.chatId]),lhinst.syncadmincall(),!0}))}),n.delay)}(n)}},r.a.createElement("span",{className:"material-icons fs12"},"send")),r.a.createElement("a",{title:n.msg,onClick:function(t){return function(n){document.getElementById("CSChatMessage-"+e.chatId).value=n.msg,document.getElementById("CSChatMessage-"+e.chatId).focus(),I(n)}(n)}},n.message_title))}))))})))),r.a.createElement("div",{className:"col-6 mx300",id:"chat-render-preview-"+e.chatId}))}}}]);