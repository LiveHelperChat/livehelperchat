<?php
/**
 * These operations are executed directly in an iframe. Most of the time it's postMessage
 * */
$definition = array(
    'operation' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::REQUIRED, 'string')
);

$form = new ezcInputForm(INPUT_POST, $definition);

if (trim($form->operation) != '') {
    $operation = $form->operation;
    
    $onlineuser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_user_id']);
    $currentUser = erLhcoreClassUser::instance();
    
    if (! isset($_SERVER['HTTP_X_CSRFTOKEN']) || ! $currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        echo json_encode(array(
            'error' => 'true',
            'result' => 'Invalid CSRF Token'
        ));
        exit();
    }

    if (!($onlineuser instanceof erLhcoreClassModelChatOnlineUser)) {
        echo json_encode(array('error' => 'true', 'result' => 'Visitor was not found'));
        exit();
    }

    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
    if ($userDepartments !== true && !$currentUser->hasAccessTo('lhchat','sees_all_online_visitors') && !in_array($onlineuser->dep_id, $userDepartments)) {
        echo json_encode(array('error' => 'true', 'result' => 'No permission to access this visitor'));
        exit();
    }
    
    $onlineuser->operation_chat .= $form->operation . "\n";
    $onlineuser->saveThis();
    
    echo json_encode(array(
        'error' => 'false'
    ));
} else {
    echo json_encode(array(
        'error' => 'true'
    ));
}

exit();

?>