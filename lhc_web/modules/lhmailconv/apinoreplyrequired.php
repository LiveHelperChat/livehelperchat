<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }
    
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $message = erLhcoreClassModelMailconvMessage::fetchAndLock($Params['user_parameters']['id']);

    $conv = $message->conversation;

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv) )
    {

        if ($message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED) {
            $message->lr_time = time();
            $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED;
            $message->response_time = $message->lr_time - $message->accept_time;
            $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
            $message->user_id = $conv->user_id;
            $message->conv_user_id = $conv->user_id;
            $message->updateThis();
        }

        $returnAttributes = [];

        // There are no more unresponded messages in this conversation.
        if (erLhcoreClassModelMailconvMessage::getCount(['filternot' => ['status' => erLhcoreClassModelMailconvMessage::STATUS_RESPONDED],'filter' => ['conversation_id' => $conv->id]]) == 0) {

            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conv, 'user_id' => $currentUser->getUserID()]);

            erLhcoreClassChat::prefillGetAttributesObject($conv,
                erLhcoreClassMailconv::$conversationAttributes,
                erLhcoreClassMailconv::$conversationAttributesRemove
            );

            $message->refreshThis();

            $returnAttributes['conv'] = $conv;
        }

        erLhcoreClassChat::prefillGetAttributesObject($message,
            erLhcoreClassMailconv::$messagesAttributes,
            erLhcoreClassMailconv::$messagesAttributesRemove
        );

        $returnAttributes['message'] = $message;

        $db->commit();

        echo json_encode($returnAttributes,\JSON_INVALID_UTF8_IGNORE);

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read conversation.'));
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>