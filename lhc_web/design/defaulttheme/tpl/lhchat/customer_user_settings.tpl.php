<?php 

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_user_enabled'])); ?>



  <div class="btn-group pull-right" role="group">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings_multiinclude.tpl.php'));?>
    
    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      <i class="icon-tools"></i>
      <span class="caret"></span>
    </button>  
    <ul role="menu" data-dropdown-content class="dropdown-menu widget-options">
		 
		 <li><a href="#" class="icon-sound <?php $soundMessageEnabled == 0 ? print ' icon-mute' : ''?>" onclick="return lhinst.disableChatSoundUser($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from the operator');?>"></a></li>
	     	     
	     <?php if ( isset($chat) ) : ?>
	     
	     <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>
		 <li><a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>" class="icon-print" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Print')?>"></a></li>
		 <?php endif;?>
		 				 
		 <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>
		 <li><a target="_blank" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/sendchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>'});return false;" href="#" class="icon-mail" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Send chat transcript to your e-mail')?>"></a></li>
		 <?php endif;?>
		 
		 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/user_file_upload.tpl.php'));?>

		 <?php endif;?>
	</ul>
  </div>


<?php if ( isset($chat) && isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true ) : ?>
<script>
lhinst.addFileUserUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',hash:'<?php echo $chat->hash?>',chat_id:'<?php echo $chat->id?>',fs:<?php echo $fileData['fs_max']*1024?>,ft_us:/(\.|\/)(<?php echo $fileData['ft_us']?>)$/i});
</script>
<?php endif;?>