<?php

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSFR Token' ));
	exit;
}

try {
	$file = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatFile', $Params['user_parameters']['file_id']);

	if ( ($file->user_id == $currentUser->getUserID() || ($file->chat !== false && $file->chat->user_id == $currentUser->getUserID()) ) && $currentUser->hasAccessTo('lhfile','file_delete_chat')){
		$file->removeThis();
		echo json_encode(array('error' => 'false' ));
		exit;
	} else {
		throw new Exception('No permission to delete the file!');
	}

} catch (Exception $e) {
	echo json_encode(array('error' => 'true', 'result' => $e->getMessage() ));
	exit;
}


exit;
?>

