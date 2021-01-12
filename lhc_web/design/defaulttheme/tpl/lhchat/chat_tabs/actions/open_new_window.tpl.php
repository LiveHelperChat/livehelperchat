<?php if (isset($orderChatButtons['open_new_window']) && $orderChatButtons['open_new_window']['enabled'] == 1) : ?>
<div class="col-6 pb-1">
<a class="text-muted" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatCloseTabNewWindow('<?php echo $chat->id;?>',$('#tabs'),$(this).attr('data-title'))">
<span class="material-icons">open_in_new</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','New window');?>
</a>
</div>
<?php endif; ?>