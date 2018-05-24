<?php if (is_array($metaMessageData) && !isset($metaMessageData['processed']) || $metaMessageData['processed'] == false) : ?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'quick_replies' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/quick_replies.tpl.php'));?>
        <?php elseif ($type == 'dropdown' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/dropdown.tpl.php'));?>
        <?php elseif ($type == 'buttons' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/buttons.tpl.php'));?>
        <?php elseif ($type == 'custom' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/custom.tpl.php'));?>
        <?php elseif ($type == 'collected_summary') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/collected_summary.tpl.php'));?>
        <?php elseif ($type == 'buttons_generic') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/buttons_generic.tpl.php'));?>
        <?php elseif ($type == 'generic') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/generic.tpl.php'));?>
        <?php elseif ($type == 'list') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/list.tpl.php'));?>
        <?php elseif ($type == 'typing') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/typing.tpl.php'));?>
        <?php endif; ?>
    <?php endforeach; endif;  ?>
<?php endif; ?>