<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    
    if (!isset($_POST['token'])) {
        throw new Exception('Token not found!');
    }

    $token = $_POST['token'];

    $uSession = erLhcoreClassModelUserSession::findOne(array('filter' => array('token' => $token)));
        
    if ($uSession instanceof erLhcoreClassModelUserSession)
    {
        $uSession->token = '';
        $uSession->updateThis();
        
        echo json_encode(
            array('error' => false, 'msg' => 'Token was revoked')
        );            
    } else {
        http_response_code(400);
        echo json_encode(
            array('error' => true, 'msg' => 'Token not found')
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