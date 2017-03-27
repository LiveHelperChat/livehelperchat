<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

$currentUser = erLhcoreClassUser::instance();

try {

    if ($currentUser->authenticate($_POST['username'], $_POST['password']))
    {
        $sessionToken = null;
        
        if (isset($_POST['generate_token']) && $_POST['generate_token'] == 'true') {
            
            $typeValid = array(
                "unknown" => erLhcoreClassModelUserSession::DEVICE_TYPE_UNKNOWN,
                "android" => erLhcoreClassModelUserSession::DEVICE_TYPE_ANDROID,
                "ios" => erLhcoreClassModelUserSession::DEVICE_TYPE_IOS
            );
                       
            $deviceToken = '';
            $device = erLhcoreClassModelUserSession::DEVICE_TYPE_UNKNOWN;
            
            if ((isset($_POST['device']) && key_exists($_POST['device'], $typeValid)) && $_POST['device'] != '') {
                $device = $typeValid[$_POST['device']];
                if (!isset($_POST['device_token']) || $_POST['device_token'] == '') {
                    throw new Exception('Please provide device token!');
                } else {
                    $deviceToken = $_POST['device_token'];
                }
            } else {
                throw new Exception('Device not provided!');
            }
            
            $uSession = erLhcoreClassModelUserSession::findOne(array('filter' => array('device_token' => $deviceToken, 'device_type' => $device)));
            
            if (!($uSession instanceof erLhcoreClassModelUserSession)) {            
                $uSession = new erLhcoreClassModelUserSession();
            }
            
            $uSession->token = erLhcoreClassModelForgotPassword::randomPassword(40);
            $uSession->device_token = $deviceToken;
            $uSession->user_id = $currentUser->getUserID();
            
            if ($uSession->created_on == 0) {
                $uSession->created_on = time();
            }
            
            $uSession->updated_on = time();
            $uSession->device_type = $device;
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