<?php include(erLhcoreClassDesign::designtpl('lhchat/part/offline_from_startchat_pre.tpl.php'));?>

<?php if ($chat_part_offline_form_start_chat_enabled == true) : ?>

<?php if (isset($request_send)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your request was sent!');?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php else : ?>
<form enctype="multipart/form-data" method="post" id="form-start-chat" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>/(offline)/true/(leaveamessage)/true<?php $department !== false ? print '/(department)/'.$department : ''?><?php $input_data->chatprefill !== '' ? print '/(chatprefill)/'.htmlspecialchars($input_data->chatprefill) : ''?><?php echo $append_mode_theme?>" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

<?php if (isset($start_data_fields['offline_name_visible_in_popup']) && $start_data_fields['offline_name_visible_in_popup'] == true) : ?>
<?php if (isset($start_data_fields['offline_name_hidden']) && $start_data_fields['offline_name_hidden'] == true) : ?>
<input type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
<?php else : ?>
<div class="form-group<?php if (isset($errors['nick'])) : ?> has-error<?php endif;?>">
<label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required') : ?>*<?php endif;?></label>
<input class="form-control" type="text" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
</div>
<?php endif; ?>
<?php endif; ?>

<?php if (isset($start_data_fields['offline_email_hidden']) && $start_data_fields['offline_email_hidden'] == true) : ?>
<input type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
<?php else : ?>
<div class="form-group<?php if (isset($errors['email'])) : ?> has-error<?php endif;?>">
    <label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?>*</label>
    <input class="form-control" type="text" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
</div>
<?php endif;?>

<?php if (isset($start_data_fields['offline_phone_visible_in_popup']) && $start_data_fields['offline_phone_visible_in_popup'] == true) : ?>
<?php if (isset($start_data_fields['offline_phone_hidden']) && $start_data_fields['offline_phone_hidden'] == true) : ?>
<input type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php else : ?>
<div class="form-group<?php if (isset($errors['phone'])) : ?> has-error<?php endif;?>">
    <label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required') : ?>*<?php endif;?></label>
    <input class="form-control" type="text" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
</div>
<?php endif;?>
<?php endif; ?>

<?php if (isset($start_data_fields['offline_file_visible_in_popup']) && $start_data_fields['offline_file_visible_in_popup'] == true) : ?>
<div class="form-group">
    <label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','File');?></label>
    <input type="file" name="File" value="" />
</div>
<?php endif; ?>

<?php if (isset($start_data_fields['offline_message_visible_in_popup']) && $start_data_fields['offline_message_visible_in_popup'] == true) : ?>
<?php if (isset($start_data_fields['offline_message_hidden']) && $start_data_fields['offline_message_hidden'] == true) : ?>
<textarea class="hide" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php else : ?>
<div class="form-group<?php if (isset($errors['question'])) : ?> has-error<?php endif;?>">
    <label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?><?php if (isset($start_data_fields['offline_message_require_option']) && $start_data_fields['offline_message_require_option'] == 'required') : ?>*<?php endif;?></label>
    <textarea class="form-control" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
</div>
<?php endif;?>
<?php endif;?>

<?php $adminCustomFieldsMode = 'off';?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/admin_form_variables.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php if ($department === false) : ?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>
<?php endif;?>

<?php $tosVariable = 'offline_tos_visible_in_popup';$tosCheckedVariable = 'tos_checked_offline';?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/accept_tos.tpl.php'));?>

<div class="btn-group" role="group" aria-label="...">
  	<input type="submit" class="btn btn-default leaveamessage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message');?>" name="StartChatAction" />
  	<?php include(erLhcoreClassDesign::designtpl('lhchat/offline_form_startchat_button_multiinclude.tpl.php'));?>
  	<?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly(array('reopen_closed' => erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value))) !== false ) : ?>
		<input type="button" class="btn btn-default resumechat"  value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?>" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $reopenData['id']?>/<?php echo $reopenData['hash']?><?php if ( isset($modeAppend) && $modeAppend != '' ) : ?>/(embedmode)/embed<?php endif;?>'">
	<?php endif; ?>
</div>

<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>
<input type="hidden" value="1" name="StartChat"/>

</form>
<?php endif;?>

<?php else : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/offline_form_startchat_disabled.tpl.php'));?>
<?php endif;?>