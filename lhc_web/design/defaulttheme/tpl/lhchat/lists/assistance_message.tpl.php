<div class="message-row system-response">
	<div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div><i><span class="usr-tit sys-tit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant')?></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?></i>
</div>