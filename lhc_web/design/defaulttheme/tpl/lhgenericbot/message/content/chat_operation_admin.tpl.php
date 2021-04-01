<?php $msgBody = (isset($metaMessage['intro_op']) && !empty($metaMessage['intro_op'])) ? $metaMessage['intro_op'] : $metaMessage['intro_us']; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>

<?php if ($metaMessage['operation'] == 'chat_abort') : ?>
    <?php $msgBody = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list', 'Visitor was blocked by') . ' ' . $metaMessage['intro_op']; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
<?php endif; ?>
