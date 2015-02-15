<?php if ($disabled_department === true) : ?>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department is disabled');?></h4>

<?php else : ?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/widget_geo_adjustment.tpl.php'));?>
<?php if ($exitTemplate == true) return; ?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if ($leaveamessage == false || ($forceoffline === false && erLhcoreClassChat::isOnline($department,false,array('ignore_user_status'=> (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']))) === true) : ?>

<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill out this form to start a chat');?></h4>

<form id="form-start-chat" method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $input_data->priority !== false ? print '/(priority)/'.$input_data->priority : ''?><?php $input_data->vid !== false ? print '/(vid)/'.htmlspecialchars($input_data->vid) : ''?><?php $input_data->hash_resume !== false ? print '/(hash_resume)/'.htmlspecialchars($input_data->hash_resume) : ''?>" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

<?php if (isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) : ?>
	<?php if (isset($start_data_fields['name_hidden']) && $start_data_fields['name_hidden'] == true) : ?>
	<input type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
	<?php else : ?>
		<?php if (in_array('username', $input_data->hattr)) : ?>
			<input class="form-control" type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
		<?php else : ?>
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') : ?>*<?php endif;?></label>
			<input class="form-control" type="text" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
		</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php if (isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) : ?>
	<?php if (isset($start_data_fields['email_hidden']) && $start_data_fields['email_hidden'] == true) : ?>
	<input type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
	<?php else : ?>
		<?php if (in_array('email', $input_data->hattr)) : ?>
			<input class="form-control" type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
		<?php else : ?>
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?><?php if (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required') : ?>*<?php endif;?></label>
			<input class="form-control" type="text" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
		</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php if (isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true) : ?>

<?php if (isset($start_data_fields['phone_hidden']) && $start_data_fields['phone_hidden'] == true) : ?>
<input type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php else : ?>
		<?php if (in_array('phone', $input_data->hattr)) : ?>
			<input class="form-control" type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
		<?php else : ?>
		<div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') : ?>*<?php endif;?></label>
			<input class="form-control" type="text" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
		</div>
		<?php endif; ?>
<?php endif; ?>

<?php endif; ?>

<?php if (isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) : ?>
	<?php if (isset($start_data_fields['message_hidden']) && $start_data_fields['message_hidden'] == true) : ?>
	<textarea class="hide" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
	<?php else : ?>
	<div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?><?php if (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required') : ?>*<?php endif;?></label>
	   <textarea class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/user_timezone.tpl.php'));?>

<?php if ($department === false) : ?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>
<?php endif;?>

<?php $tosVariable = 'tos_visible_in_popup'?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/accept_tos.tpl.php'));?>

<div class="btn-group" role="group" aria-label="...">
  <input type="submit" class="btn btn-default btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChatAction" />
  <?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly(array('reopen_closed' => erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value))) !== false ) : ?>
  <input type="button" class="btn btn-default btn-sm"  value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?>" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $reopenData['id']?>/<?php echo $reopenData['hash']?><?php if ( isset($modeAppend) && $modeAppend != '' ) : ?>/(embedmode)/embed<?php endif;?>'">
  <?php endif; ?>
</div>


<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>
<input type="hidden" value="<?php echo htmlspecialchars($referer_site);?>" name="r" />
<input type="hidden" value="<?php echo htmlspecialchars($input_data->operator);?>" name="operator" />
<input type="hidden" value="1" name="StartChat"/>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/switch_to_offline.tpl.php'));?>

</form>
<?php else : ?>
	<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','There are no online operators at the moment, please leave your message')?></h1>
	<?php include(erLhcoreClassDesign::designtpl('lhchat/offline_form_startchat.tpl.php'));?>
<?php endif;?>

<?php endif;?>