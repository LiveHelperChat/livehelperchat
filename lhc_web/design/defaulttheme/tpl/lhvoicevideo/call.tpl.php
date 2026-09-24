<?php if (erLhcoreClassVoiceVideo::isEnabled()) : ?>
<script>
    var WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
    var WWW_DIR_LHC_WEBPACK_ADMIN = '<?php echo erLhcoreClassDesign::design('js/voice')?>/';
    var confLH = {};
    confLH.lngUser = '<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>';
    (function (){
        var initParams = <?php echo json_encode(erLhcoreClassVoiceVideo::getClientParams($chat, true)); ?>;
            window.initParams = initParams;
    })();
</script>
<?php else : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/errors/adminchatnopermission.tpl.php'));?>
<?php endif; ?>
