<?php

session_write_close();

try {
    $file = erLhcoreClassModelMailconvFile::fetch((int)$Params['user_parameters']['id']);

    // Handle if file is archived
    if (!($file instanceof \erLhcoreClassModelMailconvFile)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
        if (isset($mailData['mail'])) {
            $file = \LiveHelperChat\Models\mailConv\Archive\File::fetch((int)$Params['user_parameters']['id']);
        }
    }

    if ($file->disposition != 'INLINE') {
        $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
        $mcOptionsData = (array)$mcOptions->data;
        if ($file->extension === 'jpg' && !str_ends_with($file->name, '.jpg')) {
            header('Content-Disposition: '. (!isset($mcOptionsData['download_view_mode']) || $mcOptionsData['download_view_mode'] == 0 ? 'attachment' : 'inline') . '; filename="'.$file->name . '.jpg' .'"');
        } else {
            header('Content-Disposition: '. (!isset($mcOptionsData['download_view_mode']) || $mcOptionsData['download_view_mode'] == 0 ? 'attachment' : 'inline') . '; filename="'.$file->name.'"');
        }
    }

    $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

    // Make sure file exists on server
    if (!file_exists($file->file_path_server)) {
        erLhcoreClassMailconvParser::fetchFile($file,($fileData['max_res_mail'] ?? 0));
    }

    $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
    $mcOptionsData = (array)$mcOptions->data;

    $restrictedFile = false;
    $restrictedReason = '';
    
    // Check if file extension restrictions are configured
    if ( isset($mcOptionsData['file_download_mode']) && $mcOptionsData['file_download_mode'] == 1 && (isset($mcOptionsData['allowed_extensions_public']) || isset($mcOptionsData['allowed_extensions_restricted']))) {
        $allowedExtensionsPublic = isset($mcOptionsData['allowed_extensions_public']) ? explode('|', strtolower($mcOptionsData['allowed_extensions_public'])) : array();
        $allowedExtensionsRestricted = isset($mcOptionsData['allowed_extensions_restricted']) ? explode('|', strtolower($mcOptionsData['allowed_extensions_restricted'])) : array();
        
        $fileExtension = strtolower($file->extension);
        
        // Check if user has download_restricted permission
        $hasRestrictedAccess = erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','download_restricted');
        
        // If file extension is not in public allowed extensions
        if (empty($allowedExtensionsPublic) || (!empty($allowedExtensionsPublic) && !in_array($fileExtension, $allowedExtensionsPublic))) {
            // Check if it's in restricted extensions and user has permission
            if (!empty($allowedExtensionsRestricted) && in_array($fileExtension, $allowedExtensionsRestricted)) {
                if (!$hasRestrictedAccess) {
                    $restrictedFile = true;
                    $restrictedReason = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','You do not have permission to download that type of files!');
                }
            } else {
                // File extension is not allowed at all
                $restrictedFile = true;
                $restrictedReason = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','This type of files are not allowed to be downloaded!');
            }
        }
        
        // If file is restricted, deny access
        if ($restrictedFile) {
            header('HTTP/1.1 403 Forbidden');           
            die($restrictedReason);
        }
    }

    $validRequest = true;

    if (
        (isset($fileData['mail_file_policy']) && $fileData['mail_file_policy'] === 1) || 
        (isset($fileData['mail_img_download_policy']) && $fileData['img_download_policy'] === 1)
        ) {
        if ($file->is_archive === false) {
            $mail = erLhcoreClassModelMailconvMessage::fetch($file->message_id);
        } else {
            $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetch($file->message_id);
        }
    }

    if (isset($fileData['mail_file_policy']) && $fileData['mail_file_policy'] === 1) {
        $conv = $mail->conversation;

        if (!($conv instanceof erLhcoreClassModelMailconvConversation) || !erLhcoreClassChat::hasAccessToRead($conv)) {
            $validRequest = false;
        }
    }

    $denyImage = 'design/defaulttheme/images/general/denied.png';

    if (in_array($file->extension, erLhcoreClassMailconvParser::IMAGE_EXTENSIONS) && file_exists($file->file_path_server) && (!isset($fileData['mail_img_verify_skip']) || !in_array($file->extension, explode('|',$fileData['mail_img_verify_skip'])))) {
        if (isset($fileData['mail_img_download_policy']) && $fileData['mail_img_download_policy'] === 1) {
            
            $minDim = isset($fileData['mail_img_verify_min_dim']) ? (int)$fileData['mail_img_verify_min_dim'] : 100;

            $width = $file->width > 0 ? $file->width : 0;
            $height = $file->height > 0 ? $file->height : 0;

            if ($width == 0 || $height == 0) {
                list($width, $height) = getimagesize($file->file_path_server);
            }

            if ($width > $minDim || $height > $minDim) {

                if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','download_unverified')) {
                    $download_policy = 0;
                } elseif (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','download_verified')) {
                    $download_policy = 1;
                } else {
                    $download_policy = 2;
                }

                $metaData = $file->meta_msg_array;

                if (isset($metaData['verified'])) {
                    $response['verified'] = true;
                    if (isset($metaData['verified']['success']) && $metaData['verified']['success'] == true) {
                        if (isset($metaData['verified']['sensitive']) && $metaData['verified']['sensitive'] == true) {
                            if ($download_policy == 2) {
                                $validRequest = false;
                                $denyImage = 'design/defaulttheme/images/general/sensitive-information.png';
                            } else {
                                
                                $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.file.download_verified', array('mail' => $mail, 'user' => erLhcoreClassUser::instance()->getUserData(true), 'chat_file' => $file));

                                erLhcoreClassLog::logObjectChange(array(
                                    'check_log' => true,
                                    'object' => $mail,
                                    'object_id' => $mail->conversation_id,
                                    'action_class' => 'FileReveal',
                                    'user_id' => erLhcoreClassUser::instance()->getUserID(),
                                    'msg' => array(
                                        'message_id' => $mail->id,
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
        if (in_array($file->extension, erLhcoreClassMailconvParser::IMAGE_EXTENSIONS)) {
            header('Content-type: image/png; charset=binary');
            echo file_get_contents($denyImage);
            exit;
        } else {
            exit('No permission to access a file!');
        }
    }

    header('Content-type: '.$file->type);

    if (file_exists($file->file_path_server)) {
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
        header("Cache-Control: private, max-age=3600");
        echo file_get_contents($file->file_path_server);
    } else {
        echo file_get_contents('design/defaulttheme/images/general/denied.png');
        http_response_code(404);
    }


} catch (Exception $e) {
    \erLhcoreClassLog::write(
        $e->getMessage(),
        \ezcLog::SUCCESS_AUDIT,
        array(
            'source' => 'lhc',
            'category' => 'mail_import_failure',
            'line' => __LINE__,
            'file' => __FILE__,
            'object_id' => $file->id
        )
    );
    http_response_code(404);
}
exit;

?>