<?php

header('content-type: application/json; charset=utf-8');

$db = ezcDbInstance::get();
$db->beginTransaction();
try {

    $msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);

    $chat = erLhcoreClassModelChat::fetch($msg->chat_id);

    if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat)) {

        if ($Params['user_parameters']['payload'] == 'send_manual_message') {
            if ($chat->online_user instanceof erLhcoreClassModelChatOnlineUser) {
                echo json_encode(['modal' => 'chat/sendnotice/'.$chat->online_user->id]);
            } else {
                echo json_encode(['error' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/abstractclick', 'Invitation can not be send because online visitor profile can not be found.')]);
            }
        } else {
            $workflow = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.abstract_click', array('payload' => $Params['user_parameters']['payload'], 'msg' => & $msg, 'chat' => & $chat));
            if ($workflow === false) {
                echo json_encode(['error' => 'There is no listener setup for ' . $Params['user_parameters']['payload'] . ' event']);
            } else {
                echo json_encode($workflow['response']);
            }
        }
    }

    $db->commit();

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

exit;

?>