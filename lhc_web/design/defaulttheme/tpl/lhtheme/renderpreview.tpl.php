<?php
    $chat = new erLhcoreClassModelChat();
    $messages = erLhcoreClassGenericBotWorkflow::processTriggerPreview($chat, $trigger, array('args' => array('presentation' => true, 'do_not_save' => true)));
?>
<br/>
<?php foreach ($messages as $msgObject) : ?>
    <?php $msg = (array)$msgObject?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>
<?php endforeach; ?>