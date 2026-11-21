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

    // Changing own status requires `lhuser/changeonlinestatus`.
    // Changing another operator status requires `lhuser/setopstatus` or
    // `lhuser/edituser` with access to all of the target user groups.
    if ($userData->id == erLhcoreClassRestAPIHandler::getUserId()) {
        if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'changeonlinestatus')) {
            throw new Exception('You do not have permission. `lhuser`, `changeonlinestatus` is required.');
        }
    } elseif (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'setopstatus') && (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'edituser') || !erLhcoreClassGroupRole::canEditUserGroups(erLhcoreClassRestAPIHandler::getUser(), $userData))) {
        throw new Exception('You do not have permission. `lhuser`, `setopstatus` is required to change another user status.');
    }

    if ($_GET['status'] == 'true') {
        $userData->hide_online = 0;
        $text = 'flash_on';
    } else {
        $text = 'flash_off';
        $userData->hide_online = 1;
    }

    $userData->operation_admin .= "$('#online-offline-user').text('" . $text . "');";

    erLhcoreClassUser::getSession()->update($userData);
    
    erLhcoreClassUserDep::setHideOnlineStatus($userData);
    
    erLhcoreClassRestAPIHandler::outputResponse(array(
        'offline' => $userData->hide_online
    ));

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $userData, 'reason' => 'rest_api'));

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();