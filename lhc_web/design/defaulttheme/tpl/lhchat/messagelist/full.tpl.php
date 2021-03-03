<?php foreach ($messages as $msg ) : ?><?php if (!isset($remove_meta) || $remove_meta == false ) :?><div class="lhc-user-name">[<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$msg->time);?>] [<?php echo $msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support) ?>]</div><?php endif;?>
<div class="msg-row">
    <?php if ($msg->user_id == 0) : ?>
        <?php $msgBody = $msg->msg; $paramsMessageRender = array('render_html' => true);?>
    <?php else : ?>
        <?php $msgBody = $msg->msg; $paramsMessageRender = array('render_html' => true,'sender' => $msg->user_id);?>
    <?php endif; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
</div>
<?php
    if (isset($msg->meta_msg) && $msg->meta_msg != '') {
        $metaMessageData = json_decode($msg->meta_msg, true); $messageId = $msg->id;
    } else if (isset($metaMessageData)) {
        unset($metaMessageData);
    }
if (isset($metaMessageData)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
<?php endif; ?>
<?php endforeach; ?>