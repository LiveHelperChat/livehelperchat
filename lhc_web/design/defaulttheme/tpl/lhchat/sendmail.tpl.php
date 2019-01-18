<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send mail to the user');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Mail was sent to the user'); ?>
<script>
setTimeout(function(){
	parent.lhinst.updateVoteStatus('<?php echo $chat->id?>');
    parent.$('#myModal').modal('hide');
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Recipient');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Recipient e-mail');?>" name="RecipientEmail" value="<?php echo htmlspecialchars($mail_template->recipient);?>" />
	</div>

	<?php if ($mail_template->subject_ac == 1) : ?>
	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject');?></label>
		<input class="form-control" type="text" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject');?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Subject');?>" name="Subject" value="<?php echo htmlspecialchars($mail_template->subject);?>" />
	</div>
	<?php endif;?>
	<?php if ($mail_template->from_name_ac == 1) : ?>
	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From name');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From name');?>" name="FromName" value="<?php echo htmlspecialchars($mail_template->from_name);?>" />
	</div>
	<?php endif;?>
	<?php if ($mail_template->reply_to_ac == 1) : ?>
	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Reply e-mail');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Reply e-mail');?>" name="ReplyEmail" value="<?php echo htmlspecialchars($mail_template->reply_to);?>" />
	</div>
	<?php endif;?>
	<?php if ($mail_template->from_email_ac == 1) : ?>
	<div class="form-group">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From e-mail');?></label>
		<input class="form-control" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','From name');?>" name="FromEmail" value="<?php echo htmlspecialchars($mail_template->from_email);?>" />
	</div>
	<?php endif;?>

    <textarea class="form-control form-group" name="Message" id="MailMessage" style="height:100px" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Type your message to the user');?>"><?php echo isset($_POST['Message']) ? htmlspecialchars($_POST['Message']) : ''?></textarea>
    
    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-default" name="SendMail" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send the mail');?>" />
        <input type="button" class="btn btn-default" onclick="return lhc.revealModal({'iframe':true,'height':350,'url':WWW_DIR_JAVASCRIPT +'file/attatchfilemail'})" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Embed link to file');?>" />
    </div>

</form>