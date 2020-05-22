<?php

$chat = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);

if ($chat instanceof erLhcoreClassModelGroupChat)
{

    $result = erLhcoreClassGroupChat::getChatHistory($chat, $Params['user_parameters']['msg_id']);

    $tpl = erLhcoreClassTemplate::getInstance('lhchat/loadpreviousmessages.tpl.php');
    $tpl->set('messages', $result['messages']);
    $tpl->set('chat', $chat);
    $tpl->set('initial', $Params['user_parameters_unordered']['initial'] == 1);

    echo json_encode(array(
        'result' => trim($tpl->fetch()),
        'has_messages' => $result['has_messages'],
        'message_id' => (int)$result['message_id'],
        'lmsop' => (int)$result['lmsop'],
        'msop' => (int)$result['msop'],
    ));

} else {
    echo json_encode(array('error' => true));
}

exit;

?>