<?php 

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_user_enabled'])); ?>

  <div class="btn-group pull-right" role="group">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings_multiinclude.tpl.php'));?>
    
    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      <i class="material-icons mr-0">settings_applications</i>
      <span class="caret"></span>
    </button>  
    <ul role="menu" data-dropdown-content class="dropdown-menu widget-options">
		 
	     <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_sound.tpl.php'));?>
	     	     
	     <?php if (isset($chat)) : ?>
	     
    	     <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>
    		      <li><a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>" class="material-icons mat-100 mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Print')?>">print</a></li>
    		 <?php endif;?>
    		 				 
    		 <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>
    		      <li><a target="_blank" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/sendchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>'});return false;" href="#" class="material-icons mat-100 mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Send chat transcript to your e-mail')?>">email</a></li>
    		 <?php endif;?>
    		 
    		 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/user_file_upload.tpl.php'));?>

    		 <?php if ((int)erLhcoreClassModelChatConfig::fetch('hide_button_dropdown')->current_value == 0 && isset($chat_widget_mode) && $chat_widget_mode == true) : ?>
    		      <li><a onclick="lhinst.explicitChatCloseByUser();" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','End the chat')?>"><i class="material-icons mr-0">close</i></a></li>
    		 <?php endif;?>

		 <?php endif; ?>
		 
		 <?php include(erLhcoreClassDesign::designtpl('lhchat/customer_user_settings/option_last_multiinclude.tpl.php'));?>
		 
	</ul>
  </div>


<?php if ( isset($chat) && isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true ) : ?>
<script>
lhinst.addFileUserUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',hash:'<?php echo $chat->hash?>',chat_id:'<?php echo $chat->id?>',fs:<?php echo $fileData['fs_max']*1024?>,ft_us:/(\.|\/)(<?php echo $fileData['ft_us']?>)$/i});
</script>
<?php endif;?>