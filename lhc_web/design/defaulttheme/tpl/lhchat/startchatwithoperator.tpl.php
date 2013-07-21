<div class="p10 wb border-grey">

<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator');?> - <?php echo htmlspecialchars($user->name.' '.$user->surname)?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<textarea name="Message" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the operator');?>"><?php echo htmlspecialchars($msg->msg) ?></textarea>
<input type="submit" class="button small mb0 radius" name="SendMessage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Start chat with operator');?>" />

</form>

</div>