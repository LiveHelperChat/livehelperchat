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
		echo file_get_contents($file->content_array[$attr_name]['filepath'] . $file->content_array[$attr_name]['filename']);
	}

} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>