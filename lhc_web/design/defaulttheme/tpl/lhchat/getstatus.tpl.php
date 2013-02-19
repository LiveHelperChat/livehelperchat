var lh_inst  = {

    urlopen : "http://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>",
    
    windowname : "startchatwindow",
    
    addCss : function(css_content) {
        var head = document.getElementsByTagName('head')[0];
        var style = document.createElement('style');
        style.type = 'text/css';
        
        if(style.styleSheet) {
          style.styleSheet.cssText = css_content;
        } else {
          rules = document.createTextNode(css_content);
          style.appendChild(rules);
        }
        
        head.appendChild(style);
    },
    
    appendHTML : function (htmlStr) {
        var frag = document.createDocumentFragment(),
            temp = document.createElement('div');
        temp.innerHTML = htmlStr;
        while (temp.firstChild) {
            frag.appendChild(temp.firstChild);
        }
        return frag;
    },

    removeById : function(EId)
    {
        return(EObj=document.getElementById(EId))?EObj.parentNode.removeChild(EObj):false;
    },

    hide : function() {    
        this.removeById('lhc_container');
    },
    
    showStartWindow : function() {

           this.initial_iframe_url = "http://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?>";

           this.iframe_html = '<iframe id="fdbk_iframe" allowTransparency="true" scrolling="no" frameborder="0" class="loading"' +
                       ( this.initial_iframe_url != '' ? ' src="'    + this.initial_iframe_url + '"' : '' ) +
                       ' width="300"' +
                       ' height="320"' +
                       ' style="width: 300px; height: 320px;"></iframe>';

           this.overlay_html = '<div id="lhc_container">' +
                              '<div id="lhc_header"><span id="lhc_title">Live support</span><a href="#" id="lhc_close">Close</a></div>' +
                              this.iframe_html + '</div>';

          raw_css = "#lhc_container * {font-family:arial;font-size:12px;}\n#lhc_title{float:left;}\n#lhc_header{text-align:right;clear:both;border-bottom:1px solid #CCC;padding:5px;}\n#lhc_container {\n  ;width:300px;\n  height: 345px;\n position: absolute;bottom:0;right:0;-webkit-box-shadow: -2px -2px 5px rgba(50, 50, 50, 0.17);-moz-box-shadow:    -2px -2px 5px rgba(50, 50, 50, 0.17);box-shadow:         -2px -2px 5px rgba(50, 50, 50, 0.17);border:1px solid #CCC;-webkit-border-top-left-radius: 10px;-moz-border-radius-topleft: 10px;border-top-left-radius: 10px; }\n  #fdbk_container iframe {\n    width: 658px;\n    height: 100%;\n    margin: 20px;\n    background: transparent; }\n  #fdbk_container iframe.loading {\n    background: transparent url(https://d1xklv3tn7qmp2.cloudfront.net/_cb/8e27877/assets/fb_loading.png) no-repeat; }\n\na#fdbk_tab {\n  top: 25%;\n  left: 0;\n  width: 42px;\n  height: 102px;\n  color: white;\n  cursor: pointer;\n  text-indent: -100000px;\n  overflow: hidden;\n  position: fixed;\n  z-index: 100000;\n  margin-left: -7px;\n  background-image: url(https://d1xklv3tn7qmp2.cloudfront.net/_cb/8e27877/assets/feedback_trans_tab.png);\n  _position: absolute;\n  _background-image: url(https://d1xklv3tn7qmp2.cloudfront.net/_cb/8e27877/assets/feedback_tab_ie6.png); }\n  a#fdbk_tab:hover {\n    margin-left: -4px; }\n\na.fdbk_tab_right {\n  right: 0 !important;\n  left: auto !important;\n  margin-right: 0 !important;\n  margin-left: auto !important;\n  width: 35px !important; }\n  a.fdbk_tab_right:hover {\n    width: 38px !important;\n    margin-right: 0 !important;\n    margin-left: auto !important; }\n\na.fdbk_tab_bottom {\n  top: auto !important;\n  bottom: 0 !important;\n  left: 20% !important;\n  height: 38px !important;\n  width: 102px !important;\n  background-position: 0 -102px !important;\n  margin-bottom: -7px !important;\n  margin-left: auto !important; }\n  a.fdbk_tab_bottom:hover {\n    margin-bottom: -4px !important;\n    margin-left: auto !important; }\n\na.fdbk_tab_hidden {\n  display: none !important; }\n\na#lhc_close {text-align:right; color:#CCC;text-decoration:none;font-weight:bold; }\n  a#lhc_close:hover {\ntext-decoration:underline}\n\n.feedback_tab_on embed, .feedback_tab_on select, .feedback_tab_on object {\n  visibility: hidden; }\n";
          this.addCss(raw_css);

          var fragment = this.appendHTML(this.overlay_html);

          // You can use native DOM methods to insert the fragment:
          document.body.insertBefore(fragment, document.body.childNodes[0]);  

          
          var lhc_obj = this;
          document.getElementById('lhc_close').onclick = function() { lhc_obj.hide(); return false; }
    },
    
    lh_openchatWindow : function() {
        <?php if ($click == 'internal') : ?>
        this.showStartWindow();
        <?php else : ?>
        window.open(this.urlopen+'?URLReferer='+escape(document.location),this.windowname,"menubar=1,resizable=1,width=500,height=520");	      
        <?php endif; ?>
        return false;
    }
};

<?php if (erLhcoreClassChat::isOnline() === true) { ?>
document.write('<p><a href="#" onclick="lh_inst.lh_openchatWindow()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is online...")?></a></p>');
<?php } else { ?>
document.write('<p><a href="#" onclick="lh_inst.lh_openchatWindow()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus',"Live help is offline...")?></a></p>');
<?php }  exit; ?>