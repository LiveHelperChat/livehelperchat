<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($Params['user_parameters']['id']);

    if (!($conv instanceof \erLhcoreClassModelMailconvConversation)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
        if (isset($mailData['mail'])) {
            $conv = $mailData['mail'];
        }
    }

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv) )
    {
        $conv->is_archive === false && erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conv, 'user_id' => $currentUser->getUserID()]);

        $messages = erLhcoreClassModelMailconvMessage::getList(array('sort' => 'udate ASC', 'filter' => ['conversation_id' => $conv->id]));

        erLhcoreClassChat::prefillGetAttributesObject($conv,
            erLhcoreClassMailconv::$conversationAttributes,
            erLhcoreClassMailconv::$conversationAttributesRemove
        );

        erLhcoreClassChat::prefillGetAttributes($messages,
            erLhcoreClassMailconv::$messagesAttributesLoaded,
            erLhcoreClassMailconv::$messagesAttributesRemoveLoaded
        );

        $db->commit();

        $userData = $currentUser->getUserData();

        if ($conv->is_archive === false) {
            erLhcoreClassMailconvWorkflow::logInteraction($userData->name_support . ' [' . $userData->id.'] '.erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','has closed a conversation by clicking a close button.'), $userData->name_support, $conv->id);
        }

        echo json_encode(['conv' => $conv, 'messages' =>  array_values($messages)],\JSON_INVALID_UTF8_IGNORE);

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to write conversation.'));
    }

} catch (Exception $e) {

    erLhcoreClassLog::write(print_r($e,true));

    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>