<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send a message to the user') ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

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
        <div class="col-6"><label><input type="checkbox" name="AssignToMe" value="on" <?php (isset($visitor->online_attr_system_array['lhc_assign_to_me']) && $visitor->online_attr_system_array['lhc_assign_to_me'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Assign the chat to me if the visitor replies')?></label></div>
        <div class="col-6"><label><input type="checkbox" name="IgnoreAutoresponder" value="on" <?php (isset($visitor->online_attr_system_array['lhc_ignore_autoresponder']) && $visitor->online_attr_system_array['lhc_ignore_autoresponder'] == 1) ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Do not send automated messages if the customer replies')?></label></div>
		<div class="col-6"><label><input type="checkbox" name="RequiresEmail" value="on" <?php $visitor->requires_email == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires e-mail')?></label></div>
		<div class="col-6"><label><input type="checkbox" name="RequiresUsername" value="on" <?php $visitor->requires_username == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires name')?></label></div>
		<div class="col-6"><label><input type="checkbox" name="RequiresPhone" value="on" <?php $visitor->requires_phone == 1 ? print 'checked="checked"' : ''?> />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Requires phone')?></label></div>
	</div>

    <div class="row">
        <div class="col-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Campaign')?></label>
            <select name="CampaignId" id="id_CampaignMessage-<?php echo $chat->id?>" class="form-control" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').attr('data-msg') : '');">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select campaign')?></option>
                <?php foreach (erLhAbstractModelProactiveChatCampaign::getList() as $item) : ?>
                    <option value="<?php echo $item->id?>" data-msg="<?php echo htmlspecialchars(str_replace('{nick}', (isset($chat) ? $chat->nick : ''), $item->text))?>"><?php echo htmlspecialchars($item->name)?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Canned message')?></label>
            <select class="form-control" id="id_CannedMessage-<?php echo $chat->id?>" onchange="$('#sendMessageContent').val(($(this).val() > 0) ? $(this).find(':selected').text() : '');">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Select a canned message')?></option>
                <?php foreach (erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id,erLhcoreClassUser::instance()->getUserID()) as $item) : ?>
                    <option value="<?php echo $item->id?>"><?php echo htmlspecialchars(str_replace('{nick}', (isset($chat) ? $chat->nick : ''), $item->msg))?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

	<input type="hidden" name="SendMessage" value="1" />
	<hr>      
	<input type="submit" class="btn btn-secondary" name="SendMessage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Send the message');?>" />

</form>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>