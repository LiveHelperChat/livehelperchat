<p style="padding-bottom: 5px"><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Some changes might take effect after you save a widget theme!');?></b></p>

<ul style="padding: 5px;">
    <li style="padding-bottom: 5px"><button type="button" onclick="clearCookiesReload()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Clear cookies and reload');?></button>&nbsp;<button type="button" onclick="document.location.reload()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Reload');?></button></li>
</ul>

<pre id="json-args"><?php echo htmlspecialchars('<script>');?><div id="json-args-content"></div><?php echo htmlspecialchars('</script>');?></pre>

<script>
    function clearCookiesReload() {
        window.$_LHC.attributes.userSession.setChatInformation({id:null,hash:null},false);
        var sessionAttributes = window.$_LHC.attributes.userSession.getSessionAttributes();
        if (sessionAttributes['hnh']) {
            delete sessionAttributes['hnh'];
        };
        window.$_LHC.attributes.storageHandler.storeSessionInformation(sessionAttributes);
        document.location.reload();
    }
    var LHC_API = LHC_API||{};
    LHC_API.args = {mode:'widget',lhc_base_url:'//<?php echo str_replace(['http://','https://'],'',erLhcoreClassSystem::getHost())?><?php echo erLhcoreClassDesign::baseurldirect()?>',wheight:450,wwidth:350,pheight:520,pwidth:500, department : <?php echo json_encode($department)?>, leaveamessage:true,check_messages:false};
    LHC_API.args['theme'] = <?php echo json_encode(is_numeric($theme) && $theme > 0 && ($themeObj = erLhAbstractModelWidgetTheme::fetch($theme)) instanceof erLhAbstractModelWidgetTheme ? ($themeObj->alias != '' ? $themeObj->alias : $themeObj->id) : null);?>;
    document.getElementById('json-args-content').innerText = "var LHC_API = LHC_API||{};\nLHC_API = "+JSON.stringify(LHC_API, null, 2);
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.setAttribute('crossorigin','anonymous'); po.async = true;
        var date = new Date();po.src = '//<?php echo $_SERVER['HTTP_HOST']?>/design/defaulttheme/js/widgetv2/index.js?'+(""+date.getFullYear() + date.getMonth() + date.getDate());
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script>