<?php

$tpl = erLhcoreClassTemplate::getInstance('lhabstract/custom/testmasking.tpl.php');
$tpl->set('mask',(isset($_POST['mask']) ? $_POST['mask'] : ''));
$tpl->set('messages',(isset($_POST['messages']) ? $_POST['messages'] : ''));
$tpl->set('chat_id',(isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0));
$tpl->set('output','');
$tpl->set('diagnostics',null);

if (isset($_POST['messages'])) {

    $chatId = (int)(isset($_POST['chat_id']) ? $_POST['chat_id'] : 0);

    if ($chatId > 0) {
        // Chat simulation mode - department, assigned operator and its permissions are taken from the chat
        $chat = erLhcoreClassModelChat::fetch($chatId);
        $diagnostics = \LiveHelperChat\Helpers\ChatMessagesMasking::testMaskingForChat($chat, $_POST['messages']);
        $tpl->set('diagnostics',$diagnostics);
        $tpl->set('output',$diagnostics['output']);
    } else {
        // Direct pattern test mode
        $maskingObject = new \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting();
        $maskingObject->pattern = isset($_POST['mask']) ? $_POST['mask'] : '';
        $tpl->set('output',$maskingObject->getMasked($_POST['messages']));
    }
}

echo $tpl->fetch();
exit;

?>