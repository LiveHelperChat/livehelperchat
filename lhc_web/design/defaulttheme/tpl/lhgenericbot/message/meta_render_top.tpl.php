<?php if (is_array($metaMessageData) && !isset($metaMessageData['processed']) || $metaMessageData['processed'] == false) : ?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'attachements') : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/attachements.tpl.php'));?>
        <?php endif; ?>
    <?php endforeach; endif;  ?>
<?php endif; ?>