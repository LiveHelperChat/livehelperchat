<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send mail to the user');?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Mail was sent to the user'); ?>
<script>
setTimeout(function(){
	lhinst.updateVoteStatus('<?php echo $chat->id?>');
    $('#myModal').modal('hide');
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/sendmail')?>/<?php echo $chat->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

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

    <input type="hidden" value="on" name="SendMail" />

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="SendMail" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send the mail');?>" />
        <?php /*<input type="button" class="btn btn-secondary" onclick="return lhc.revealModal({'iframe':true,'height':350,'url':WWW_DIR_JAVASCRIPT +'file/attatchfilemail'})" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Embed link to file');?>" />*/ ?>
    </div>

</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>