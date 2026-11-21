<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $userData = erLhcoreClassModelUser::fetch((int) $_GET['user_id']);
    } elseif (isset($_GET['username']) && ! empty($_GET['username'])) {
        $userData = erLhcoreClassModelUser::findOne(array(
            'filter' => array(
                'username' => $_GET['username']
            )
        ));
    } elseif (isset($_GET['email']) && ! empty($_GET['email'])) {
        $userData = erLhcoreClassModelUser::findOne(array(
            'filter' => array(
                'email' => $_GET['email']
            )
        ));
    }
    
    if (! ($userData instanceof erLhcoreClassModelUser)) {
        throw new Exception('User could not be found!');
    }

    // Changing own visibility requires `lhuser/changevisibility`.
    // Changing another operator visibility requires `lhuser/edituser` with
    // access to all of the target user groups.
    if ($userData->id == erLhcoreClassRestAPIHandler::getUserId()) {
        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'changevisibility')) {
            throw new Exception('You do not have permission. `lhuser`, `changevisibility` is required.');
        }
    } elseif (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'edituser') || !erLhcoreClassGroupRole::canEditUserGroups(erLhcoreClassRestAPIHandler::getUser(), $userData)) {
        throw new Exception('You do not have permission. `lhuser`, `edituser` is required and to belong to all other user groups.');
    }

    if ($_GET['status'] == 'false') {
        $userData->invisible_mode = 0;
        $text = 'visibility_on';
    } else {
        $text = 'visibility_off';
        $userData->invisible_mode = 1;
    }

    $userData->operation_admin .= "$('#vi-in-user').text('" . $text . "');";

    erLhcoreClassUser::getSession()->update($userData);

    erLhcoreClassRestAPIHandler::outputResponse(array(
        'invisible' => $userData->invisible_mode
    ));

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_visibility_changed',array('user' => & $userData, 'reason' => 'rest_api'));

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();