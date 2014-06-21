<?php if (isset($request_send)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your request was sent!');?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php else : ?>
<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>/(offline)/true/(leaveamessage)/true<?php $department !== false ? print '/(department)/'.$department : ''?><?php $input_data->chatprefill !== '' ? print '/(chatprefill)/'.htmlspecialchars($input_data->chatprefill) : ''?>" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

<?php if (isset($start_data_fields['offline_name_visible_in_popup']) && $start_data_fields['offline_name_visible_in_popup'] == true) : ?>
<?php if (isset($start_data_fields['offline_name_hidden']) && $start_data_fields['offline_name_hidden'] == true) : ?>
<input type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
<?php else : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required') : ?>*<?php endif;?></label>
<input type="text" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
<?php endif; ?>
<?php endif; ?>

<?php if (isset($start_data_fields['offline_email_hidden']) && $start_data_fields['offline_email_hidden'] == true) : ?>
<input type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
<?php else : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?>*</label>
<input type="text" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
<?php endif;?>

<?php if (isset($start_data_fields['offline_phone_visible_in_popup']) && $start_data_fields['offline_phone_visible_in_popup'] == true) : ?>
<?php if (isset($start_data_fields['offline_phone_hidden']) && $start_data_fields['offline_phone_hidden'] == true) : ?>
<input type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php else : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required') : ?>*<?php endif;?></label>
<input type="text" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php endif;?>
<?php endif; ?>

<?php if (isset($start_data_fields['offline_message_visible_in_popup']) && $start_data_fields['offline_message_visible_in_popup'] == true) : ?>
<?php if (isset($start_data_fields['offline_message_hidden']) && $start_data_fields['offline_message_hidden'] == true) : ?>
<textarea class="hide" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php else : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?><?php if (isset($start_data_fields['offline_message_require_option']) && $start_data_fields['offline_message_require_option'] == 'required') : ?>*<?php endif;?></label>
<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php endif;?>
<?php endif;?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php if ($department === false) : ?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>
<?php endif;?>

<?php $tosVariable = 'offline_tos_visible_in_popup'?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/accept_tos.tpl.php'));?>

<ul class="button-group radius">
  	<li><input type="submit" class="small secondary button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Leave a message');?>" name="StartChatAction" /></li>
  	<?php if ( erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly(array('reopen_closed' => erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value))) !== false ) : ?>
		<li><input type="button" class="small success button"  value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?>" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $reopenData['id']?>/<?php echo $reopenData['hash']?><?php if ( isset($modeAppend) && $modeAppend != '' ) : ?>/(embedmode)/embed<?php endif;?>'"></li>
	<?php endif; ?>
</ul>

<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>
<input type="hidden" value="1" name="StartChat"/>

</form>
<?php endif;?>