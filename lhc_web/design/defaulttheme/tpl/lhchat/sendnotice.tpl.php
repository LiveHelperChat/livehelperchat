<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send a message to the user');?></h2>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_saved) && $message_saved == 'true') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Message was sent to the user'); ?>

<script>
setTimeout(function(){
    $('#myModal').modal('hide');
},2000);
</script>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','If the message was already sent, this will mark it as not delivered and the user will be shown the chat message again.');?></p>

<form action="<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $visitor->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">


	<textarea class="form-control form-group" name="Message" id="sendMessageContent" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Type your message to the user');?>"><?php echo htmlspecialchars($visitor->operator_message) ?></textarea>
	
	<div class="row form-group">
		<div class="col-xs-6"><label><input type="checkbox" name="RequiresEmail" value="on" <?php $visitor->requires_email == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires e-mail')?></label></div>
		<div class="col-xs-6"><label><input type="checkbox" name="RequiresUsername" value="on" <?php $visitor->requires_username == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires name')?></label></div>
		<div class="col-xs-6"><label><input type="checkbox" name="RequiresPhone" value="on" <?php $visitor->requires_phone == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires phone')?></label></div>
	</div>
	
	<select class="form-control" id="id_CannedMessage-<?php echo $chat->id?>" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').text() : '');">
		        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message')?></option>
		        <?php foreach (erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,erLhcoreClassUser::instance()->getUserID()) as $item) : ?>
		            <option value="<?php echo $item->id?>"><?php echo htmlspecialchars(str_replace('{nick}', (isset($chat) ? $chat->nick : ''), $item->msg))?></option>
		       <?php endforeach;?>
	</select>
		      
	<input type="hidden" name="SendMessage" value="1" />
	<hr>      
	<input type="submit" class="btn btn-default" name="SendMessage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message');?>" />

</form>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>