<?php
// Has exists variable
?>
<?php if (!isset($metaMessageData['processed']) || $metaMessageData['processed'] == false) : ?>
<div id="meta-message-<?php echo $messageId?>" class="meta-message">
<?php foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
    <?php if ($type == 'quick_replies' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/quick_replies.tpl.php'));?>
    <?php endif; ?>
<?php endforeach; ?>
</div>
<?php endif; ?>