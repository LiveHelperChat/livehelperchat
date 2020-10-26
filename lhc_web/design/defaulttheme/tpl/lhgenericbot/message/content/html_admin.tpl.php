<?php if (is_array($metaMessage)) : ?>
    <?php $msgBody = '[html]'.$metaMessage['content'].'[/html]'; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
<?php endif; ?>
