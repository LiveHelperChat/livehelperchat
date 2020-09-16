<?php $msgBody = $metaMessage['intro_us']; $paramsMessageRender = array('render_html' => true); ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php')); ?>

<?php $fullMessage = $metaMessage['full_us']; ?>

<?php if (!empty($fullMessage)) : ?>
    <?php
        $msgBody = $fullMessage;
        $paramsMessageRender = array('sender' => $msg['user_id']);
        $jsonPayload = ['show_text' => (isset($metaMessage['readmore_us']) && !empty($metaMessage['readmore_us']) ? $metaMessage['readmore_us'] : htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser', 'Read more'))), 'hide_text' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser', 'Hide'), 'id' => $messageId, 'hide_show' => !isset($metaMessage['hide_show']) || $metaMessage['hide_show'] == true];
    ?>
    <button class="btn btn-link fs13 pt-0 pb-1 d-block" id="hide-show-action-<?php echo $messageId?>" data-load='<?php echo json_encode($jsonPayload)?>' onclick='lhinst.hideShowAction(<?php echo json_encode($jsonPayload)?>)'>
        <?php echo htmlspecialchars($jsonPayload['show_text']) ?>
    </button>
    <div id="message-more-<?php echo $messageId?>" class="hide"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?></div>
<?php endif; ?>
