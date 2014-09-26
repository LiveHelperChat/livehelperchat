<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Change chat status')?></h2>
<hr>
<form action="<?php echo erLhcoreClassDesign::baseurl('chat/changestatus')?>/<?php echo $chat->id?>" method="post" onsubmit="return lhinst.changeStatusAction($(this),'<?php echo $chat->id?>')">
<label><input type="radio" name="ChatStatus" value="0" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Pending')?></label>
<label><input type="radio" name="ChatStatus" value="1" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Active')?></label>
<label><input type="radio" name="ChatStatus" value="2" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Closed')?></label>
<label><input type="radio" name="ChatStatus" value="3" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chatbox chat')?></label>
<label><input type="radio" name="ChatStatus" value="4" <?php if ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operators chat')?></label>

<ul class="button-group radius">
	<li><input type="submit" class="button small" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Change chat status')?>" /></li>
	<li><input type="button" class="button small alert" onclick="lhinst.closeReveal('#myModal');" name="CancelAlbum" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Cancel')?>"/></li>
</ul>

</form>
<a class="close-reveal-modal">&#215;</a>