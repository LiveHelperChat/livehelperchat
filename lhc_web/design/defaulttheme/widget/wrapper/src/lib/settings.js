var settings = {};

settings.ResetStyle = "html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent}body{line-height:1}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}nav ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none}a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent}ins{background-color:#ff9;color:#000;text-decoration:none}mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold}del{text-decoration:line-through}abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help}table{border-collapse:collapse;border-spacing:0}hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0}input,select{vertical-align:middle}html,body{height: 100% !important;\n" +
    "        min-height: 100% !important;\n" +
    "        max-height: 100% !important;\n" +
    "        width: 100% !important;\n" +
    "        min-width: 100% !important;\n" +
    "        max-width: 100% !important;}body{display: flex;flex-direction: column;background:transparent;font:13px Helvetica,Arial,sans-serif;position:relative}.clear{clear:both}.clearfix:after{content:'';display:block;height:0;clear:both;visibility:hidden}";
settings.FontStyle = '@font-face {  font-family: \'Material Icons\';  font-style: normal;  font-weight: 400;  src: url(\'./fonts/materialdesignicons-webfont.eot\'); /* For IE6-8 */  src: local(\'Material Icons\'),       local(\'MaterialIcons-Regular\'),       url(\'./fonts/materialdesignicons-webfont.woff2\') format(\'woff2\'),       url(\'./fonts/materialdesignicons-webfont.woff\') format(\'woff\'),       url(\'./fonts/materialdesignicons-webfont.ttf\') format(\'truetype\');}.material-icons {  font-family: \'Material Icons\';  font-weight: normal;  font-style: normal;  font-size: 18px;  /* Preferred icon size */  display: inline-block;  line-height: 1;  text-transform: none;  letter-spacing: normal;  word-wrap: normal;  width: 1em;  /* Support for all WebKit browsers. */  -webkit-font-smoothing: antialiased;  /* Support for Safari and Chrome. */  text-rendering: optimizeLegibility;  /* Support for Firefox. */  -moz-osx-font-smoothing: grayscale;  /* Support for IE. */  font-feature-settings: \'liga\';  vertical-align: middle;  margin-right:5px;  overflow:hidden;  }';
settings.ChatStatus = '#unread-msg-number{float: left;color: #ffffff;font-size: 12px;font-weight: normal;line-height: 19px;position: absolute;background-color: red;border-radius: 37px;display: none;padding-left: 8px;padding-right: 8px;margin-top: -5px;margin-left: -4px;}#lhc_status_container.has-uread-message #unread-msg-number{display: inline-block} #lhc_status_container .offline-status { background: #888888 url(//clients.livehelperchat.com/design/defaulttheme/images/getstatus/offline.svg) no-repeat center !important;}#lhc_status_container{padding-top:15px;padding-left:15px;}#lhc_status_container #status-icon {border: 2px solid #e3e3e3;    -webkit-border-radius: 47px;    -moz-border-radius:47px ;    border-radius: 47px;    -webkit-box-shadow: 0px 0px 17px rgba(50, 50, 50, 0.5);    -moz-box-shadow: 0px 0px 17px rgba(50, 50, 50, 0.5);    box-shadow: 0px 0px 17px rgba(50, 50, 50, 0.5);    text-decoration: none;    height: 41px;    width: 41px;    font-weight: bold;    color: #000000;    display: block;    padding: 10px;    background: #0c8fc4 url(//devmysql.livehelperchat.com/design/defaulttheme/images/getstatus/online.svg) no-repeat center center;}';

settings.defaultIframeStyle = {
    src: "about:blank",
    border: "0",
    cellspacing: "0",
    frameBorder: "0",
    scrolling: "no",
    horizontalscrolling: "no",
    verticalscrolling: "no",
    allowTransparency: "true",
    title: "chat widget"
};

export {settings};