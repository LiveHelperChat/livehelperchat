<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send a message to the user');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Message was sent to the user'); ?>

<script>
setTimeout(function(){
    parent.$.colorbox.close();
    parent.lhinst.syncOnlineUsers();
},3000);
</script>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','If the message was already sent, this will mark it as not delivered and the user will be shown the chat message again.');?></p>

<form action="" method="post">

<textarea name="Message" id="sendMessageContent" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the user');?>"><?php echo htmlspecialchars($visitor->operator_message) ?></textarea>

<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'canned_msg',
						'display_name'	 => 'msg',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message'),
	                    'selected_id'    => 0,
						'on_change'		 => '$(\'#sendMessageContent\').val(($(this).val() > 0) ? $(this).find(\':selected\').text() : \'\');',
	                    'list_function'  => 'erLhcoreClassModelCannedMsg::getList'
)); ?>
	    
<input type="submit" class="button small" name="SendMessage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message');?>" />


</form>