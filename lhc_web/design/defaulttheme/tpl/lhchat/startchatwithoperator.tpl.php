<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator');?> - <?php echo htmlspecialchars($user->name.' '.$user->surname)?></h3>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<div class="form-group">
<textarea class="form-control" name="Message" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the operator');?>"><?php echo htmlspecialchars($msg->msg) ?></textarea>
</div>

<input type="submit" class="btn btn-default" name="SendMessage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator');?>" />

</form>

