<?php
    if ($msg['meta_msg'] != '') {
        $metaMessageData = json_decode($msg['meta_msg'], true); $messageId = $msg['id'];
    } else if (isset($metaMessageData)) {
        unset($metaMessageData);
    }
?>

<?php if ($msg['user_id'] > -1 || $msg['user_id'] == -2) : ?>
	<?php if ($msg['user_id'] == 0) { ?>
	        <div class="message-row response<?php if (isset($hideNextMessages) && $hideNextMessages == true) : ?> hide<?php endif;?>" id="msg-<?php echo $msg['id']?>" data-op-id="<?php echo $msg['user_id']?>"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) {	echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else { echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row_nick.tpl.php'));?>
                <?php if ($msg['msg'] != '') : ?>
                <div class="msg-body">
                    <?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?>

                    <?php if (isset($metaMessageData['content_static']['message_explain'])) : ?>
                        <span><a class="show-more" onclick="$(this).hide();$('#message-explain-<?php echo $messageId?>').removeClass('hide')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Read more');?></a><span id="message-explain-<?php echo $messageId?>" class="hide"><?php echo htmlspecialchars(erLhcoreClassBBCode::make_clickable($metaMessageData['content_static']['message_explain']))?></span></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (isset($metaMessageData)) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
                <?php endif; ?>
            </div>
	 <?php } else { ?>
	        <div class="message-row message-admin<?php (isset($lastOperatorChanged) && $lastOperatorChanged == true ? print ' operator-changes' : '') ?><?php if (isset($hideNextMessages) && $hideNextMessages == true) : ?> hide<?php endif;?>" id="msg-<?php echo $msg['id']?>" data-op-id="<?php echo $msg['user_id']?>"><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg['time'])) { echo  date(erLhcoreClassModule::$dateHourFormat,$msg['time']);} else {	echo date(erLhcoreClassModule::$dateDateHourFormat,$msg['time']);}; ?></div><span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><i class="material-icons chat-operators mi-fs15 mr-0">account_box</i><?php echo htmlspecialchars($msg['name_support'])?></span>

                <?php if ($msg['msg'] != '') : ?>
                    <div class="msg-body">
                        <?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']))?>

                        <?php if (isset($metaMessageData['content_static']['message_explain'])) : ?>
                            <span><a class="show-more" onclick="$(this).hide();$('#message-explain-<?php echo $messageId?>').removeClass('hide')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Read more');?></a><span id="message-explain-<?php echo $messageId?>" class="hide"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($metaMessageData['content_static']['message_explain']))?></span></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($metaMessageData)) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render.tpl.php'));?>
                <?php endif; ?>
            </div>
	<?php } ?>
<?php endif;?>


