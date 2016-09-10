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
    
    if ($_GET['status'] == 'true') {
        $userData->hide_online = 0;
    } else {
        $userData->hide_online = 1;
    }
    
    erLhcoreClassUser::getSession()->update($userData);
    
    erLhcoreClassUserDep::setHideOnlineStatus($userData);
    
    erLhcoreClassRestAPIHandler::outputResponse(array(
        'offline' => $userData->hide_online
    ));

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed',array('user' => & $userData));

} catch (Exception $e) {
    echo json_encode(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();