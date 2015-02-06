<?php 

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;
$soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_user_enabled'])); ?>

<div class="right pos-rel">
	<a href="#" data-dropdown="drop2" class="tiny secondary round button dropdown"><i class="icon-tools"></i></a>

	<ul id="drop2" data-dropdown-content class="f-dropdown widget-options">
		 
		 <li><a href="#" class="icon-sound <?php $soundMessageEnabled == 0 ? print ' icon-mute' : ''?>" onclick="return lhinst.disableChatSoundUser($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from the operator');?>"></a></li>
	     
	     
	     <?php if ( isset($chat) ) : ?>
	     
	     <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 0) : ?>
		 <li><a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/printchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>" class="icon-print" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Print')?>"></a></li>
		 <?php endif;?>
		 				 
		 <?php if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0) : ?>
		 <li><a target="_blank" onclick="lhinst.revealModal('<?php echo erLhcoreClassDesign::baseurl('chat/sendchat')?>/<?php echo $chat->id?>/<?php echo $chat->hash?>');return false;" href="#" class="icon-mail" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Send chat transcript to your e-mail')?>"></a></li>
		 <?php endif;?>
		 
		 <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>

		 <?php if (isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true) : ?>
		 <li>
		 <a class="file-uploader icon-attach" href="#">
		        <!-- The file input field used as target for the file upload widget -->
		        <input id="fileupload" type="file" name="files[]" multiple>
		 </a>
		 </li>
		 <?php endif;?>

		 <?php endif;?>
	</ul>
</div>

<?php if ( isset($chat) && isset($fileData['active_user_upload']) && $fileData['active_user_upload'] == true ) : ?>
<script>
lhinst.addFileUserUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',hash:'<?php echo $chat->hash?>',chat_id:'<?php echo $chat->id?>',fs:<?php echo $fileData['fs_max']*1024?>,ft_us:/(\.|\/)(<?php echo $fileData['ft_us']?>)$/i});
</script>
<?php endif;?>