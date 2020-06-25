<script type="text/javascript">
var WWW_DIR_JAVASCRIPT = '<?php echo erLhcoreClassDesign::baseurl()?>';
var WWW_DIR_JAVASCRIPT_FILES = '<?php echo erLhcoreClassDesign::design('sound')?>';
var WWW_DIR_LHC_WEBPACK = '<?php echo erLhcoreClassDesign::design('js/lh/dist')?>/';
var WWW_DIR_LHC_WEBPACK_ADMIN = '<?php echo erLhcoreClassDesign::design('js/admin/dist')?>/';
var WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION = '<?php echo erLhcoreClassDesign::design('images/notification')?>';
var confLH = {};
<?php $soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data_value; ?>
confLH.back_office_sinterval = <?php echo (int)($soundData['back_office_sinterval']*1000) ?>;
confLH.chat_message_sinterval = <?php echo (int)($soundData['chat_message_sinterval']*1000) ?>;
confLH.transLation = {'delete_confirm':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Are you sure you want to delete this chat?')?>','new_chat':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat request')?>','transfered':'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chat has been transferred to you directly!')?>'};
confLH.new_message_sound_user_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_user_enabled'])) ?>;
<?php if (!isset($Result['anonymous'])) : ?>
confLH.csrf_token = '<?php echo erLhcoreClassUser::instance()->getCSFRToken()?>';
confLH.user_id = '<?php echo erLhcoreClassUser::instance()->getUserID()?>';
confLH.show_alert_transfer = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('show_alert_transfer',1)?>;
confLH.show_alert = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('show_alert_chat',0)?>;
confLH.auto_join_private = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('auto_join_private',1)?>;
confLH.new_message_sound_admin_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_admin_enabled'])) ?>;
confLH.new_message_browser_notification = <?php echo isset($soundData['browser_notification_message']) ? (int)($soundData['browser_notification_message']) : 0 ?>;
confLH.new_chat_sound_enabled = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)($soundData['new_chat_sound_enabled'])) ?>;
confLH.sn_off = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('sn_off',1)?>;
confLH.ownntfonly = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('ownntfonly',0)?>;
confLH.accept_chats = <?php if (erLhcoreClassUser::instance()->isLogged()) { print (int)erLhcoreClassUser::instance()->getUserData()->auto_accept; } else {print 0;}?>;
confLH.auto_uppercase = <?php echo (int)erLhcoreClassModelUserSetting::getSetting('auto_uppercase',1)?>;
<?php else : ?>
confLH.csrf_token = '<?php echo erLhcoreClassUser::anonymousGetCSFRToken()?>';
<?php endif;?>
<?php if (isset($Result['chat_init_data'])) : ?>
confLH.chat_init = <?php echo json_encode($Result['chat_init_data']);?>;
<?php endif; ?>
confLH.repeat_sound = <?php echo (int)$soundData['repeat_sound']?>;
confLH.repeat_sound_delay = <?php echo (int)$soundData['repeat_sound_delay']?>;
confLH.content_language = '<?php echo erLhcoreClassSystem::instance()->ContentLanguage?>';
confLH.defaultm_hegiht = '<?php echo erLhcoreClassModelChatConfig::fetch('mheight_op')->current_value;?>';
confLH.dlist = {'op_n':'<?php echo erLhcoreClassModelChatConfig::fetch('listd_op')->current_value;?>'};
confLH.lngUser = '<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>';
<?php $geo_location_data = erLhcoreClassModelChatConfig::fetch('geo_location_data')->data_value; ?>
confLH.gmaps_api_key = "<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false)) {echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'maps_api_key', false);} elseif (isset($geo_location_data['gmaps_api_key'])) {echo $geo_location_data['gmaps_api_key'];}?>";
</script>

<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('vendor/jquery/jquery.min.js;vendor/bootstrap/js/bootstrap.min.js;js/modernizr.js;js/lh.min.js;js/lh.cannedmsg.min.js;js/jquery.hotkeys.min.js;js/fileupload/jquery.fileupload.min.js;js/jquery.zoom.min.js;js/datepicker.min.js;js/lh/dist/common.js;js/lh/dist/bundle.js;js/EventEmitter.min.js;js/events.js;js/notifiations.js;js/color-picker.min.js;js/admin/dist/react.admin.app.js');?>"></script>
<?php echo isset($Result['additional_header_js']) ? $Result['additional_header_js'] : ''?>