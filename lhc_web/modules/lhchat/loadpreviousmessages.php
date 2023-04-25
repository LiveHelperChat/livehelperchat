<?php

$db = ezcDbInstance::get();
$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat)
{
        $userData = $currentUser->getUserData();

        try {

            $result = erLhcoreClassChatWorkflow::getChatHistory($chat, $Params['user_parameters']['message_id']);

            $tpl = erLhcoreClassTemplate::getInstance('lhchat/loadpreviousmessages.tpl.php');
            $tpl->set('messages', $result['messages']);
            $tpl->set('chat_id_original', $Params['user_parameters_unordered']['original']);
            $tpl->set('chat_id_messages', $result['last_message_chat_id']);
            $tpl->set('chat', $chat);
            $tpl->set('chat_history', $result['chat']);
            $tpl->set('initial', $Params['user_parameters_unordered']['initial'] == 1);
            $tpl->set('message_start', (int)$Params['user_parameters']['message_id']);
            $tpl->set('see_sensitive_information', $currentUser->hasAccessTo('lhchat','see_sensitive_information'));
            
            echo json_encode(array(
                'error' => false,
                'result' => $tpl->fetch(),
                'has_messages' => $result['has_messages'],
                'chat_id' => $result['chat_id'],
                'message_id' => (int)$result['message_id']
            ));

        } catch (Exception $e) {
            echo $e->getMessage();
        }

} else {
    echo json_encode(array('error' => true));
}

exit;

?>