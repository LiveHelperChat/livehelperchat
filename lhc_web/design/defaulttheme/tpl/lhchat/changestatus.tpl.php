<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Change chat status')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>


<form action="<?php echo erLhcoreClassDesign::baseurl('chat/changestatus')?>/<?php echo $chat->id?>" method="post" onsubmit="return lhinst.changeStatusAction($(this),'<?php echo $chat->id?>')">

<div class="form-group">
    <label><input type="radio" name="ChatStatus" value="<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending')?></label><br/>
    <label><input type="radio" name="ChatStatus" value="<?php echo erLhcoreClassModelChat::STATUS_ACTIVE_CHAT?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Active')?></label><br/>
    <label><input type="radio" name="ChatStatus" value="<?php echo erLhcoreClassModelChat::STATUS_CLOSED_CHAT?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Closed')?></label><br/>
    <label><input type="radio" name="ChatStatus" value="<?php echo erLhcoreClassModelChat::STATUS_CHATBOX_CHAT?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chatbox chat')?></label><br/>
    <label><input type="radio" name="ChatStatus" value="<?php echo erLhcoreClassModelChat::STATUS_OPERATORS_CHAT?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operators chat')?></label><br/>
    <label><input type="radio" name="ChatStatus" value="<?php echo erLhcoreClassModelChat::STATUS_BOT_CHAT?>" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Bot chat')?></label>
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Change chat status')?>" />
	<input type="button" class="btn btn-secondary" onclick="$('#myModal').modal('hide');" name="CancelAlbum" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Cancel')?>"/>
</div>

</form>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>