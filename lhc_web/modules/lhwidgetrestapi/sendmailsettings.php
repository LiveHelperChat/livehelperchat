<?php

erLhcoreClassRestAPIHandler::setHeaders();

$db = ezcDbInstance::get();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $Params['user_parameters']['hash'])
{
    if ($Params['user_parameters_unordered']['action'] == 'send') {

        if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 0 && ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || erLhcoreClassChat::canReopen($chat,true)))
        {
            $payload = json_decode(file_get_contents('php://input'),true);
            if (isset($payload['email']) && filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_send', array('chat' => & $chat, 'errors' => & $Errors));

                $mailTemplate = erLhAbstractModelEmailTemplate::fetch(3);
                $mailTemplate->translate($chat->chat_locale);

                erLhcoreClassChatMail::prepareSendMail($mailTemplate, $chat);
                $mailTemplate->recipient = $payload['email'];

                $messages = array_reverse(erLhcoreClassModelmsg::getList(array('customfilter' => array('user_id != -1'),'limit' => 500, 'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));

                // Fetch chat messages
                $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
                $tpl->set('chat', $chat);
                $tpl->set('messages', $messages);

                $mailTemplate->content = str_replace(array('{user_chat_nick}','{messages_content}','{chat_id}'), array($chat->nick, $tpl->fetch(), $chat->id), $mailTemplate->content);

                erLhcoreClassChatMail::sendMail($mailTemplate, $chat);

                echo json_encode(array('error' => false));
                exit;

            } else {
                $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Invalid email address');
            }

        } else {
            $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Invalid chat!');
        }

        $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');
        $tpl->set('errors',$errors);
        $tpl->set('hideErrorButton',true);

        echo json_encode(array('error' => true, 'result' => $tpl->fetch()));
        exit;

    } else {
        echo $chat->email;
    }
}

exit;

?>