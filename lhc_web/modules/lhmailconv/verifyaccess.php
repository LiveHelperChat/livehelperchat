<?php

try {

    $file = erLhcoreClassModelMailconvFile::fetch((int)$Params['user_parameters']['id']);

    // Handle if file is archived
    if (!($file instanceof \erLhcoreClassModelMailconvFile)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
        if (isset($mailData['mail'])) {
            $file = \LiveHelperChat\Models\mailConv\Archive\File::fetch((int)$Params['user_parameters']['id']);
        }
    }
   
    $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

    if (!file_exists($file->file_path_server)) {
        erLhcoreClassMailconvParser::fetchFile($file, ($fileData['max_res_mail'] ?? 0));
    }

    if (isset($fileData['mail_img_verify_skip']) && in_array($file->extension, explode('|',$fileData['mail_img_verify_skip']))) {
        echo json_encode(['verified' => true]);
        exit;
    }

    $validRequest = true;

    if ($file->is_archive === false) {
        $mail = erLhcoreClassModelMailconvMessage::fetch($file->message_id);
    } else {
        $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetch($file->message_id);
    }

    if (isset($fileData['mail_file_policy']) && $fileData['mail_file_policy'] === 1) {

        $conv = $mail->conversation;

        if (!($conv instanceof erLhcoreClassModelMailconvConversation) || !erLhcoreClassChat::hasAccessToRead($conv)) {
            $validRequest = false;
        }
    }

    if ($validRequest === false) {
        echo json_encode(['verified' => true, "error_msg" => "Access denied"]);
        exit;
    }

    $metaData = $file->meta_msg_array;

    $response = array('verified' => false);
   
    if (isset($metaData['verified']) && (isset($metaData['verified']['success']) || isset($metaData['verified']['msg']))) {

        $response['verified'] = true;

        if (isset($metaData['verified']['success']) && $metaData['verified']['success'] == true) {
            if (isset($metaData['verified']['sensitive']) && $metaData['verified']['sensitive'] == true) {
                if (isset($metaData['verified']['protection_image'])) {
                    $response['protection_image'] = erLhcoreClassDesign::design($metaData['verified']['protection_image']);
                } elseif (isset($metaData['verified']['protection_html'])) {
                    $response['protection_html'] = $metaData['verified']['protection_html'];
                } else {
                    $response['protection_image'] = erLhcoreClassDesign::design('images/general/sensitive-information.png');
                }

                if (isset($metaData['verified']['btn_title'])) {
                    $response['btn_title'] = $metaData['verified']['btn_title'];
                } else {
                    $response['btn_title'] = erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/conversation','Sensitive Information');
                }
            }
        } else {
            $response['error_msg'] = $metaData['verified']['msg'];
        }

    } elseif (!isset($metaData['verified']['started']) || (time() - $metaData['verified']['started']) > 60) {

        $requireVerification = true;

        $width = $file->width > 0 ? $file->width : 0;
        $height = $file->height > 0 ? $file->height : 0;

        if ($width == 0 || $height == 0) {
            list($width, $height) = getimagesize($file->file_path_server);
        }

        if ($width > 0 && $height > 0 && $width < 10000 && $height < 10000) {

            if ($file->width == 0 && $file->height == 0) {
                $file->width = $width;
                $file->height = $height;
                $file->updateThis(['update' => ['width', 'height']]);
            }

            if (isset($fileData['mail_img_verify_min_dim'])) {
                $minDim = (int)$fileData['mail_img_verify_min_dim'];
                $metaMsgArray = $file->meta_msg_array;

                if ($width < $minDim && $height < $minDim)  {
                    $requireVerification = false;
                }
            }
        }

        if ($requireVerification) {
            $metaData['verified']['started'] = time();

            $file->meta_msg_array = $metaData;
            $file->meta_msg = json_encode($metaData);
            $file->saveThis();

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.file.verify_start', array('mail' => $mail, 'chat_file' => $file));
            
        } else {
            $response['verified'] = true;    
        }
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.file.verify', array('response' => & $response, 'chat_file' => $file));

    echo json_encode($response);    

} catch (Exception $e) {
    header('Location: /');
    exit;
}
exit;

?>
