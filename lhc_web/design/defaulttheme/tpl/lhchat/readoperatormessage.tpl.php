<?php 
$profileShown = false;
if (($user = $visitor->operator_user) !== false) : ?>
<?php $profileShown = true; $hideThumbs = true;$extraMessage = $user->job_title != '' ? htmlspecialchars($user->job_title) : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Personal assistant');?>
<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php'));?>
<?php endif;?>

<div id="messages" class="read-operator-message">
     <div class="msgBlock" id="messagesBlock">
     	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/operator_message_row.tpl.php'));?>
     </div>
</div>

<form action="" id="ReadOperatorMessage" method="post" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

<?php if (isset($errors)) : ?>
<div class="pt10">
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
</div>
<?php endif; ?>

<?php 
$hasExtraField = false;
if ($visitor->requires_username == 1 || $visitor->requires_email == 1 || $visitor->requires_phone == 1) : $hasExtraField = true;?><div class="row"><?php endif;?>

<?php if ($visitor->requires_username == 1) : ?>
<div class="col-xs-6 form-group">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?>*</label>
	<input type="text" class="form-control" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
</div>
<?php endif; ?>

<?php if ($visitor->requires_phone == 1) : ?>
<div class="col-xs-6 form-group">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?>*</label>
	<input type="text" class="form-control" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" placeholder="Min <?php echo erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters');?>" />
</div>
<?php endif; ?>

<?php if ($visitor->requires_email == 1) : ?>
<div class="col-xs-6 form-group">
	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?>*</label>
	<input type="text" class="form-control" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
</div>
<?php endif; ?>

<?php if ($visitor->requires_username == 1 || $visitor->requires_email == 1 || $visitor->requires_phone == 1) : ?></div><?php endif;?>

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

</form>

<script>
var formSubmitted = false;
jQuery('#id_Question').bind('keydown', 'return', function (evt){
	if (formSubmitted == false) {
		formSubmitted = true;
		$( "#ReadOperatorMessage" ).submit();	
	};
	return false;
});
<?php if ($playsound == true) : ?>
$(function() {lhinst.playInvitationSound();});
<?php endif; ?>
</script>
