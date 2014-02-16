<?php

$definition = array(
        'operation' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'string'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->operation) != '')
{
	$validOperations = array(
		'lhc_screenshot' => 'lh_inst.makeScreenshot();'
	);
	
	if (array_key_exists($form->operation,$validOperations)) {	
		$onlineuser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_user_id']);    
	    $currentUser = erLhcoreClassUser::instance();
	
	    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	        echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	        exit;
	    }
	         	
	    $onlineuser->operation .= $validOperations[$form->operation];
	    $onlineuser->saveThis();
	}
      
    echo json_encode(array('error' => 'false'));
    
} else {
    echo json_encode(array('error' => 'true'));
}

exit;

?>