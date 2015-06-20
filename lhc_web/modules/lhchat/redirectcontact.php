<?php

$chat = erLhcoreClassChat::getSession ()->load ( 'erLhcoreClassModelChat', $Params ['user_parameters'] ['chat_id'] );

if (erLhcoreClassChat::hasAccessToRead ( $chat )) {
	$currentUser = erLhcoreClassUser::instance ();
	
	if (! isset ( $_SERVER ['HTTP_X_CSRFTOKEN'] ) || ! $currentUser->validateCSFRToken ( $_SERVER ['HTTP_X_CSRFTOKEN'] )) {
		echo json_encode ( array (
				'error' => 'true',
				'result' => 'Invalid CSRF Token' 
		) );
		exit ();
	}
	
	$userData = $currentUser->getUserData();
	
	erLhcoreClassChatHelper::redirectToContactForm(array(
	    'user' => $userData,
	    'chat' => $chat,
	));	
}

echo json_encode ( array (
		'error' => 'false' 
) );

exit ();

?>