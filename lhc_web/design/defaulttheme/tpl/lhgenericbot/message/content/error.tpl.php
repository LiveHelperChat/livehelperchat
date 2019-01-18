<?php if (isset($metaMessageData['content_error']['message']) && !empty($metaMessageData['content_error']['message'])) : ?>
    <?php if (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages']) : ?>
        <div class="alert alert-danger p-1 meta-auto-hide meta-message-<?php echo $messageId?>">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
            <?php echo htmlspecialchars($metaMessageData['content_error']['message'])?>
        </div>
    <?php endif; ?>
<?php endif; ?>
