<?php if (isset($orderInformation['chat_duration']['enabled']) && $orderInformation['chat_duration']['enabled'] == true) : ?>
    <div class="col-6 pb-1">
        <span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Chat duration')?></span> - <span id="chat-duration-<?php echo $chat->id?>"><?php echo $chat->chat_duration_front?></span>
    </div>
<?php endif; ?>