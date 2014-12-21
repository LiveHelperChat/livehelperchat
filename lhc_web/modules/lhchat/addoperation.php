<?php

$definition = array(
        'operation' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->operation) != '')
{
	$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    // Has access to read, chat
    if ( erLhcoreClassChat::hasAccessToRead($Chat) )
    {
        $currentUser = erLhcoreClassUser::instance();

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
        	exit;
        }
         	
        $Chat->operation = $form->operation."\n";
        $Chat->updateThis();
      
        echo json_encode(array('error' => 'false'));
    }

} else {
    echo json_encode(array('error' => 'true'));
}

exit;

?>