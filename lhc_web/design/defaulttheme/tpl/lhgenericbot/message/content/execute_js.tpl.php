<?php if ((isset($async_call) && $async_call == true) || (isset($chat_started_now) && isset($chat_started_now) == true)) : ?>
<div class="meta-message-<?php echo $messageId?>">
    <script data-bot-action="execute-js"><?php echo $metaMessage['payload']?></script>
</div>
<?php endif; ?>