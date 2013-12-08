<script type="text/javascript">
var WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
var WWW_DIR_JAVASCRIPT_FILES = '<?php echo erLhcoreClassDesign::design('sound')?>';
var WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION = '<?php echo erLhcoreClassDesign::design('images/notification')?>';
var confLH = {};
confLH.back_office_sinterval = <?php echo (int)(erConfigClassLhConfig::getInstance()->getSetting('chat','back_office_sinterval')*1000) ?>;
confLH.chat_message_sinterval = <?php echo (int)(erConfigClassLhConfig::getInstance()->getSetting('chat','chat_message_sinterval')*1000) ?>;
confLH.new_chat_sound_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_chat_sound_enabled'))) ?>;
confLH.new_message_sound_admin_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_message',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_message_sound_admin_enabled'))) ?>;
confLH.new_message_sound_user_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_message',(int)(erConfigClassLhConfig::getInstance()->getSetting('chat','new_message_sound_user_enabled'))) ?>;
confLH.transLation = {'new_chat':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat request')?>'};
confLH.csrf_token = '<?php echo erLhcoreClassUser::instance()->getCSFRToken()?>';
</script>
<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/jquery.js;js/modernizr.js;js/foundation.min.js;js/jquery.colorbox-min.js;js/lh.js;js/jquery.hotkeys-0.7.9.min.js;js/fileupload/jquery.ui.widget.js;js/fileupload/jquery.iframe-transport.js;js/fileupload/jquery.fileupload.js;js/jquery.sceditor.bbcode.js');?>"></script>