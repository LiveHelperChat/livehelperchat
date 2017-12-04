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
confLH.transLation = {'delete_confirm':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure you want to delete this chat?')?>','new_chat':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat request')?>','transfered':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat has been transferred to you directly!')?>'};
confLH.csrf_token = '<?php echo erLhcoreClassUser::instance()->getCSFRToken()?>';
confLH.repeat_sound = <?php echo (int)$soundData['repeat_sound']?>;
confLH.repeat_sound_delay = <?php echo (int)$soundData['repeat_sound_delay']?>;
confLH.show_alert = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('show_alert_chat',0)?>;
confLH.user_id = '<?php echo erLhcoreClassUser::instance()->getUserID()?>';
confLH.sn_off = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('sn_off',1)?>;
confLH.ownntfonly = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('ownntfonly',0)?>;
confLH.show_alert_transfer = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('show_alert_transfer',1)?>;
confLH.accept_chats = <?php if (erLhcoreClassUser::instance()->isLogged()) { print (int)erLhcoreClassUser::instance()->getUserData()->auto_accept; } else {print 0;}?>;
</script>
<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('vendor/jquery/jquery.min.js;vendor/bootstrap/js/bootstrap.min.js;js/modernizr.js;js/lh.min.js;js/lh.cannedmsg.min.js;js/jquery.hotkeys.min.js;js/fileupload/jquery.fileupload.min.js;js/jquery.zoom.min.js;js/datepicker.min.js;js/lh/dist/common.js;js/lh/dist/bundle.js;js/EventEmitter.min.js;js/events.js');?>"></script>
<?php echo isset($Result['additional_header_js']) ? $Result['additional_header_js'] : ''?>