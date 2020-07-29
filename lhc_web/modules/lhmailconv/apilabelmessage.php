<?php

try {

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    $conv = $message->conversation;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {
        if (ezcInputForm::hasPostData()) {

            $db = ezcDbInstance::get();
            $db->beginTransaction();
            $response = array();
            if (is_numeric($Params['user_parameters_unordered']['subject'])) {
                if ($Params['user_parameters_unordered']['status'] == 'true') {

                    $subjectChat = erLhcoreClassModelMailconvMessageSubject::findOne(array('filter' => array('message_id' => $message->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                    if (!($subjectChat instanceof erLhcoreClassModelMailconvMessageSubject)) {
                        $subjectChat = new erLhcoreClassModelMailconvMessageSubject();
                    }

                    $subjectChat->message_id = $message->id;
                    $subjectChat->conversation_id = $message->conversation_id;
                    $subjectChat->subject_id = (int)$Params['user_parameters_unordered']['subject'];
                    $subjectChat->saveThis();

                    $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject selected!'));

                } else {
                    $subjectChat = erLhcoreClassModelMailconvMessageSubject::findOne(array('filter' => array('message_id' => $message->id, 'subject_id' => $Params['user_parameters_unordered']['subject'])));

                    if ($subjectChat instanceof erLhcoreClassModelMailconvMessageSubject) {
                        $subjectChat->removeThis();
                    }

                    $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject unselected!'));
                }
            } else {
                $response = array('error' => false, 'message' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Please choose a subject'));
            }

            $db->commit();
            echo json_encode($response);
            exit;
        }

        $tpl = erLhcoreClassTemplate::getInstance('lhmailconv/apilabelmessage.tpl.php');

        $mail = erLhcoreClassModelMailconvConversation::fetch($Params['user_parameters']['id']);

        if (erLhcoreClassChat::hasAccessToRead($conv)) {
            $tpl->set('conv', $conv);
            $tpl->set('message', $message);
        } else {
            $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
        }

        echo $tpl->fetch();
        exit;


    } else {
        throw new Exception("No permission to read conversation.");
    }

} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/errors/adminchatnopermission.tpl.php');
    echo $tpl->fetch();
    exit;
}

?>