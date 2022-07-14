<?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
    <?php if ($type == 'reactions') : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/reactions.tpl.php'));?>
    <?php endif; ?>
<?php endforeach; endif;  ?>