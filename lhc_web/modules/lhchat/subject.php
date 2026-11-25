<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/subject.tpl.php');
$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    if (ezcInputForm::hasPostData()) {

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            echo json_encode(array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Invalid CSRF token!')));
            exit;
        }

        $db = ezcDbInstance::get();
        $db->beginTransaction();
        $response = array();
        if (is_numeric($Params['user_parameters_unordered']['subject'])) {
            if ($Params['user_parameters_unordered']['status'] == 'true') {
                $subjectChat = erLhAbstractModelSubjectChat::findOne(array('filter' => array('chat_id' => $chat->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                if (!($subjectChat instanceof erLhAbstractModelSubjectChat)) {
                    $subjectChat = new erLhAbstractModelSubjectChat();
                }

                $subjectChat->chat_id = $chat->id;
                $subjectChat->subject_id = $Params['user_parameters_unordered']['subject'];
                $subjectChat->saveThis();

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.subject_add',array('user_id' => $currentUser->getUserID(), 'chat' => & $chat, 'init' => 'op', 'subject_id' => $Params['user_parameters_unordered']['subject']));
                
                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject selected!'));

            } else {
                $subjectChat = erLhAbstractModelSubjectChat::findOne(array('filter' => array('chat_id' => $chat->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                if ($subjectChat instanceof erLhAbstractModelSubjectChat) {
                    $subjectChat->removeThis();
                }

                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject unselected!'));

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.subject_remove',array('user_id' => $currentUser->getUserID(), 'chat' => & $chat, 'init' => 'op', 'subject_id' => $Params['user_parameters_unordered']['subject']));
            }
        } else {
            $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Please choose a subject'));
        }

        $db->commit();
        echo json_encode($response);
        exit;
    }

    $tpl->set('chat', $chat);
    echo $tpl->fetch();
    exit;
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    $tpl->set('auto_close_dialog',true);
    $tpl->set('chat_id',(int)$Params['user_parameters']['chat_id']);
    echo $tpl->fetch();
    exit;
}

exit;

?>