<?php

try {
	$file = erLhcoreClassModelDocShare::fetch((int)$Params['user_parameters']['id']);
	
	if ($file->active == 0 && (!erLhcoreClassUser::instance()->isLogged() || !erLhcoreClassUser::instance()->hasAccessTo('lhdocshare', 'manage_dc'))) {	
		erLhcoreClassModule::redirect();
		exit;
	}
	
	header('Content-type: '.$file->type);
	header('Content-Disposition: attachment; filename="'.$file->file_name_upload.'"');
	echo file_get_contents($file->file_path_server);
	
} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>