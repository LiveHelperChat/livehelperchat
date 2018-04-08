<?php

/**
 * php cron.php -s site_admin -c cron/encrypt
 *
 * Run every 1 hour or so.
 *
 * */

echo "Starting chat encrypt workflow\n";

$encOptions = erLhcoreClassModelChatConfig::fetch('encrypt_msg_after');
$value = $encOptions->current_value;

$encMsgOperator = erLhcoreClassModelChatConfig::fetch('encrypt_msg_op');
$valueOp = $encMsgOperator->current_value;

if ($value > 0) {

    $chats = erLhcoreClassModelChat::getList(array(
        'sort' => 'id DESC',
        'limit' => 500,
        'filter' => array('anonymized' => 0),
        'filterlt' => array('time' => (time()-($value*24*3600))
    )));

    foreach ($chats as $chat) {
        echo "Anonymizing - ",$chat->id,"\n";
        $messages = erLhcoreClassModelmsg::getList(array('filter' => array('chat_id' => $chat->id)));

        foreach ($messages as $message) {
            if ($message->user_id == 0 || ($message->user_id > 0 && $valueOp == 1)) {
                $message->msg = '[anonymized]';
                $message->saveThis();
            }
        }

        $chat->anonymized = 1;
        $chat->saveThis();
    }

    echo "Encrypting";
} else {
    echo "Automatic chats encrypting is not setup\n";
}


?>