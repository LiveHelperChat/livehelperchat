<?php
header('content-type: application/json; charset=utf-8');
/**
 * These operations are executed directly in user site. Not in iframe
 * */
$definition = array(
        'operation' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->operation) != '')
{
	$validOperations = array(
		'lhc_screenshot' => 'lh_inst.makeScreenshot();',
		'lhc_cobrowse_ou' => 'lh_inst.startCoBrowse(\'{online_user_id}_{online_user_hash}\',\'onlineuser\');',
	    'lhc_cobrowse_multi_command__' => 'lh_inst.handleMessage()'
	);
	
	$operation = $form->operation;
		
	if (strpos($operation, 'lhc_cobrowse_multi_command__') !== false) {
	    $validOperations['lhc_cobrowse_multi_command__'] = 'lh_inst.handleMessage('.json_encode(array('data' => str_replace('lhc_cobrowse_multi_command__', '', $operation))).');';
	    $operation = 'lhc_cobrowse_multi_command__';
	}
	
	if (array_key_exists($operation,$validOperations)) {	
		$onlineuser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_user_id']);    
	    $currentUser = erLhcoreClassUser::instance();
	
	    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	        echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	        exit;
	    }
	    
	    if ($form->operation == "lhc_screenshot") {
	       $onlineuser->operation_chat .= $form->operation . "\n";
	    }
	    	    
	    $onlineuser->operation .= str_replace(array('{online_user_id}','{online_user_hash}'), array($onlineuser->id,$onlineuser->vid), $validOperations[$operation]);
	    $onlineuser->saveThis();	    
	}
      
    echo json_encode(array('error' => 'false'));
    
} else {
    echo json_encode(array('error' => 'true'));
}

exit;

?>