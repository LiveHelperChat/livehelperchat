<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendmail','Send mail to user');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Mail was send to user'); ?>
<script>
setTimeout(function(){
    parent.$.colorbox.close();
},3000);
</script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<textarea name="Message" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to user');?>"><?php //echo htmlspecialchars($input->operator_message) ?></textarea>
<input type="submit" class="button small" name="SendMail" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send mail');?>" />

</form>