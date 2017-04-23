<?php

header('content-type: application/json; charset=utf-8');

$chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if (erLhcoreClassChat::hasAccessToRead($chat)) {

    $grouped = erLhcoreClassModelCannedMsg::groupItems(erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id, erLhcoreClassUser::instance()->getUserID(), array(
        'q' => (isset($_GET['q']) ? $_GET['q'] : '')
    )), $chat, erLhcoreClassUser::instance()->getUserData(true));

    $tpl = erLhcoreClassTemplate::getInstance('lhchat/part/canned_messages_options.tpl.php');
    $tpl->set('canned_options',$grouped);

    echo erLhcoreClassChat::safe_json_encode(array(
        'error' => false,
        'result' => $tpl->fetch()
    ));

} else {
    echo erLhcoreClassChat::safe_json_encode(array(
        'error' => true,
        'result' => 'no permission'
    ));
}

exit();

?>