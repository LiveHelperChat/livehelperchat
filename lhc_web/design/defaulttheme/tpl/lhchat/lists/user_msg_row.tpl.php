<?php

    $visitorRender = true;

    $metaMessageDataByBBCode = erLhcoreClassBBCode::extractMetaByMessage($msg['msg']);

    $messageId = isset($triggerMessageId) ? $triggerMessageId : $msg['id'];

    if (isset($msg['meta_msg']) && $msg['meta_msg'] != '') {
        $metaMessageData = json_decode($msg['meta_msg'], true);
    } else if (isset($metaMessageData)) {
        unset($metaMessageData);
    }

    $skip = false;

    if (isset($metaMessageData['content']['whisper']) || isset($metaMessageData['content']['seen_content'])) {
        $skip = true;
    }

    if ($skip == false) {
    if (is_array($metaMessageDataByBBCode) && !empty($metaMessageDataByBBCode)) {
        if (!isset($metaMessageData)) {
            $metaMessageData = array();
        }
        $metaMessageData['content'] = $metaMessageDataByBBCode;
    }

    $typingMessage = false;
    if (isset($metaMessageData['content']['typing'])) {
        $typingMessage = true;
    }

    $hideOperator = false;

    // Hide operator nick, etc for javsacript messages
    if (
        (isset($metaMessageData['content']['execute_js']) && count($metaMessageData['content']) == 1) ||
        (isset($metaMessageData['content']['iframe']['iframe_options']['hide_op']) && $metaMessageData['content']['iframe']['iframe_options']['hide_op'] == 1)
    ) {
        $hideOperator = true;
    }

    $metaMessageId = '';

    if (isset($metaMessageData['content']['attr_options']['hide_on_next']) && $metaMessageData['content']['attr_options']['hide_on_next'] == true) {
        $metaMessageId = ' meta-auto-hide-normal';
        if (isset($messagesStats) && $messagesStats['total_messages'] != $messagesStats['counter_messages']){
            $msg['user_id'] = -1;
        }
    }

?>

<?php if (isset($msg['user_id']) && ($msg['user_id'] > -1 || $msg['user_id'] == -2)) : ?>
	<?php if ($msg['user_id'] == 0) { ?>
	        <div class="message-row response<?php echo $metaMessageId?><?php if (isset($hideNextMessages) && $hideNextMessages == true) : ?> hide<?php endif;?>" id="msg-<?php echo $msg['id']?>" data-op-id="<?php echo $msg['user_id']?>"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else { echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row_nick.tpl.php'));?>
                <?php if ($msg['msg'] != '') : ?>

                    <?php $msgBody = $msg['msg']; $paramsMessageRender = array('msg_id' => $msg['id'], 'render_html' => true);?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>

                    <?php if (isset($metaMessageData['content_static']['message_explain'])) : ?>
                        <div class="msg-body">
                            <span><a class="show-more" onclick="$(this).hide();$('#message-explain-<?php echo $messageId?>').removeClass('hide')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Read more');?></a><span id="message-explain-<?php echo $messageId?>" class="hide"><?php echo htmlspecialchars(erLhcoreClassBBCode::make_clickable($metaMessageData['content_static']['message_explain']))?></span></span>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

                <?php if (isset($metaMessageData)) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
                <?php endif; ?>
            </div>
	 <?php } else { ?>

            <?php if ($hideOperator == false) : ?>
                <div class="message-row message-admin<?php echo $metaMessageId?><?php $typingMessage == true ? print ' ignore-auto-scroll' : ''?><?php (isset($lastOperatorChanged) && $lastOperatorChanged == true ? print ' operator-changes' : '') ?><?php if (isset($hideNextMessages) && $hideNextMessages == true) : ?> hide<?php endif;?>" id="msg-<?php echo $msg['id']?>" data-op-id="<?php echo $msg['user_id']?>" title="<?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else {	echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?>">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/op_msg_row_nick.tpl.php'));?>
            <?php endif; ?>

                <?php if ($msg['msg'] != '') : ?>

                        <?php $msgBody = $msg['msg']; $paramsMessageRender = array('msg_id' => $msg['id'], 'sender' => $msg['user_id'], 'render_js' => ((isset($async_call) && $async_call == true) || (isset($chat_started_now) && isset($chat_started_now) == true && (!isset($msg['id']) || !isset($old_msg_id) || $msg['id'] > $old_msg_id)))); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>

                        <?php if (isset($metaMessageData['content_static']['message_explain'])) : ?>
                            <div class="msg-body"><span><a class="show-more" onclick="$(this).hide();$('#message-explain-<?php echo $messageId?>').removeClass('hide')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Read more');?></a><span id="message-explain-<?php echo $messageId?>" class="hide"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($metaMessageData['content_static']['message_explain']))?></span></span></div>
                        <?php endif; ?>

                <?php endif; ?>

                <?php if (isset($metaMessageData)) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
                <?php endif; ?>

            <?php if ($hideOperator == false) : ?>
                </div>
            <?php endif; ?>

	<?php } ?>
<?php endif;}?>


