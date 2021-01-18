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
            );
            echo json_encode($params); ?>;
            window.initParams = initParams;
    })();
</script>
