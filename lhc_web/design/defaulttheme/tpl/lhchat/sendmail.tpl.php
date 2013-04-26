<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send mail to user');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Mail was send to user'); ?>
<script>
setTimeout(function(){
    parent.$.colorbox.close();
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<div class="row">
	<?php if ($mail_template->subject_ac == 1) : ?>
	<div class="column small-12">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject');?></label>
		<input type="text" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject');?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject');?>" name="Subject" value="<?php echo htmlspecialchars($mail_template->subject);?>" />
	</div>
	<?php endif;?>
	<?php if ($mail_template->from_name_ac == 1) : ?>
	<div class="column small-12">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From name');?></label>
		<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From name');?>" name="FromName" value="<?php echo htmlspecialchars($mail_template->from_name);?>" />
	</div>
	<?php endif;?>
	<?php if ($mail_template->reply_to_ac == 1) : ?>
	<div class="column small-6 end">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Reply e-mail');?></label>
		<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Reply e-mail');?>" name="ReplyEmail" value="<?php echo htmlspecialchars($mail_template->reply_to);?>" />
	</div>
	<?php endif;?>
	<?php if ($mail_template->from_email_ac == 1) : ?>
	<div class="column small-6 end">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From e-mail');?></label>
		<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From name');?>" name="FromEmail" value="<?php echo htmlspecialchars($mail_template->from_email);?>" />
	</div>
	<?php endif;?>
</div>

<textarea name="Message" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Type your message to user');?>"><?php echo isset($_POST['Message']) ? htmlspecialchars($_POST['Message']) : ''?></textarea>
<input type="submit" class="button small" name="SendMail" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send mail');?>" />

</form>