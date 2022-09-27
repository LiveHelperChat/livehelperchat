<?php
header('content-type: application/json; charset=utf-8');

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
    exit;
}

$definition = array(
    'data' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    ),
    'identifier' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    )
);

$form = new ezcInputForm( INPUT_POST, $definition );

$msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);

$chat = erLhcoreClassModelChat::fetch($msg->chat_id);

$errorTpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    if ($form->hasInputField('data') && $form->hasValidData('data')) {
        $errors = array();
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_save_reaction',array('chat' => & $chat, 'errors' => & $errors));

        if (empty($errors)) {

            // Operator Reaction
            LiveHelperChat\Helpers\Reaction::operatorReaction($chat, $msg, [
                'payload-id' => $form->identifier,
                'payload' => $form->data
            ]);

            $reactions = new erLhcoreClassTemplate( 'lhgenericbot/message/reaction_to_visitor.tpl.php');
            $reactions->set('msg', $msg->getState());
            $reactions->set('metaMessageData', $msg->meta_msg_array);
            $reactions->set('chat', $chat);
            $reactions->set('messageId', $msg->id);
            $reactionBody = $reactions->fetch();

            echo json_encode(array('error' => 'false','result' => $reactionBody));
            exit;
        } else {
            $errorTpl->set('errors', $errors);
            echo json_encode(array('error' => 'true', 'result' => $errorTpl->fetch()));
            exit;
        }
    } else {
        $errorTpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Form data not valid')));
        echo json_encode(array('error' => 'true', 'result' => $errorTpl->fetch()));
        exit;
    }
} else {
    $errorTpl->set('errors', array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Has no access to this chat')));
    echo json_encode(array('error' => 'true', 'result' => $errorTpl->fetch()));
    exit;
}
?>