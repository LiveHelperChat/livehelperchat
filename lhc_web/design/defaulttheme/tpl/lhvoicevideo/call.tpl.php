<?php if ($voice_data['voice'] && $voice_data['voice'] == 1) : ?>
<script>
    var WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
    var WWW_DIR_LHC_WEBPACK_ADMIN = '<?php echo erLhcoreClassDesign::design('js/voice')?>/';
    var confLH = {};
    confLH.lngUser = '<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>';
    (function (){
        var initParams = <?php
            $params = array (
                'isVisitor' => true,
                'id' => $chat->id,
                'hash' => $chat->hash,
                'appid' => $voice_data['agora_app_id'],
                'options' => array(
                        'video' => ($voice_data['video'] && $voice_data['video'] == 1),
                        'screenshare' => ($voice_data['screenshare'] && $voice_data['screenshare'] == 1)
                )
            );
            echo json_encode($params); ?>;
            window.initParams = initParams;
    })();
</script>
<?php else : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/errors/adminchatnopermission.tpl.php'));?>
<?php endif; ?>
