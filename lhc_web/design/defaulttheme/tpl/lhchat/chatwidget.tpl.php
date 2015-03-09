<?php if ($disabled_department === true) : ?>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department is disabled');?></h1>

<?php else : ?>

<?php if (!isset($start_data_fields['show_operator_profile']) || $start_data_fields['show_operator_profile'] == false) : ?>
<div class="pl10 pos-rel max-width-180 pull-right">
	<?php $rightLanguage = true;?>
	<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
</div>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/widget_geo_adjustment.tpl.php'));?>
<?php if ($exitTemplate == true) return; ?>

<?php if ($leaveamessage == false || ($forceoffline === false && erLhcoreClassChat::isOnline($department, false, array('ignore_user_status'=> (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'])) === true)) : ?>

<?php if (isset($start_data_fields['show_operator_profile']) && $start_data_fields['show_operator_profile'] == true) : ?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_start_chat.tpl.php'));?>
<?php endif;?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php 
$hasExtraField = false; 
if ($theme !== false && $theme->explain_text != '') : ?>
<p class="start-chat-intro"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($theme->explain_text))?></p>
<?php endif;?>

<form method="post" id="form-start-chat" action="<?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?><?php echo $append_mode?><?php $department !== false ? print '/(department)/'.$department : ''?><?php $input_data->priority !== false ? print '/(priority)/'.$input_data->priority : ''?><?php $input_data->vid !== false ? print '/(vid)/'.htmlspecialchars($input_data->vid) : ''?><?php $input_data->hash_resume !== false ? print '/(hash_resume)/'.htmlspecialchars($input_data->hash_resume) : ''?><?php $leaveamessage == true ? print '/(leaveamessage)/true' : ''?><?php $forceoffline == true ? print '/(offline)/true' : ''?><?php echo $append_mode_theme?>" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

<div class="row">
    <?php if (isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true) : $hasExtraField = true;?>
    
    <?php if (isset($start_data_fields['name_hidden']) && $start_data_fields['name_hidden'] == true) : ?>
	<input type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
	<?php else : ?>	
		<?php if (in_array('username', $input_data->hattr)) : ?>
			<input type="hidden" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
		<?php else : ?>
	    <div class="col-xs-6 form-group">
	        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') : ?>*<?php endif;?></label>
	        <input type="text" class="form-control" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
	    </div>    
	    <?php endif; ?>    
    <?php endif; ?>
    
    <?php endif; ?>

    <?php if (isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) : $hasExtraField = true;?>
    
    <?php if (isset($start_data_fields['email_hidden']) && $start_data_fields['email_hidden'] == true) : ?>
	<input type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
	<?php else : ?>
		<?php if (in_array('email', $input_data->hattr)) : ?>
			<input type="hidden" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
		<?php else : ?>
	    <div class="col-xs-6 form-group">
	        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?><?php if (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required') : ?>*<?php endif;?></label>
	        <input class="form-control" type="text" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
	    </div>
	    <?php endif; ?>
    <?php endif; ?>
    
    <?php endif; ?>
</div>


<?php if (isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) : $hasExtraField = true;?>
<?php if (isset($start_data_fields['phone_hidden']) && $start_data_fields['phone_hidden'] == true) : ?>
<input type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php else : ?>
		<?php if (in_array('phone', $input_data->hattr)) : ?>
		<input type="hidden" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
		<?php else : ?>
		<div class="form-group">
		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') : ?>*<?php endif;?></label>
		  <input class="form-control" type="text" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
		</div>
		<?php endif; ?>
<?php endif; ?>
<?php endif; ?>

<?php $canReopen = erLhcoreClassModelChatConfig::fetch('reopen_chat_enabled')->current_value == 1 && ($reopenData = erLhcoreClassChat::canReopenDirectly(array('reopen_closed' => erLhcoreClassModelChatConfig::fetch('allow_reopen_closed')->current_value))) !== false; ?>

<?php if (isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true) : ?>
<?php if (isset($start_data_fields['message_hidden']) && $start_data_fields['message_hidden'] == true) : $hasExtraField = true; ?>
<textarea class="hide" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php else : ?>
<?php if (!isset($start_data_fields['hide_message_label']) || $start_data_fields['hide_message_label'] == false) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?><?php if (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required') : ?>*<?php endif;?></label><?php endif;?>
<textarea class="form-control form-group <?php if ($hasExtraField !== true && $canReopen !== true) : ?>btrad-reset<?php endif;?>" <?php if (isset($start_data_fields['user_msg_height']) && $start_data_fields['user_msg_height'] > 0) : ?>style="height: <?php echo $start_data_fields['user_msg_height']?>px"<?php endif;?> placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message...');?>" id="id_Question" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php endif; ?>
<?php else : $hasExtraField = true; endif; ?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php if ($department === false) : ?>
	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/user_timezone.tpl.php'));?>

<?php $tosVariable = 'tos_visible_in_page_widget'?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/accept_tos.tpl.php'));?>

<?php if ($hasExtraField == true || $canReopen == true) : ?>
<div class="btn-group" role="group" aria-label="...">  
  <?php if ($hasExtraField === true) : ?>
  <input type="submit" class="btn btn-default btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChatAction" />
  <?php endif;?>
  
  <?php if ( $canReopen == true ) : ?>
  <input type="button" class="btn btn-default btn-sm"  value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Resume chat');?>" onclick="document.location = '<?php echo erLhcoreClassDesign::baseurl('chat/reopen')?>/<?php echo $reopenData['id']?>/<?php echo $reopenData['hash']?>/(mode)/widget<?php if ( isset($append_mode) && $append_mode != '' ) : ?>/(embedmode)/embed<?php endif;?><?php echo $append_mode_theme?>'">
  <?php endif; ?>
</div>
<?php endif;?>

<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer" />
<input type="hidden" value="<?php echo htmlspecialchars($referer_site);?>" name="r" />
<input type="hidden" value="<?php echo htmlspecialchars($input_data->operator);?>" name="operator" />
<input type="hidden" value="1" name="StartChat"/>

</form>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/switch_to_offline.tpl.php'));?>

<?php if ($hasExtraField === false) : ?>
<script>
<?php if ($canReopen == false) : ?>
jQuery('#id_Question').addClass('mb0');
<?php endif;?>
var formSubmitted = false;
jQuery('#id_Question').bind('keydown', 'return', function (evt){
	if (formSubmitted == false) {
		formSubmitted = true;
		$( "#form-start-chat" ).submit();	
	};
	return false;
});
</script>
<?php endif;?>

<?php else : ?>
	<?php if (isset($start_data_fields['show_operator_profile']) && $start_data_fields['show_operator_profile'] == true) : ?>
	
		<div class="pl10 pos-rel max-width-180 pull-right">
		<?php $rightLanguage = true;?>
		<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
		</div>
	
	<?php endif;?>

	<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
	<?php endif; ?>

	<?php include(erLhcoreClassDesign::designtpl('lhchat/offline_form.tpl.php'));?>
<?php endif;?>

<?php endif;?>