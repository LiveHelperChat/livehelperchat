<?php
    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($theme->bot_configuration_array['trigger_id']);
    $chat = new erLhcoreClassModelChat();
    $messages = erLhcoreClassGenericBotWorkflow::processTriggerPreview($chat, $trigger, array('args' => array('do_not_save' => true)));
?>
<br/>


<div id="messages">
    <div id="messagesBlockWrap">
        <div class="msgBlock<?php if (isset($theme) && $theme !== false && $theme->hide_ts == 1) : ?> msg-hide-ts<?php endif?>" <?php if (erLhcoreClassModelChatConfig::fetch('mheight')->current_value > 0) : ?>style="height:<?php echo (int)erLhcoreClassModelChatConfig::fetch('mheight')->current_value?>px"<?php endif?> id="messagesBlock">
            <?php foreach ($messages as $msgObject) : ?>
                <?php $msg = (array)$msgObject?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
            <?php endforeach; ?>
        </div>
        <div id="chat-progress-status" class="hide"></div>
    </div>
</div>