<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

$currentUser = erLhcoreClassUser::instance();

try {

    if ($currentUser->authenticate($_POST['username'], $_POST['password']))
    {
        $sessionToken = null;
        
        if (isset($_POST['generate_token'])) {
            
            $uSession = new erLhcoreClassModelUserSession();
            $uSession->token = erLhcoreClassModelForgotPassword::randomPassword(40);
            $uSession->device_token = isset($_POST['device_token']) ? $_POST['device_token'] : '';
            $uSession->user_id = $currentUser->getUserID();
            $uSession->created_on = time();
            $uSession->updated_on = time();
            $uSession->device_type = isset($_POST['device']) ? $_POST['device'] : '';
            $uSession->saveThis();
            
            $sessionToken = $uSession->token;
        }
    
        echo json_encode(
            array('error' => false, 'session_token' => $sessionToken)
        );
            
    } else {
        http_response_code(400);
        echo json_encode(
                array('error' => true, 'msg' => 'Authentification failed')
        );
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(
        array('error' => true, 'msg' => $e->getMessage())
    );
}

exit;
?>