<?php $msgBody = $metaMessage['content']; $paramsMessageRender = array('sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));?>
<div class="whisper-msg">
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
</div>
