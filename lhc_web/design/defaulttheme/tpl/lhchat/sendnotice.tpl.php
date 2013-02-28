<h2>Send message to user</h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Message was send to user'); ?>

<script>
setTimeout(function(){
    parent.$.colorbox.close();
    parent.lhinst.syncOnlineUsers();
},3000);
</script>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<p>If message was already send, this will mark message as not delivered and to user again will be shown chat message.</p>

<form action="" method="post">

<textarea name="Message" placeholder="Type your message to user"><?php echo htmlspecialchars($visitor->operator_message) ?></textarea>
<input type="submit" class="button small" name="SendMessage" value="Send message" />

</form>