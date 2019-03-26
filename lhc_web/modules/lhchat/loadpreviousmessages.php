<?php

$db = ezcDbInstance::get();
$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) )
{
        $userData = $currentUser->getUserData();

        try {

            $result = erLhcoreClassChatWorkflow::getChatHistory($chat, $Params['user_parameters']['message_id']);

            $tpl = erLhcoreClassTemplate::getInstance('lhchat/loadpreviousmessages.tpl.php');
            $tpl->set('messages', $result['messages']);
            $tpl->set('chat', $chat);
            $tpl->set('initial', $Params['user_parameters_unordered']['initial'] == 1);

            echo json_encode(array('error' => false, 'result' => $tpl->fetch(), 'has_messages' => $result['has_messages'], 'chat_id' => $result['chat_id'], 'message_id' => (int)$result['message_id']));

        } catch (Exception $e) {
            echo $e->getMessage();
        }

} else {
    echo json_encode(array('error' => true));
}

exit;

?>