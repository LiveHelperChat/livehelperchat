<?php

erLhcoreClassRestAPIHandler::setHeaders();

$modeAPI = isset($_GET['api']) && $_GET['api'] == 'true';

try {
    
    if (!isset($_GET['token'])) {
        throw new Exception('Token not found!');
    }

    $token = $_GET['token'];

    $uSession = erLhcoreClassModelUserSession::findOne(array('filter' => array('token' => $token)));
        
    if ($uSession instanceof erLhcoreClassModelUserSession)
    {
        $currentUser = erLhcoreClassUser::instance();
        $instance = erLhcoreClassSystem::instance();
               
        $userToLogin = erLhcoreClassModelUser::fetch((int)$uSession->user_id);           
        
        $r = '';
        if (isset($_GET['r'])) {
            $r = rawurldecode($_GET['r']);
        }

        if ($userToLogin instanceof erLhcoreClassModelUser) {
            erLhcoreClassUser::instance()->setLoggedUser($userToLogin->id);
            if ($modeAPI == false) {
                header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin').'/'.ltrim($r, '/'));
                exit;
            } else {
                echo json_encode(array('error' => false, 'msg' => 'Session started', 'url' => erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('site_admin').'/'.ltrim($r,'/')));
                exit;
            }
        } else {
            throw new Exception('User could not be found!');
        }

    } else {
        throw new Exception('Token not found');        
    }    

} catch (Exception $e) {
    
    if ($modeAPI == false) {
        http_response_code(400);
        die($e->getMessage());
    } else {
        http_response_code(400);
        echo json_encode(
            array('error' => true, 'msg' => $e->getMessage())
        );
    }
}

exit;
?>