<?php

try {
	$file = erLhcoreClassModelDocShare::fetch((int)$Params['user_parameters']['id']);
	
	if ($file->active == 0 && (!erLhcoreClassUser::instance()->isLogged() || !erLhcoreClassUser::instance()->hasAccessTo('lhdocshare', 'manage_dc'))) {	
		erLhcoreClassModule::redirect();
		exit;
	}
	
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="'.$file->file_name_upload_pdf.'"');
	echo file_get_contents($file->pdf_file_path_server);
	
} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>