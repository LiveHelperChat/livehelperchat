<?php

session_write_close();

try {
	$file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['file_id']);
	$hash = $Params['user_parameters']['hash'];

	if ( $hash == $file->security_hash ) {

        if (!(isset($_GET['modal']) && $_GET['modal'] === 'true')) {
            header('Content-type: '.$file->type);

            if (!isset($Params['user_parameters_unordered']['inline']) || $Params['user_parameters_unordered']['inline'] != 'true') {
                header('Content-Disposition: attachment; filename="'.$file->id.'-'.$file->chat_id.'.'.$file->extension.'"');
            }
        }

		$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.download', array('chat_file' => $file));

        if (isset($_GET['modal']) && $_GET['modal'] === 'true') {

            $tpl = erLhcoreClassTemplate::getInstance('lhchat/zoomimage.tpl.php');
            $tpl->set('fileImage', $file);
            echo $tpl->fetch();
            exit;

        } else {
            header('Content-length: ' . $file->size);

            // There was no callbacks or file not found etc, we try to download from standard location
            if ($response === false) {
                echo file_get_contents($file->file_path_server);
            } else {
                echo $response['filedata'];
            }
        }
	}

} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>