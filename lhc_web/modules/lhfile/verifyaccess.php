<?php

try {

	$file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['file_id']);
	$hash = $Params['user_parameters']['hash'];

	if ( $hash == $file->security_hash ) {

        $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

        $response = array('verified' => false);

        // Chat based file permissions checks
        if ($file->chat_id > 0) {

            $chat = erLhcoreClassModelChat::fetch($file->chat_id);

            if (!($chat instanceof erLhcoreClassModelChat)) {
                $data = erLhcoreClassChatArcive::fetchChatById($file->chat_id);
                if (isset($data['chat']) && is_object($data['chat'])){
                    $chat = $data['chat'];
                }
            }
                        
            $metaData = $file->meta_msg_array;

            if (isset($metaData['verified'])) {
                $response['verified'] = true;
                if ($metaData['verified']['success'] == true) {
                    if (isset($metaData['verified']['sensitive']) && $metaData['verified']['sensitive'] == true) {

                        if (isset($metaData['verified']['protection_image'])) {
                            $response['protection_image'] = erLhcoreClassDesign::design($metaData['verified']['protection_image']);
                        } elseif (isset($metaData['verified']['protection_html'])) {
                            $response['protection_html'] = $metaData['verified']['protection_html'];
                        } else {
                            $response['protection_image'] = erLhcoreClassDesign::design('images/general/sensitive-information.jpg');
                        }

                        if (isset($metaData['verified']['btn_title'])) {
                            $response['btn_title'] = $metaData['verified']['btn_title'];
                        } else {
                            $response['btn_title'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Sensitive Information');
                        }

                    }
                } else {
                    $response['error_msg'] = $metaData['verified']['msg'];
                }
            }
        }

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.verify', array('response' => & $response, 'chat_file' => $file));

        echo json_encode($response);
  	}

} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>