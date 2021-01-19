<?php if (is_array($metaMessageData)) : ?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'text_conditional') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/text_conditional_admin.tpl.php'));?>
       <?php elseif ($type == 'html') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/html_admin.tpl.php'));?>
        <?php elseif ($type == 'button_message') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/button_message_admin.tpl.php'));?>
        <?php endif; ?>
    <?php endforeach; endif;  ?>
<?php endif; ?>