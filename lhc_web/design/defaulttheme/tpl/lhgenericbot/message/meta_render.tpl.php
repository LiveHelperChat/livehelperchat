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
        <?php elseif ($type == 'progress') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/progress.tpl.php'));?>
        <?php elseif ($type == 'html') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/html.tpl.php'));?>
        <?php elseif ($type == 'html_snippet') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/html_snippet.tpl.php'));?>
        <?php elseif ($type == 'execute_js') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/execute_js.tpl.php'));?>
        <?php elseif ($type == 'video') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/video.tpl.php'));?>
        <?php elseif ($type == 'attr_options' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/attr_options.tpl.php'));?>
        <?php endif; ?>
    <?php endforeach; endif;  ?>


    <?php if (isset($metaMessageData['content_error']) && is_array($metaMessageData['content_error'])) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/error.tpl.php'));?>
    <?php endif; ?>

<?php endif; ?>