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

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) )
    {

        $returnAttributes = [];

        erLhcoreClassChat::prefillGetAttributesObject($conv,
            erLhcoreClassMailconv::$conversationAttributes,
            erLhcoreClassMailconv::$conversationAttributesRemove
        );

        $returnAttributes['conv'] = $conv;

        erLhcoreClassChat::prefillGetAttributesObject($message,
            erLhcoreClassMailconv::$messagesAttributes,
            erLhcoreClassMailconv::$messagesAttributesRemove
        );

        if (!isset($message->body_front)) {
            $message->body_front = "";
        }

        if (!isset($message->body)) {
            $message->body = "";
        }

        if (!isset($message->alt_body)) {
            $message->alt_body = "";
        }

        $returnAttributes['message'] = $message;

        $db->commit();

        echo json_encode($returnAttributes);

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