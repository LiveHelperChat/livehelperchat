<?php if (isset($chat) && erLhcoreClassVoiceVideo::isEnabled()) : ?>
<div id="root" class="container-fluid d-flex flex-column flex-grow-1 overflow-auto">
</div>
<script>
    var WWW_DIR_LHC_WEBPACK_ADMIN = '<?php echo erLhcoreClassDesign::design('js/voice')?>/';
    (function (){
        var initParams = <?php echo json_encode(erLhcoreClassVoiceVideo::getClientParams($chat, false)); ?>;
        window.initParams = initParams;

    })();
</script>
<script src="<?php echo erLhcoreClassDesign::designJS('js/voice/voice.call.js');?>?t=<?php echo time()?>"></script>
<?php else : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/errors/adminchatnopermission.tpl.php'));?>
<?php endif; ?>