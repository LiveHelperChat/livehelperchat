<?php $msgBody = (isset($metaMessage['intro_op']) && !empty($metaMessage['intro_op'])) ? $metaMessage['intro_op'] : $metaMessage['intro_us']; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>

<?php $fullMessage = (isset($metaMessage['full_op']) && $metaMessage['full_op'] != '') ? $metaMessage['full_op'] : $metaMessage['full_us']; ?>

<?php if (!empty($fullMessage)) : ?>
    <?php $msgBody = $fullMessage; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
    <button class="btn btn-xs fs13 btn-outline-primary btn-sm mb-1" id="hide-show-action-<?php echo $messageId?>"onclick='lhinst.hideShowAction(<?php echo json_encode(['chat_id' => (is_object($msg) ? $msg->chat_id : $msg['chat_id']), 'show_text' => (isset($metaMessage['readmore_op']) && !empty($metaMessage['readmore_op']) ? $metaMessage['readmore_op'] : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser', 'Read more')), 'hide_text' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser', 'Hide'),'id' => $messageId, 'hide_show' => !isset($metaMessage['hide_show']) || $metaMessage['hide_show'] == true])?>)'><?php if (isset($metaMessage['readmore_op']) && !empty($metaMessage['readmore_op'])) : ?><?php echo htmlspecialchars($metaMessage['readmore_op']); else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser', 'Read more')?><?php endif;?></button>
    <span id="message-more-<?php echo $messageId?>" class="hide"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?></span>
<?php endif; ?>
