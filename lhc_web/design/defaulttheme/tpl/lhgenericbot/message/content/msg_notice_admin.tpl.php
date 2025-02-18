<?php $msgBody = $metaMessage['content']; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
<div class="whisper-msg">
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    <?php if (isset($metaMessage['content_history'])) : ?>
        <?php foreach ($metaMessage['content_history'] as $msgHistory) : ?>
            <br>
            <?php $msgBody = $msgHistory; $paramsMessageRender = array('sender' => $msg['user_id']);?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
