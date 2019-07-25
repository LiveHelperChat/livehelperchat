showSurvey : function() {
    if (this.cookieData.hash && this.hasSurvey == true && this.surveyShown == false){
        var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
        this.surveyShown = true;
        document.getElementById('<?php echo $chatCSSPrefix?>_iframe_page').contentWindow.location.replace("<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurlsite()?>"+this.lang+"/survey/fillwidget/(mode)/embed<?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $department !== false ? print '/(department)/'.$department : ''?><?php is_object($theme) ? print '/(theme)/'.$theme->id : ''?><?php $operator !== false ? print '/(operator)/'.$operator : ''?><?php $priority !== false ? print '/(priority)/'.$priority : ''?><?php $uarguments !== false ? print '/(ua)/'.$uarguments : '' ?>"+this.getAppendCookieArguments()+'?URLReferer='+locationCurrent+this.parseOptions()+this.parseStorageArguments()+'&dt='+encodeURIComponent(document.title));
    }
},