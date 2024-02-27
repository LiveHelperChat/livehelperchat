<?php

header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }
    
    $db = ezcDbInstance::get();
    $db->beginTransaction();

    $conv = erLhcoreClassModelMailconvConversation::fetchAndLock($Params['user_parameters']['id']);

    if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToWrite($conv))
    {
        $item = erLhcoreClassModelMailconvConversation::fetch( $Params['user_parameters']['id']);
        $item->removeThis();

        $db->commit();
        echo json_encode('ok');

    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No permission to read conversation.'));
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