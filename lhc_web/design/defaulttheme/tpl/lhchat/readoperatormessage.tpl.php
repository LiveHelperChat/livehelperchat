<?php include(erLhcoreClassDesign::designtpl('lhchat/readoperatormessage/read_message_profile.tpl.php'));?>

<form action="" id="ReadOperatorMessage" method="post" onsubmit="return <?php if (isset($start_data_fields['message_auto_start']) && $start_data_fields['message_auto_start'] == true) : ?>lhinst.prestartChat('<?php echo time()?>',$(this))<?php else : ?>lhinst.addCaptcha('<?php echo time()?>',$(this))<?php endif?>">

<div id="messages" class="read-operator-message<?php if($fullheight) : ?> fullheight<?php endif ?>">
    <div id="messagesBlockWrap">
		<div class="msgBlock<?php if (isset($theme) && $theme !== false && $theme->hide_ts == 1) : ?> msg-hide-ts<?php endif?>" id="messagesBlock">
			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/operator_message_row.tpl.php'));?>
			<?php if (isset($start_data_fields['show_messages_box']) && $start_data_fields['show_messages_box'] == true) : ?>
			<?php $formIdentifier = '#ReadOperatorMessage';?>
			<?php include(erLhcoreClassDesign::designtpl('lhchat/startchatformsettings/presend_script.tpl.php'));?>
			<?php endif;?>
		 </div>
	</div>
</div>

<?php if (isset($errors)) : ?>
<div class="pt10">
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
</div>
<?php endif; ?>

<?php $formResubmitId = 'ReadOperatorMessage'; ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/auto_resubmit.tpl.php'));?>

<?php

$hasExtraField = false;
$extraFields = array();

if ($visitor->requires_username == 1 && !in_array('username', $input_data->hattr)) {
	$hasExtraField = true;
}

if ($visitor->requires_email == 1 && !in_array('email', $input_data->hattr)) {
	$hasExtraField = true;
}

if ($visitor->requires_phone == 1 && !in_array('phone', $input_data->hattr)) {
	$hasExtraField = true;
}

if ($hasExtraField == true) : ?><div class="row"><?php endif;?>

<?php if ($visitor->requires_username == 1) : ?>
	<?php if (in_array('username', $input_data->hattr)) : ?>
	<input type="hidden" class="form-control" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
	<?php else : $allHiddenFields = false; ?>
	<div class="col-xs-6 form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?>*</label>
		<input type="text" class="form-control" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($visitor->requires_phone == 1) : ?>
	<?php if (in_array('phone', $input_data->hattr)) : ?>
	<input type="hidden" class="form-control" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
	<?php else : $allHiddenFields = false;?>
	<div class="col-xs-6 form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?>*</label>
		<input type="text" class="form-control" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" placeholder="Min <?php echo erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters');?>" />
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($visitor->requires_email == 1) : ?>
	<?php if (in_array('email', $input_data->hattr)) : ?>
	<input type="hidden" class="form-control" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
	<?php else : $allHiddenFields = false;?>
	<div class="col-xs-6 form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?>*</label>
		<input type="text" class="form-control" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($hasExtraField == true) : ?></div><?php endif;?>

<?php $adminCustomFieldsMode = 'on';?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/admin_form_variables.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php if ($department === false) : ?>
	<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>
<?php endif;?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/user_timezone.tpl.php'));?>

<input type="hidden" name="askQuestion" value="1" />
<input type="hidden" value="<?php echo htmlspecialchars($input_data->operator);?>" name="operator" />

<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>
<input type="hidden" value="<?php echo htmlspecialchars($referer_site);?>" name="r"/>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/readoperatormessage_form_bottom.tpl.php'));?>

<?php if ($hasExtraField === true) : ?>
    <input type="hidden" value="1" id="hasFormExtraField"/>
<?php endif;?>

</form>

<?php include(erLhcoreClassDesign::designtpl('lhchat/part/readoperatormessage_js.tpl.php'));?>