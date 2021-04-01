<div class="meta-message-<?php echo $messageId?>">
    <script data-bot-action="execute-js" data-bot-emit='attr_set' <?php if (isset($metaMessage['ext_args'])) : ?>data-bot-args='<?php echo $metaMessage['ext_args']?>'<?php endif; ?>></script>
    <?php if ($metaMessage['operation'] == 'chat_abort') : // We just delete chat cookies so on refresh new chat would be presented ?>
        <script data-bot-action="execute-js" data-bot-emit='endCookies'></script>
    <?php endif; ?>
</div>
