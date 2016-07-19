<?php
/**
 * These operations are executed directly in an iframe. Most of the time it's postMessage
 * */
$definition = array(
        'operation' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->operation) != '')
{
	$db = ezcDbInstance::get();
	$db->beginTransaction();
			
	$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    $errors = [];

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_screenshot_addoperacion',array('chat' => & $Chat, 'errors' => & $errors));
    // Has access to read, chat
    if ( erLhcoreClassChat::hasAccessToRead($Chat) && empty($errors) )
    {
        $currentUser = erLhcoreClassUser::instance();

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
        	exit;
        }
         	
        $Chat->operation .= $form->operation."\n";
        $Chat->updateThis();
      
        echo json_encode(array('error' => 'false'));
    } else {
        echo json_encode(array('error' => 'true', 'result' => 'Errors found' ));
    }
    
    $db->commit();
} else {
    echo json_encode(array('error' => 'true'));
}

exit;

?>