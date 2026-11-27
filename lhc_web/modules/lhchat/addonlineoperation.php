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
		'lhc_screenshot' => 'lh_inst.makeScreenshot();'
	);
	
	$operation = $form->operation;
	
	if (array_key_exists($operation,$validOperations)) {	
		$onlineuser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['online_user_id']);    
	    $currentUser = erLhcoreClassUser::instance();
	
	    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	        echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	        exit;
	    }

	    if (!($onlineuser instanceof erLhcoreClassModelChatOnlineUser)) {
	        echo json_encode(array('error' => 'true', 'result' => 'Visitor was not found'));
	        exit;
	    }

	    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
	    if ($userDepartments !== true && !$currentUser->hasAccessTo('lhchat','sees_all_online_visitors') && !in_array($onlineuser->dep_id, $userDepartments)) {
	        echo json_encode(array('error' => 'true', 'result' => 'No permission to access this visitor'));
	        exit;
	    }

	    if ($form->operation == "lhc_screenshot") {
	       $onlineuser->operation_chat .= $form->operation . "\n";
	    }

	    if ($onlineuser->chat_id > 0) {
	        $chat = erLhcoreClassModelChat::fetch($onlineuser->chat_id);
	        if ($chat instanceof erLhcoreClassModelChat) {
                $chat->operation .= $form->operation . "\n";
                $chat->updateThis(array('update' => array('operation')));
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.added_operation',array('chat' => & $chat));
            }
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