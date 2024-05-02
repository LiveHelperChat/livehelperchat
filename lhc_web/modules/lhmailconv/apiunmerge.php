<?php
header ( 'content-type: application/json; charset=utf-8' );

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

    // Un-merge by message
    LiveHelperChat\mailConv\helpers\MergeHelper::unMerge($message, ['user_id' => $currentUser->getUserID(), 'name_support' => $currentUser->getUserData()->name_support]);

    echo json_encode(array(
        'result' => true
    ));

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}
exit;

?>