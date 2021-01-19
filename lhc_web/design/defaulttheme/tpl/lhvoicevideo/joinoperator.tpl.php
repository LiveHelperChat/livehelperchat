<?php if ($voice_data['voice'] && $voice_data['voice'] == 1) : ?>
<div id="root" class="container-fluid d-flex flex-column flex-grow-1 overflow-auto">
</div>
<script>
    var WWW_DIR_LHC_WEBPACK_ADMIN = '<?php echo erLhcoreClassDesign::design('js/voice')?>/';
    (function (){
        var initParams = <?php
            $params = array (
                'id' => $chat->id,
                'hash' => $chat->hash,
                'isVisitor' => false,
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
<script src="<?php echo erLhcoreClassDesign::designJS('js/voice/voice.call.js');?>?t=<?php echo time()?>"></script>
<?php else : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/errors/adminchatnopermission.tpl.php'));?>
<?php endif; ?>