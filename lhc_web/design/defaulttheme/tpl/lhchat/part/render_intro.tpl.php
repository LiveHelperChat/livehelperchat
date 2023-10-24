<?php
    if (isset($triggerMessage) && $triggerMessage instanceof erLhcoreClassModelGenericBotTrigger) {
        $trigger = $triggerMessage;
    } else {
        $trigger = erLhcoreClassModelGenericBotTrigger::fetch((isset($triggerMessageId) && $triggerMessageId > 0) ? $triggerMessageId : $theme->bot_configuration_array['trigger_id']);
    }

    if (!isset($chat)) {
        $chat = new erLhcoreClassModelChat();

        if (isset($additionalDataArray)) {
            $chat->additional_data_array = $additionalDataArray;
        }

        if (isset($variablesDataArray)) {
            $chat->chat_variables_array = $variablesDataArray;
        }
    }

    $messages = erLhcoreClassGenericBotWorkflow::processTriggerPreview($chat, $trigger, array('args' => array('do_not_save' => true)));

?>

<?php if (!isset($no_br)) : ?>
<br/>
<?php endif; ?>

<?php if (!isset($no_wrap_intro)):?>
<div id="messages">
    <div id="messagesBlockWrap">
        <div class="msgBlock<?php if (isset($theme) && $theme !== false && $theme->hide_ts == 0) : ?> msg-hide-ts<?php endif?>" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php endif?> id="messagesBlock">
            <?php endif; ?>

            <?php foreach ($messages as $msgObject) : ?>
                <?php $msg = (array)$msgObject; if (is_numeric($msg['id'])) {$triggerMessageId = $msg['id'];};?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
            <?php endforeach; ?>

            <?php if (!isset($no_wrap_intro)):?>
        </div>
        <div id="chat-progress-status" class="hide"></div>
    </div>
</div>
<?php endif; ?>


