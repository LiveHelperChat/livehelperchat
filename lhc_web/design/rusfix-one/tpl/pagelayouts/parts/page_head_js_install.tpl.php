<script type="text/javascript">
var WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
var WWW_DIR_JAVASCRIPT_FILES = '<?php echo erLhcoreClassDesign::design('sound')?>';
var confLH = {};
confLH.back_office_sinterval = <?php echo (int)(erConfigClassLhConfig::getInstance()->getSetting('chat','back_office_sinterval')*1000) ?>;
confLH.chat_message_sinterval = <?php echo (int)(erConfigClassLhConfig::getInstance()->getSetting('chat','chat_message_sinterval')*1000) ?>;
confLH.new_chat_sound_enabled = 1;
confLH.new_message_sound_admin_enabled =1;
confLH.new_message_sound_user_enabled = 1;
</script>
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/jquery.js;js/modernizr.js;js/foundation.min.js;js/jquery.colorbox-min.js;js/lh.js;js/jquery.hotkeys-0.7.9.min.js');?>"></script>