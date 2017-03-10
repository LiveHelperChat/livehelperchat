<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $jsonData = array();
    
    if (isset($_POST['vid'])) {
        $jsonData['vid'] = $_POST['vid'];
    }
    
    if (isset($_POST['new'])) {
        $jsonData['new'] = $_POST['new'];
    }
       
    try {
        erLhcoreClassChatHelper::mergeVid($jsonData);
        echo json_encode(array(
            'error' => false,
            'result' => 'merged'
        ));
    } catch (Exception $e) {
        echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => $e->getMessage()));
    }
        
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();