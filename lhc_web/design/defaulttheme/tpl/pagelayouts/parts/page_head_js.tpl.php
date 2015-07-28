<script type="text/javascript">
var WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
var WWW_DIR_JAVASCRIPT_FILES = '<?php echo erLhcoreClassDesign::design('sound')?>';
var WWW_DIR_LHC_WEBPACK = '<?php echo erLhcoreClassDesign::design('js/lh/dist')?>/';
var WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION = '<?php echo erLhcoreClassDesign::design('images/notification')?>';
var confLH = {};
<?php $soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data_value; ?>
confLH.back_office_sinterval = <?php echo (int)($soundData['back_office_sinterval']*1000) ?>;
confLH.chat_message_sinterval = <?php echo (int)($soundData['chat_message_sinterval']*1000) ?>;
confLH.new_chat_sound_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)($soundData['new_chat_sound_enabled'])) ?>;
confLH.new_message_sound_admin_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_admin_enabled'])) ?>;
confLH.new_message_sound_user_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_user_enabled'])) ?>;
confLH.new_message_browser_notification = <?php echo isset($soundData['browser_notification_message']) ? (int)($soundData['browser_notification_message']) : 0 ?>;
confLH.transLation = {'new_chat':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat request')?>'};
confLH.csrf_token = '<?php echo erLhcoreClassUser::instance()->getCSFRToken()?>';
confLH.repeat_sound = <?php echo (int)$soundData['repeat_sound']?>;
confLH.repeat_sound_delay = <?php echo (int)$soundData['repeat_sound_delay']?>;
confLH.show_alert = <?php echo (int)$soundData['show_alert']?>;
</script>
<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('vendor/jquery/jquery.min.js;vendor/bootstrap/js/bootstrap.min.js;js/modernizr.js;js/lh.min.js;js/jquery.hotkeys-0.7.9.min.js;js/fileupload/jquery.fileupload.min.js;js/jquery.zoom.min.js;js/datepicker.min.js;js/lh/dist/common.js;js/lh/dist/bundle.js');?>"></script>
<?php echo isset($Result['additional_header_js']) ? $Result['additional_header_js'] : ''?>