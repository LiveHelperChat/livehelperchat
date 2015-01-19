<?php

try {
	$file = erLhAbstractModelFormCollected::fetch((int)$Params['user_parameters']['collected_id']);

	$attr_name = $Params['user_parameters']['attr_name'];
		
	if ( isset($file->content_array[$attr_name]) ) {
		
		$type = $file->content_array[$attr_name]['value']['type'];
		$array = explode('.',$file->content_array[$attr_name]['value']['name']);
		$ext = end($array);
		
		header('Content-type: '.$type);
		header('Content-Disposition: attachment; filename="'.$attr_name.'.'.$ext.'"');
		
		$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.file.download', array('filename' => $file->content_array[$attr_name]['filename']));
		
		// There was no callbacks or file not found etc, we try to download from standard location
		if ($response === false) {
			echo file_get_contents($file->content_array[$attr_name]['filepath'] . $file->content_array[$attr_name]['filename']);
		} else {
			echo $response['filedata'];
		}
	}

} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>