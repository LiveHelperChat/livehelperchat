(window.webpackJsonpLHCReactAPPAdmin=window.webpackJsonpLHCReactAPPAdmin||[]).push([[6],{85:function(e,t,a){"use strict";a.r(t);var n=a(14),l=a.n(n),r=a(2),c=a.n(r),i=a(0),s=a.n(i),o=a(15),m=a.n(o),u=a(63),d=a(16),p=a.n(d),f=s.a.memo((function(e){var t=e.children,a=Object(i.useState)(!1),n=l()(a,2),r=n[0],c=n[1];return s.a.createElement(s.a.Fragment,null,s.a.createElement("div",{className:"pb-2"},s.a.createElement("button",{onClick:function(){return c(!r)},className:"btn btn-sm btn-outline-secondary"},"...")),r&&t)})),v=a(30),g=a.n(v),h=a(82),b=a(72),E=a.n(b),y=a(74),_=a.n(y),N=a(3),R=a.n(N),w=a(4),k=a.n(w),O=a(31),A=a.n(O),I=a(75),j=a.n(I),P=a(77),x=a.n(P),S=a(79),C=a.n(S),D=a(84);function W(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function T(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?W(Object(a),!0).forEach((function(t){c()(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):W(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}function F(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}var V=function(e){j()(l,e);var t,a,n=(t=l,function(){var e,a=C()(t);if(F()){var n=C()(this).constructor;e=Reflect.construct(a,arguments,n)}else e=a.apply(this,arguments);return x()(this,e)});function l(e){var t;return R()(this,l),t=n.call(this,e),c()(A()(t),"state",{hightlight:!1,files:[],uploading:!1,uploadProgress:{},successfullUploaded:!1,progress:""}),t.fileInputRef=s.a.createRef(),t.dropAreaRef=s.a.createRef(),t.openFileDialog=t.openFileDialog.bind(A()(t)),t.onFilesAddedUI=t.onFilesAddedUI.bind(A()(t)),t.onDragOver=t.onDragOver.bind(A()(t)),t.onDragLeave=t.onDragLeave.bind(A()(t)),t.onDrop=t.onDrop.bind(A()(t)),t.onPaste=t.onPaste.bind(A()(t)),t.onFilesAdded=t.onFilesAdded.bind(A()(t)),t.uploadFiles=t.uploadFiles.bind(A()(t)),t.sendRequest=t.sendRequest.bind(A()(t)),t.chooseFromUploaded=t.chooseFromUploaded.bind(A()(t)),t.fileUploaded=t.fileUploaded.bind(A()(t)),t}return k()(l,[{key:"onFilesAdded",value:function(e){var t=this,a=this.props.t,n=new RegExp("(.|/)("+this.props.moptions.fop_op+")$","i"),l=[];e.forEach((function(e){n.test(e.type)||n.test(e.name)||l.push(e.name+": "+a("file.incorrect_type")),e.size>t.props.moptions.fop_size&&l.push(e.name+": "+a("file.to_big_file"))})),l.length>0?alert(l.join("\n")):this.setState({files:e})}},{key:"componentDidUpdate",value:function(e,t){this.state.files.length>0&&0==this.state.uploading&&this.uploadFiles()}},{key:"uploadFiles",value:(a=_()(E.a.mark((function e(){var t,a=this;return E.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return this.setState({uploadProgress:{},uploading:!0}),t=[],this.state.files.forEach((function(e){t.push(a.sendRequest(e))})),e.prev=3,e.next=6,Promise.all(t);case 6:this.setState({successfullUploaded:!0,uploading:!1,files:[]}),e.next=12;break;case 9:e.prev=9,e.t0=e.catch(3),this.setState({successfullUploaded:!0,uploading:!1,files:[]});case 12:case"end":return e.stop()}}),e,this,[[3,9]])}))),function(){return a.apply(this,arguments)})},{key:"fileUploaded",value:function(e){this.props.fileAttached(e)}},{key:"sendRequest",value:function(e){var t=this,a=this.props.t;return new Promise((function(n,l){var r=new XMLHttpRequest;r.upload.addEventListener("progress",(function(n){n.lengthComputable&&(T({},t.state.uploadProgress)[e.name]={state:"pending",percentage:n.loaded/n.total*100},t.setState({progress:a("file.uploading")+" "+Math.round(n.loaded/n.total*100)+"%"}))})),r.upload.addEventListener("load",(function(a){T({},t.state.uploadProgress)[e.name]={state:"done",percentage:100},t.setState({progress:""}),n(r.response)}));var c=t;r.onreadystatechange=function(){4===r.readyState&&c.fileUploaded(JSON.parse(r.response))},r.upload.addEventListener("error",(function(a){var n=T({},t.state.uploadProgress);n[e.name]={state:"error",percentage:0},t.setState({progress:n}),l(r.response)}));var i=new FormData;i.append("files",e,e.name),r.open("POST",WWW_DIR_JAVASCRIPT+"mailconv/uploadfile"),r.send(i)}))}},{key:"openFileDialog",value:function(){this.state.uploading||this.fileInputRef.current.click()}},{key:"onFilesAddedUI",value:function(e){var t=e.target.files,a=this.fileListToArray(t);this.onFilesAdded(a)}},{key:"onDragOver",value:function(e){e.preventDefault(),this.state.uploading||this.setState({hightlight:!0})}},{key:"componentDidMount",value:function(){this.dropAreaRef.current&&(this.dropAreaRef.current.ondragover=this.onDragOver,this.dropAreaRef.current.ondragleave=this.onDragLeave,this.dropAreaRef.current.ondrop=this.onDrop)}},{key:"componentWillUnmount",value:function(){this.dropAreaRef.current&&(this.dropAreaRef.current.ondragover=null,this.dropAreaRef.current.ondragleave=null,this.dropAreaRef.current.ondrop=null),window.attatchReplyCurrent=null}},{key:"onPaste",value:function(e){var t=e&&e.clipboardData&&e.clipboardData.items;if(t&&t.length){for(var a=[],n=0;n<t.length;n++){var l=t[n].getAsFile&&t[n].getAsFile();l&&a.push(l)}a.length>0&&this.onFilesAdded(a)}}},{key:"onDragLeave",value:function(e){this.setState({hightlight:!1})}},{key:"onDrop",value:function(e){if(e.preventDefault(),!this.state.uploading){var t=e.dataTransfer.files,a=this.fileListToArray(t);this.onFilesAdded(a),this.setState({hightlight:!1})}}},{key:"fileListToArray",value:function(e){for(var t=[],a=0;a<e.length;a++)t.push(e.item(a));return t}},{key:"chooseFromUploaded",value:function(){lhc.revealModal({title:"Attatch an already uploaded file",iframe:!0,height:500,url:WWW_DIR_JAVASCRIPT+"mailconv/attatchfile/(attachment)/1"});var e=this;window.attatchReplyCurrent=function(t){e.props.fileAttached(t)}}},{key:"render",value:function(){return s.a.createElement(s.a.Fragment,null,s.a.createElement("button",{className:"btn btn-sm btn-outline-secondary",onClick:this.chooseFromUploaded},s.a.createElement("i",{className:"material-icons"},"list")," Choose file from uploaded files"),s.a.createElement("button",{ref:this.dropAreaRef,onClick:this.openFileDialog,className:"btn btn-sm "+(1==this.state.hightlight?"btn-outline-primary":"btn-outline-secondary")},s.a.createElement("i",{className:"material-icons"},"attach_file")," ",this.state.progress||"Drop your files here or choose a new file"),s.a.createElement("input",{onChange:this.onFilesAddedUI,ref:this.fileInputRef,id:"fileupload",type:"file",name:"files[]",multiple:!0,className:"d-none"}))}}]),l}(i.PureComponent),U=Object(D.a)()(V);function J(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function L(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?J(Object(a),!0).forEach((function(t){c()(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):J(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}var M=s.a.memo((function(e){var t=Object(i.useReducer)((function(e,t){var a=t.type,n=t.value;switch(a){case"add":return[].concat(g()(e),[n]);case"add_recipient":return(e=L({},e))[n].push({name:"",email:""}),e;case"remove_recipient":return(e=L({},e))[n.recipient]=e[n.recipient].filter((function(e,t){return t!==n.index})),e;case"set":return console.log(n),n;case"cleanup":return[];case"remove":return e.filter((function(e,t){return t!==n}));default:return e}}),[]),a=l()(t,2),n=a[0],r=a[1];return Object(i.useEffect)((function(){r({type:"set",value:e.recipients})}),[e.recipients]),s.a.createElement("div",{className:"row"},s.a.createElement("div",{className:"col-12 text-secondary font-weight-bold fs13 pb-1"},"Recipients ",s.a.createElement("i",{className:"material-icons settings text-muted",onClick:function(e){return r({type:"add_recipient",value:"to"})},style:{fontSize:"20px"}},"add")," Cc ",s.a.createElement("i",{className:"material-icons settings text-muted",onClick:function(e){return r({type:"add_recipient",value:"cc"})},style:{fontSize:"20px"}},"add")," Bcc ",s.a.createElement("i",{onClick:function(e){return r({type:"add_recipient",value:"bcc"})},className:"material-icons settings text-muted",style:{fontSize:"20px"}},"add")),s.a.createElement("div",{className:"col-6"},n.to&&n.to.map((function(e,t){var a;return s.a.createElement("div",{className:"form-row pb-1"},s.a.createElement("div",{className:"col-1 text-secondary fs13 pt-1"},"To:"),s.a.createElement("div",{className:"col"},s.a.createElement("div",{className:"input-group input-group-sm"},s.a.createElement("div",{className:"input-group-prepend"},s.a.createElement("span",{className:"input-group-text"},s.a.createElement("i",{className:"material-icons mr-0"},"mail_outline"))),s.a.createElement("input",(a={type:"text",className:"form-control form-control-sm",placeholder:"E-mail",defaultValue:e.email},c()(a,"placeholder","Username"),c()(a,"aria-describedby","validationTooltipUsernamePrepend"),a)))),s.a.createElement("div",{className:"col"},s.a.createElement("input",{type:"text",placeholder:"Recipient name",defaultValue:e.name,className:"form-control form-control-sm"})),t>0&&s.a.createElement("div",{className:"col"},s.a.createElement("i",{className:"material-icons settings text-muted",onClick:function(e){return r({type:"remove_recipient",value:{recipient:"to",index:t}})}},"remove")))}))),s.a.createElement("div",{className:"col-6"},n.cc&&n.cc.map((function(e,t){var a;return s.a.createElement("div",{className:"form-row pb-1"},s.a.createElement("div",{className:"col text-secondary fs13 pt-1"},"Cc:"),s.a.createElement("div",{className:"col"},s.a.createElement("div",{className:"input-group input-group-sm"},s.a.createElement("div",{className:"input-group-prepend"},s.a.createElement("span",{className:"input-group-text"},s.a.createElement("i",{className:"material-icons mr-0"},"mail_outline"))),s.a.createElement("input",(a={type:"text",className:"form-control form-control-sm",placeholder:"E-mail",defaultValue:e.email},c()(a,"placeholder","Username"),c()(a,"aria-describedby","validationTooltipUsernamePrepend"),a)))),s.a.createElement("div",{className:"col"},s.a.createElement("input",{type:"text",placeholder:"Recipient name",defaultValue:e.name,className:"form-control form-control-sm"})),s.a.createElement("div",{className:"col"},s.a.createElement("i",{className:"material-icons settings text-muted",onClick:function(e){return r({type:"remove_recipient",value:{recipient:"cc",index:t}})}},"remove")))}))),s.a.createElement("div",{className:"col-6"},n.bcc&&n.bcc.map((function(e,t){var a;return s.a.createElement("div",{className:"form-row pb-1"},s.a.createElement("div",{className:"col text-secondary fs13 pt-1"},"Bcc:"),s.a.createElement("div",{className:"col"},s.a.createElement("div",{className:"input-group input-group-sm"},s.a.createElement("div",{className:"input-group-prepend"},s.a.createElement("span",{className:"input-group-text"},s.a.createElement("i",{className:"material-icons mr-0"},"mail_outline"))),s.a.createElement("input",(a={type:"text",className:"form-control form-control-sm",placeholder:"E-mail",defaultValue:e.email},c()(a,"placeholder","Username"),c()(a,"aria-describedby","validationTooltipUsernamePrepend"),a)))),s.a.createElement("div",{className:"col"},s.a.createElement("input",{type:"text",placeholder:"Recipient name",defaultValue:e.name,className:"form-control form-control-sm"})),s.a.createElement("div",{className:"col"},s.a.createElement("i",{className:"material-icons settings text-muted",onClick:function(e){return r({type:"remove_recipient",value:{recipient:"bcc",index:t}})}},"remove")))}))))})),q=s.a.memo((function(e){var t=Object(i.useState)(!1),a=l()(t,2),n=a[0],r=a[1],o=Object(i.useState)(null),u=l()(o,2),d=(u[0],u[1],Object(i.useState)(null)),p=l()(d,2),f=p[0],v=p[1],b=Object(i.useState)(null),E=l()(b,2),y=E[0],_=E[1],N=Object(i.useState)(!1),R=l()(N,2),w=R[0],k=R[1],O=Object(i.useState)([]),A=l()(O,2),I=A[0],j=A[1],P=Object(i.useReducer)((function(e,t){var a=t.type,n=t.value;switch(a){case"add":return[].concat(g()(e),[n]);case"cleanup":return[];case"remove":return e.filter((function(e,t){return t!==n}));default:return e}}),[]),x=l()(P,2),S=x[0],C=x[1],D=Object(i.useRef)();D.current=S;return Object(i.useEffect)((function(){return function(){D.current.map((function(e,t){!0===e.new&&m.a.get(WWW_DIR_JAVASCRIPT+"file/delete/"+e.id+"/(csfr)/"+confLH.csrf_token+"?react=1")}))}}),[]),Object(i.useEffect)((function(){1==n&&0==w?m.a.post(WWW_DIR_JAVASCRIPT+"mailconv/getreplydata/"+e.message.id).then((function(e){k(!0),v(e.data.intro),_(e.data.signature),j(e.data.recipients)})):0==n&&1==w&&D.current.length>0&&(D.current.map((function(e,t){!0===e.new&&m.a.get(WWW_DIR_JAVASCRIPT+"file/delete/"+e.id+"/(csfr)/"+confLH.csrf_token+"?react=1")})),C({type:"cleanup"}))}),[n]),1==e.replyMode&&0==n&&r(!0),s.a.createElement(s.a.Fragment,null,s.a.createElement("div",{className:"col-12 mt-2 pt-3 pb-2"},!n&&s.a.createElement("div",{className:"btn-group",role:"group","aria-label":"Mail actions"},s.a.createElement("button",{type:"button",className:"btn btn-sm btn-outline-secondary",onClick:function(){return r(!0)}},s.a.createElement("i",{className:"material-icons"},"reply"),"Reply"),s.a.createElement("button",{disabled:1==e.message.response_type,type:"button",className:"btn btn-sm btn-outline-secondary",onClick:function(){return e.noReplyRequired()}},s.a.createElement("i",{className:"material-icons"},"done"),"No reply required"),s.a.createElement("button",{type:"button",className:"btn btn-sm btn-outline-secondary"},s.a.createElement("i",{className:"material-icons"},"forward"),"Forward")),n&&w&&s.a.createElement("div",{className:"shadow p-2"},s.a.createElement(M,{message:e.message,recipients:I}),s.a.createElement(h.a,{tinymceScriptSrc:"/design/defaulttheme/js/tinymce/js/tinymce/tinymce.min.js",initialValue:"<p></p>"+f+"<blockquote>"+e.message.body_front+"</blockquote>"+y,onInit:function(){tinyMCE.get("reply-to-mce-"+e.message.id).focus()},id:"reply-to-mce-"+e.message.id,init:{height:320,automatic_uploads:!0,file_picker_types:"image",images_upload_url:WWW_DIR_JAVASCRIPT+"mailconv/uploadimage",templates:WWW_DIR_JAVASCRIPT+"mailconv/apiresponsetemplates/"+e.message.id,paste_data_images:!0,relative_urls:!1,browser_spellcheck:!0,paste_as_text:!0,contextmenu:!1,menubar:!1,plugins:["advlist autolink lists link image charmap print preview anchor image lhfiles","searchreplace visualblocks code fullscreen","media table paste help","print preview importcss searchreplace autolink save autosave directionality visualblocks visualchars fullscreen media template codesample charmap pagebreak nonbreaking anchor toc advlist lists wordcount textpattern noneditable help charmap emoticons"],toolbar_mode:"wrap",toolbar:"undo redo | fontselect formatselect fontsizeselect | table | paste pastetext | subscript superscript | bold italic underline strikethrough | forecolor backcolor |                             alignleft aligncenter alignright alignjustify | lhfiles insertfile image pageembed template link anchor codesample |                             bullist numlist outdent indent | removeformat permanentpen | charmap emoticons | fullscreen print preview paste code | help"}}),s.a.createElement("div",{className:"float-right"},s.a.createElement("a",{className:"action-image",onClick:function(){r(!1),e.cancelReply()}},s.a.createElement("i",{className:"material-icons"},"delete"))),s.a.createElement("div",{className:"btn-group mt-1",role:"group","aria-label":"Mail actions"},s.a.createElement("button",{type:"button",className:"btn btn-sm btn-outline-primary",onClick:function(){console.log(tinyMCE.get("reply-to-mce-"+e.message.id).getContent())}},s.a.createElement("i",{className:"material-icons"},"send"),"Send"),s.a.createElement(U,{moptions:e.moptions,fileAttached:function(e){return C({type:"add",value:e})},message:e.message})),S&&S.length>0&&s.a.createElement("div",{className:"pt-2"},S.map((function(e,t){return s.a.createElement("button",c()({title:"Click to remove",onClick:function(){return function(e,t){C({type:"remove",value:t}),!0===e.new&&m.a.get(WWW_DIR_JAVASCRIPT+"file/delete/"+e.id+"/(csfr)/"+confLH.csrf_token+"?react=1")}(e,t)},className:"btn btn-sm btn-outline-info mr-1 mb-1"},"title",e.id),e.name)}))))))})),z=s.a.memo((function(e){var t=e.message,a=e.index,n=e.totalMessages,r=e.noReplyRequired,c=e.mode,o=e.addLabel,m=e.moptions,u=Object(i.useState)(!1),v=l()(u,2),g=v[0],h=v[1],b=Object(i.useState)(a+1==n),E=l()(b,2),y=E[0],_=E[1],N=Object(i.useState)(!1),R=l()(N,2),w=R[0],k=R[1];Object(i.useEffect)((function(){}),[]);return s.a.createElement("div",{className:"row pb-2 mb-2 border-secondary"+("preview"!==c?" border-top pt-2":" border-bottom")},s.a.createElement("div",{className:"col-7 action-image",onClick:function(){return _(!y)}},s.a.createElement("span",{title:"Expand message "+t.id},s.a.createElement("i",{className:"material-icons"},y?"expand_less":"expand_more")),s.a.createElement("b",null,t.from_name),s.a.createElement("small",null," <",t.from_address,"> "),s.a.createElement("small",{className:t.status&&1!=t.status?t.cls_time?"chat-closed":"chat-active":"chat-pending"},s.a.createElement("i",{className:"material-icons"},"mail_outline"),t.status&&1!=t.status?"Responded":"Pending response")),s.a.createElement("div",{className:"col-5 text-right text-muted"},s.a.createElement("small",{className:"pr-1"},t.subjects&&t.subjects.map((function(e,t){return s.a.createElement("span",{className:"badge badge-info mr-1"},e.name)})),"preview"!==c&&s.a.createElement(s.a.Fragment,null,s.a.createElement("i",{title:"Add/Remove label",onClick:function(){return o(t)},className:"material-icons action-image text-muted"},"label")," |")),s.a.createElement("small",{className:"pr-2"},t.udate_front," | ",t.udate_ago," ago."),"preview"!==c&&s.a.createElement("i",{onClick:function(e){e.stopPropagation(),k(!0)},className:"material-icons settings text-muted"},"reply"),s.a.createElement("i",{onClick:function(e){e.stopPropagation(),h(!g)},className:"material-icons settings text-muted"},g?"expand_less":"expand_more"),"preview"!==c&&s.a.createElement("div",{className:"dropdown float-right"},s.a.createElement("i",{className:"material-icons settings text-muted",id:"message-id-"+t.id,"data-toggle":"dropdown","aria-haspopup":"true","aria-expanded":"false"},"more_vert"),s.a.createElement("div",{className:"dropdown-menu","aria-labelledby":"message-id-"+t.id},s.a.createElement("a",{className:"dropdown-item",href:"#",onClick:function(e){e.stopPropagation(),k(!0)}},s.a.createElement("i",{className:"material-icons text-muted"},"reply"),"Reply"),s.a.createElement("a",{className:"dropdown-item",href:"#"},s.a.createElement("i",{className:"material-icons text-muted"},"forward"),"Forward"),s.a.createElement("a",{className:"dropdown-item",target:"_blank",href:WWW_DIR_JAVASCRIPT+"mailconv/mailprint/"+t.id},s.a.createElement("i",{className:"material-icons text-muted"},"print"),"Print"),s.a.createElement("a",{className:"dropdown-item",href:WWW_DIR_JAVASCRIPT+"mailconv/apimaildownload/"+t.id},s.a.createElement("i",{className:"material-icons text-muted"},"cloud_download"),"Download"),s.a.createElement("a",{className:"dropdown-item",href:"#",onClick:function(){return r(t)}},s.a.createElement("i",{className:"material-icons text-muted"},"done"),"No reply required")))),g&&s.a.createElement("div",{className:"col-12"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h6",{className:"card-subtitle mb-2 text-muted"},"Message information"),s.a.createElement("div",{className:"row"},s.a.createElement("div",{className:"col-6"},s.a.createElement("ul",{className:"fs13 mb-0 list-unstyled"},s.a.createElement("li",null,s.a.createElement("span",{className:"text-muted"},"from:")," ",s.a.createElement("b",null,t.from_name)," <",t.from_address,">"),s.a.createElement("li",null,s.a.createElement("span",{className:"text-muted"},"to:")," ",t.to_data_front),t.cc_data_front&&s.a.createElement("li",null,s.a.createElement("span",{className:"text-muted"},"cc:")," ",t.cc_data_front),t.bcc_data_front&&s.a.createElement("li",null,s.a.createElement("span",{className:"text-muted"},"bcc:")," ",t.bcc_data_front),s.a.createElement("li",null,s.a.createElement("span",{className:"text-muted"},"reply-to:")," ",t.reply_to_data_front),s.a.createElement("li",null,s.a.createElement("span",{className:"text-muted"},"mailed-by:")," ",t.from_host))),s.a.createElement("div",{className:"col-6"},s.a.createElement("ul",{className:"fs13 mb-0 list-unstyled"},t.accept_time_front&&s.a.createElement("li",null,"Accepted at: ",t.accept_time_front),t.plain_user_name&&s.a.createElement("li",null,"Accepted by: ",s.a.createElement("b",null,t.plain_user_name)),t.wait_time&&s.a.createElement("li",null,"Accept wait time: ",t.wait_time_pending),t.lr_time&&t.response_time&&s.a.createElement("li",null,"Response wait time: ",t.wait_time_response),t.lr_time&&s.a.createElement("li",null,"Response type: ",1==t.response_type?"No response required":2==t.response_type?"Our response message":"Responeded by e-mail"),t.interaction_time&&s.a.createElement("li",null,"Interaction time: ",t.interaction_time_duration),t.cls_time&&s.a.createElement("li",null,"Close time: ",t.cls_time_front))))))),y&&s.a.createElement("div",{className:"col-12 mail-message-body pt-2 pb-2"},p()(t.body_front,{replace:function(e){if(e.attribs){Object.assign({},e.attribs);if(e.attribs.class&&(e.attribs.className=e.attribs.class,delete e.attribs.class),e.name&&"blockquote"===e.name)return e.attribs.style&&(e.attribs.style=(t=e.attribs.style,a={},t.split(";").forEach((function(e){var t=e.split(":"),n=l()(t,2),r=n[0],c=n[1];if(r){var i=function(e){var t=e.split("-");return 1===t.length?t[0]:t[0]+t.slice(1).map((function(e){return e[0].toUpperCase()+e.slice(1)})).join("")}(r.trim());a[i]=c.trim()}})),a)),s.a.createElement("blockquote",e.attribs,s.a.createElement(f,null,Object(d.domToReact)(e.children)))}var t,a}}),t.attachments&&t.attachments.length>0&&s.a.createElement("div",{className:"pt-2"},t.attachments.map((function(e){return s.a.createElement("a",{className:"btn btn-sm btn-outline-info mr-1",href:e.download_url,title:e.description},e.name)})))),"preview"!==c&&(a+1==n||w)&&s.a.createElement(q,{moptions:m,cancelReply:function(e){return k(!1)},replyMode:w,lastMessage:a+1==n,message:t,noReplyRequired:function(){return r(t)}}))}));function H(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,n)}return a}function B(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?H(Object(a),!0).forEach((function(t){c()(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):H(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}function K(e,t){switch(t.type){case"increment":return{count:e.count+1};case"decrement":return{count:e.count-1};case"update":return B({},e,{},t.value);case"update_message":var a=e.messages.findIndex((function(e){return e.id==t.message.id}));return e.messages[a]=t.message,t.conv&&(e.conv=t.conv),e=B({},e);case"update_messages":return t.messages.lmsop=e.lmsop||t.value.lmsop,(e=B({},e,{},t.value)).messages.push(t.messages),e;case"update_history":return e=B({},e,{},t.value),""!=t.history.msg&&e.messages.unshift(t.history),e;case"init":return{count:e.count-1};default:throw new Error("Unknown action!")}}t.default=function(e){Object(i.useRef)(null),Object(i.useRef)(null);var t=Object(i.useRef)(null),a=Object(i.useReducer)(K,{messages:[],operators:[],conv:null,loaded:!1,saving_remarks:!1,old_message_id:0,last_message:"",remarks:"",last_message_id:0,lmsop:0,lgsync:0}),n=l()(a,2),r=n[0],c=n[1],o=function(){m.a.post(WWW_DIR_JAVASCRIPT+"mailconv/loadmainconv/"+e.chatId+"/(mode)/"+(""!=e.mode?e.mode:"normal")).then((function(t){c({type:"update",value:{conv:t.data.conv,messages:t.data.messages,moptions:t.data.moptions,loaded:!0}}),"preview"!==e.mode&&function(e){if(localStorage)try{var t=[],a=localStorage.getItem("machat_id");null!==a&&""!==a&&(t=a.split(",")),-1===t.indexOf(e)&&(t.push(e),localStorage.setItem("machat_id",t.join(",")))}catch(e){}}(e.chatId)})).catch((function(e){}))},d=function(e){lhc.revealModal({url:WWW_DIR_JAVASCRIPT+e.url})};Object(i.useEffect)((function(){var t=setTimeout((function(){m.a.post(WWW_DIR_JAVASCRIPT+"mailconv/saveremarks/"+e.chatId,{data:r.remarks}).then((function(e){c({type:"update",value:{saving_remarks:!1}})}))}),500);return function(){return clearTimeout(t)}}),[r.remarks]);Object(i.useEffect)((function(){return o(),function(){!function(e){if(localStorage)try{var t=[],a=localStorage.getItem("machat_id");null!==a&&""!==a&&(t=a.split(",")),-1!==t.indexOf(e)&&t.splice(t.indexOf(e),1),localStorage.setItem("machat_id",t.join(","))}catch(e){}}(e.chatId)}}),[]),Object(i.useEffect)((function(){if(1==r.loaded)t.current}),[r.loaded]);var p=Object(u.a)("mail_chat");p.t,p.i18n;return 0==r.loaded?s.a.createElement("span",null,"..."):s.a.createElement(s.a.Fragment,null,s.a.createElement("div",{className:"row"},s.a.createElement("div",{className:"chat-main-left-column "+("preview"==e.mode?"col-12":"col-7")},"preview"!==e.mode&&s.a.createElement("h1",{className:"pb-2"},s.a.createElement("i",{className:"material-icons"},1==r.conv.start_type?"call_made":"call_received"),r.conv.subject),s.a.createElement("div",null,r.messages.map((function(t,a){return s.a.createElement(z,{moptions:r.moptions,mode:e.mode,key:"msg_mail_"+e.chatId+"_"+a+"_"+t.id,totalMessages:r.messages.length,index:a,message:t,noReplyRequired:function(e){return function(e){m.a.post(WWW_DIR_JAVASCRIPT+"mailconv/apinoreplyrequired/"+e.id).then((function(e){c({type:"update_message",message:e.data.message,conv:e.data.conv})})).catch((function(e){}))}(t)},addLabel:function(e){return function(e){lhc.revealModal({url:WWW_DIR_JAVASCRIPT+"mailconv/apilabelmessage/"+e.id,hidecallback:function(){m.a.get(WWW_DIR_JAVASCRIPT+"mailconv/apigetlabels/"+e.id).then((function(e){c({type:"update_message",message:e.data.message})})).catch((function(e){}))}})}(t)}})})))),s.a.createElement("div",{className:"chat-main-right-column "+("preview"==e.mode?"d-none":"col-5")},s.a.createElement("div",{role:"tabpanel"},s.a.createElement("ul",{className:"nav nav-pills",role:"tablist",ref:t},s.a.createElement("li",{role:"presentation",className:"nav-item"},s.a.createElement("a",{className:"nav-link active",href:"#mail-chat-info-"+e.chatId,"aria-controls":"#mail-chat-info-"+e.chatId,title:"Information",role:"tab","data-toggle":"tab"},s.a.createElement("i",{className:"material-icons mr-0"},"info_outline"))),s.a.createElement("li",{role:"presentation",className:"nav-item"},s.a.createElement("a",{className:"nav-link",href:"#mail-chat-remarks-"+e.chatId,"aria-controls":"#mail-chat-remarks-"+e.chatId,role:"tab","data-toggle":"tab",title:"Remarks"},s.a.createElement("i",{className:"material-icons mr-0"},"mode_edit")))),s.a.createElement("div",{className:"tab-content"},s.a.createElement("div",{role:"tabpanel",className:"tab-pane",id:"mail-chat-remarks-"+e.chatId},s.a.createElement("div",{className:"material-icons pb-1 text-success"+(r.saving_remarks?" text-warning":"")},"mode_edit"),s.a.createElement("div",null,r.conv&&s.a.createElement("textarea",{placeholder:"Enter your remarks here.",onKeyUp:function(e){return t=e.target.value,void c({type:"update",value:{saving_remarks:!0,remarks:t}});var t},class:"form-control mh150",defaultValue:r.conv.remarks}))),s.a.createElement("div",{role:"tabpanel",className:"tab-pane active",id:"mail-chat-info-"+e.chatId},s.a.createElement("div",{className:"pb-2"},s.a.createElement("a",{className:"btn btn-outline-secondary btn-sm",onClick:function(){return e=!1,r.messages.forEach((function(t){2!=t.status&&(e=!0)})),void((0==e||confirm("There is still unresponded messages, are you sure you want to close this conversation?"))&&m.a.post(WWW_DIR_JAVASCRIPT+"mailconv/apicloseconversation/"+r.conv.id).then((function(e){c({type:"update",value:{conv:e.data.conv,messages:e.data.messages}}),document.getElementById("chat-tab-link-mc"+r.conv.id)&&lhinst.removeDialogTabMail("mc"+r.conv.id,$("#tabs"),!0)})).catch((function(e){})));var e}},s.a.createElement("i",{className:"material-icons"},"close"),"Close")),r.conv&&s.a.createElement("table",{className:"table table-sm"},s.a.createElement("tr",null,s.a.createElement("td",{colSpan:"2"},s.a.createElement("i",{className:"material-icons action-image",onClick:function(){return d({url:"mailconv/mailhistory/"+e.chatId})}},"history"),s.a.createElement("a",{className:"material-icons action-image",onClick:function(){return d({url:"mailconv/transfermail/"+e.chatId})},title:"Transfer chat"},"supervisor_account"),s.a.createElement("a",{className:"text-dark material-icons",target:"_blank",href:WWW_DIR_JAVASCRIPT+"mailconv/mailprintcovnersation/"+e.chatId},"print"),s.a.createElement("a",{className:"material-icons mr-0",onClick:function(e){confirm("Are you sure?")&&m.a.post(WWW_DIR_JAVASCRIPT+"mailconv/apideleteconversation/"+r.conv.id).then((function(e){document.getElementById("chat-tab-link-mc"+r.conv.id)?lhinst.removeDialogTabMail("mc"+r.conv.id,$("#tabs"),!0):document.location=WWW_DIR_JAVASCRIPT+"mailconv/conversations"})).catch((function(e){}))},title:"Delete chat"},"delete"))),s.a.createElement("tr",null,s.a.createElement("td",null,"Sender"),s.a.createElement("td",null,r.conv.from_name," <",r.conv.from_address,">")),s.a.createElement("tr",null,s.a.createElement("td",null,"Status"),s.a.createElement("td",null,!r.conv.status&&s.a.createElement("span",null,s.a.createElement("i",{className:"material-icons chat-pending"},"mail_outline"),"Pending"),1==r.conv.status&&s.a.createElement("span",null,s.a.createElement("i",{className:"material-icons chat-active"},"mail_outline"),"Active"),2==r.conv.status&&s.a.createElement("span",null,s.a.createElement("i",{className:"material-icons chat-closed"},"mail_outline"),"Closed"))),s.a.createElement("tr",null,s.a.createElement("td",null,"Department"),s.a.createElement("td",null,r.conv.department_name)),s.a.createElement("tr",null,s.a.createElement("td",null,"Received"),s.a.createElement("td",null,r.conv.udate_front)),s.a.createElement("tr",null,s.a.createElement("td",null,"ID"),s.a.createElement("td",null,r.conv.id)),r.conv.accept_time&&s.a.createElement("tr",null,s.a.createElement("td",null,"Accepted at"),s.a.createElement("td",null,r.conv.accept_time_front," | Wait time ",r.conv.wait_time_pending)),r.conv.response_time&&s.a.createElement("tr",null,s.a.createElement("td",null,"Responded at"),s.a.createElement("td",null,r.conv.lr_time_front," | Wait time ",r.conv.wait_time_response)),r.conv.cls_time&&s.a.createElement("tr",null,s.a.createElement("td",null,"Closed at"),s.a.createElement("td",null,r.conv.cls_time_front)),r.conv.interaction_time&&s.a.createElement("tr",null,s.a.createElement("td",null,"Interaction time"),s.a.createElement("td",null,r.conv.interaction_time_duration)),r.conv.priority&&s.a.createElement("tr",null,s.a.createElement("td",null,"Priority"),s.a.createElement("td",null,r.conv.priority)),s.a.createElement("tr",null,s.a.createElement("td",null,"Chat owner"),s.a.createElement("td",null,r.conv.plain_user_name)))))))))}}}]);