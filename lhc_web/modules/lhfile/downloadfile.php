<?php

try {

    if (isset($_GET['modal']) && $_GET['modal'] == 'external') {
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/zoomimage.tpl.php');
        $tpl->set('externalImage', true);
        $tpl->set('fileImage', $_GET['src']);
        echo $tpl->fetch();
        exit;
    }

	$file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['file_id']);
	$hash = $Params['user_parameters']['hash'];

	if ( $hash == $file->security_hash ) {

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        // Chat based file permissions checks
        if ($file->chat_id > 0) {

            $chat = erLhcoreClassModelChat::fetch($file->chat_id);
            if (!($chat instanceof erLhcoreClassModelChat)) {
                $data = erLhcoreClassChatArcive::fetchChatById($file->chat_id);
                if (isset($data['chat']) && is_object($data['chat'])){
                    $chat = $data['chat'];
                }
            }

            $validRequest = false;

            if ((!isset($fileData['chat_file_policy_v']) || $fileData['chat_file_policy_v'] == 0) && isset($Params['user_parameters_unordered']['vhash']) && isset($Params['user_parameters_unordered']['vts'])) {
                $vhash = $Params['user_parameters_unordered']['vhash'];
                $vts = (int)$Params['user_parameters_unordered']['vts'];
                $expectedHash = sha1($file->id . '_' . $file->hash . '_' . $vts . '_' . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
                if ($vhash === $expectedHash && $vts > (time() - 300)) { // Give 5 minutes to download a file
                    $validRequest = true;
                }
            }

            // Will match visitors
            if ($validRequest === false && isset($fileData['chat_file_policy_v']) && $fileData['chat_file_policy_v'] == 1 &&
                is_object($chat) &&
                (
                    in_array($chat->status,[erLhcoreClassModelChat::STATUS_PENDING_CHAT,erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,erLhcoreClassModelChat::STATUS_BOT_CHAT]) ||
                    ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->cls_time > (time() - 600)) // For 10 minutes we allow to download a file
                )) {
                     // Additional validation for visitor files
                    if (isset($Params['user_parameters_unordered']['vhash']) && isset($Params['user_parameters_unordered']['vts'])){
                        $vhash = $Params['user_parameters_unordered']['vhash'];
                        $vts = (int)$Params['user_parameters_unordered']['vts'];
                        $expectedHash = sha1($file->id . '_' . $file->hash . '_' . $vts . '_' . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
                        if ($vhash === $expectedHash && $vts > (time() - 300)) { // Give 5 minutes to download a file
                            $validRequest = true;
                        }
                    }
            }

            $operatorRequest = false;

            // Perhaps it was operator request
            if ($validRequest === false && (!isset($fileData['chat_file_policy_o']) || $fileData['chat_file_policy_o'] == 0) && erLhcoreClassUser::instance()->isLogged() && erLhcoreClassUser::instance()->hasAccessTo('lhfile','use_operator')) {
                $validRequest = true;
                $operatorRequest = true;
            }

            if (isset($fileData['chat_file_policy_o']) && $fileData['chat_file_policy_o'] == 1 && is_object($chat) && $validRequest === false && erLhcoreClassUser::instance()->isLogged() && erLhcoreClassChat::hasAccessToRead($chat)) {
                $validRequest = true;
                $operatorRequest = true;
            }

            $denyImage = 'design/defaulttheme/images/general/denied.png';

            if ($operatorRequest === true && in_array($file->extension,['jfif','jpg','jpeg','png']) && $file->user_id == 0) {
                if (isset($fileData['img_download_policy']) && $fileData['img_download_policy'] == 1) {
                    
                    $minDim = isset($fileData['img_verify_min_dim']) ? (int)$fileData['img_verify_min_dim'] : 100;

                    if ($file->width > $minDim || $file->height > $minDim ) {
                       if (erLhcoreClassUser::instance()->hasAccessTo('lhfile','download_unverified')) {
                            $download_policy = 0;
                        } elseif (erLhcoreClassUser::instance()->hasAccessTo('lhfile','download_verified')) {
                            $download_policy = 1;
                        } else {
                            $download_policy = 2;
                        }

                        $metaData = $file->meta_msg_array;

                        if (isset($metaData['verified'])) {
                            $response['verified'] = true;
                            if ($metaData['verified']['success'] == true) {
                                if (isset($metaData['verified']['sensitive']) && $metaData['verified']['sensitive'] == true) {
                                    if ($download_policy == 2) {
                                        $validRequest = false;
                                        $denyImage = 'design/defaulttheme/images/general/sensitive-information.png';
                                    } else {
                                        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.download_verified', array('chat' => $chat, 'user' => erLhcoreClassUser::instance()->getUserData(true), 'chat_file' => $file));
                                        erLhcoreClassLog::logObjectChange(array(
                                            'object' => $chat,
                                            'action_class' => 'FileReveal',
                                            'user_id' => erLhcoreClassUser::instance()->getUserID(),
                                            'msg' => array(
                                                'file_id' => $file->id,
                                                'name_official' => erLhcoreClassUser::instance()->getUserData(true)->name_official
                                            )
                                        ));
                                    }
                                }
                            } else {
                                if ($download_policy !== 0) {
                                    $validRequest = false;
                                    $denyImage = 'design/defaulttheme/images/general/sensitive-information.png';
                                }
                            }
                        } else {
                            if ($download_policy !== 0) {
                                $validRequest = false;
                                $denyImage = 'design/defaulttheme/images/general/sensitive-information.png';
                            }
                        }
                    }                   
                } 
            }

            if ($validRequest === false) {
                if (in_array($file->extension,['jpg','jpeg','png'])) {
                    header('Content-type: image/png; charset=binary');
                    echo file_get_contents($denyImage);
                    exit;
                } else {
                    exit('No permission to access a file!');
                }
            }

        } // Non chat based files, those are always public

        session_write_close();

        if (!(isset($_GET['modal']) && $_GET['modal'] === 'true')) {
            header('Content-type: '.$file->type);

            if (!isset($Params['user_parameters_unordered']['inline']) || $Params['user_parameters_unordered']['inline'] != 'true') {
                // Download with file name
                header('Content-Disposition: attachment; filename="'.$file->id.'-'.pathinfo($file->upload_name, PATHINFO_FILENAME).'.'.$file->extension.'"');
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