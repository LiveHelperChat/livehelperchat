<?php

/**
 * Override
 * */
try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if ((int)erLhcoreClassModelChatConfig::fetch('disable_send')->current_value == 1){
    exit;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || erLhcoreClassChat::canReopen($chat,true)))
{
    if ( ezcInputForm::hasPostData() ) {
        
        header('content-type: application/json; charset=utf-8');
        
        $definition = array(
            'email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'email' ) )
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Wrong email address');
        }

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !isset($_POST['csfr_token']) || $_POST['csfr_token'] != $_SERVER['HTTP_X_CSRFTOKEN']) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Invalid CSRF token!');
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_send',array('chat' => & $chat, 'errors' => & $Errors));

        if ( empty($Errors) ) {

            $tpl = erLhcoreClassTemplate::getInstance('lhchat/sendmail.tpl.php');
            $mailTemplate = erLhAbstractModelEmailTemplate::fetch(3);
            erLhcoreClassChatMail::prepareSendMail($mailTemplate);
            $mailTemplate->recipient = $form->email;

            $messages = array_reverse(erLhcoreClassModelmsg::getList(array('customfilter' => array('user_id != -1'),'limit' => 500, 'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));

            // Fetch chat messages
            $tpl = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
            $tpl->set('chat', $chat);
            $tpl->set('messages', $messages);

            $mailTemplate->content = str_replace(array('{user_chat_nick}','{messages_content}','{chat_id}'), array($chat->nick, $tpl->fetch(), $chat->id), $mailTemplate->content);

            erLhcoreClassChatMail::sendMail($mailTemplate, $chat);

            echo json_encode(array('error' => 'false'));
            exit;
        } else {
            $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');
            $tpl->set('errors',$Errors);
            echo json_encode(array('error' => 'true','result' => $tpl->fetch()));
            exit;
        }

    } else {
        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/sendchat.tpl.php');
        $tpl->set('chat',$chat);
        echo $tpl->fetch();
    }
}
exit;
?>