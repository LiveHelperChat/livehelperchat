<?php

try {
	$file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['file_id']);
	$hash = $Params['user_parameters']['hash'];

	if ( $hash == md5($file->name.'_'.$file->chat_id) ) {
		header('Content-type: '.$file->type);
		header('Content-Disposition: attachment; filename="'.$file->id.'-'.$file->chat_id.'.'.$file->extension.'"');
		echo file_get_contents($file->file_path_server);
	}

} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>