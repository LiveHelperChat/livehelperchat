<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill out this form to start a chat');?></h1>
<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>">

<?php if (isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') : ?>*<?php endif;?></label>
<input type="text" class="inputfield" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
<?php endif; ?>

<?php if (isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?><?php if (isset($start_data_fields['email_require_option']) && $start_data_fields['email_require_option'] == 'required') : ?>*<?php endif;?></label>
<input type="text" class="inputfield" name="Email" value="<?php echo htmlspecialchars($input_data->email);?>" />
<?php endif; ?>

<?php if (isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Phone');?><?php if (isset($start_data_fields['phone_require_option']) && $start_data_fields['phone_require_option'] == 'required') : ?>*<?php endif;?></label>
<input type="text" class="inputfield" name="Phone" value="<?php echo htmlspecialchars($input_data->phone);?>" />
<?php endif; ?>

<?php if (isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Your question');?><?php if (isset($start_data_fields['message_require_option']) && $start_data_fields['message_require_option'] == 'required') : ?>*<?php endif;?></label>
<textarea placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Enter your message');?>" name="Question"><?php echo htmlspecialchars($input_data->question);?></textarea>
<?php endif; ?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/user_variables.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('lhchat/part/department.tpl.php'));?>

<input type="submit" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChat" />
<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>

</form>