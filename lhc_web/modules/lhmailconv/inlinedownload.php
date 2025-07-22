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
        header('Content-Disposition: attachment; filename="'.$file->name.'"');
    }

    $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;

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

    if (in_array($file->extension,['jfif','jpg','jpeg','png'])) {
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
                    if ($metaData['verified']['success'] == true) {
                        if (isset($metaData['verified']['sensitive']) && $metaData['verified']['sensitive'] == true) {
                            if ($download_policy == 2) {
                                $validRequest = false;
                                $denyImage = 'design/defaulttheme/images/general/sensitive-information.png';
                            } else {
                                
                                $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailconv.file.download_verified', array('mail' => $mail, 'user' => erLhcoreClassUser::instance()->getUserData(true), 'chat_file' => $file));

                                erLhcoreClassLog::logObjectChange(array(
                                    'check_log' => true,
                                    'object' => $mail,
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
        if (in_array($file->extension,['jpg','jpeg','png','jfif'])) {
            header('Content-type: image/png; charset=binary');
            echo file_get_contents($denyImage);
            exit;
        } else {
            exit('No permission to access a file!');
        }
    }

    header('Content-type: '.$file->type);

    if (file_exists($file->file_path_server)) {
        echo file_get_contents($file->file_path_server);
    } else {

        if ($file->is_archive === false){
            $mail = erLhcoreClassModelMailconvMessage::fetch($file->message_id);
        } else {
            $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetch($file->message_id);
        }

        $mailbox = $mail->mailbox;

        if ($mailbox->auth_method != erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
            $mailboxHandler = new PhpImap\Mailbox(
                $mailbox->imap, // IMAP server incl. flags and optional mailbox folder
                $mailbox->username, // Username for the before configured mailbox
                $mailbox->password, // Password for the before configured username
                false
            );

            $mail = $mailboxHandler->getMail($mail->uid, false);
        } else {
            $mailboxHandler = \LiveHelperChat\mailConv\OAuth\OAuth::getClient($mailbox);
            $mailboxFolderOAuth = $mailboxHandler->getFolderByPath($mail->mb_folder);

            $messagesCollection = $mailboxFolderOAuth->search()->whereUid($mail->uid)->get();

            if ($messagesCollection->total() == 1) {
                $mail = $messagesCollection->shift();
            }
        }

        if ($mail->hasAttachments() == true) {
            foreach ($mail->getAttachments() as $attachment) {
                if ((int)$attachment->sizeInBytes == 0) {
                    continue;
                }

                if (
                    $file->name == $attachment->name &&
                    $file->content_id == (string)$attachment->contentId &&
                    $file->size = (int)$attachment->sizeInBytes
                ) {

                    if ($mailbox->auth_method != erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
                        $fileBody = $attachment->getContents();
                    } else {
                        $fileBody = $attachment->getContent();
                    }

                    // Try to save file again
                    $cfg = erConfigClassLhConfig::getInstance();

                    $defaultGroup = $cfg->getSetting( 'site', 'default_group', false );
                    $defaultUser = $cfg->getSetting( 'site', 'default_user', false );

                    if (empty($file->file_path)) {
                        $dir = 'var/tmpfiles/';
                        $fileName = md5($file->id . '_' . $file->name . '_' . $file->attachment_id);

                        $cfg = erConfigClassLhConfig::getInstance();

                        $defaultGroup = $cfg->getSetting( 'site', 'default_group', false );
                        $defaultUser = $cfg->getSetting( 'site', 'default_user', false );

                        erLhcoreClassFileUpload::mkdirRecursive( $dir, true, $defaultUser, $defaultGroup);

                        $localFile = $dir . $fileName;
                        file_put_contents($localFile, $fileBody);

                        $dir = 'var/storagemail/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $file->id . '/';

                        erLhcoreClassFileUpload::mkdirRecursive( $dir, true, $defaultUser, $defaultGroup);

                        rename($localFile, $dir . $fileName);
                        chmod($dir . $fileName, 0644);

                        if ($defaultUser != '') {;
                            chown($dir, $defaultUser);
                        }

                        if ($defaultGroup != '') {
                            chgrp($dir, $defaultGroup);
                        }

                        $file->file_name = $fileName;
                        $file->file_path = $dir;
                        $file->file_path_server = $file->file_path . $file->file_name;
                        $file->updateThis(['update' => ['file_name','file_path']]);

                    } else {
                        $dir = $file->file_path;
                        erLhcoreClassFileUpload::mkdirRecursive( $dir, true, $defaultUser, $defaultGroup);

                        file_put_contents($dir . $file->file_name, $fileBody);

                        chmod($dir . $file->file_name, 0644);

                        if ($defaultUser != '') {
                            chown($dir, $defaultUser);
                        }

                        if ($defaultGroup != '') {
                            chgrp($dir, $defaultGroup);
                        }
                    }

                    // Log error for investigation
                    if (!file_exists($file->file_path_server)) {
                        \erLhcoreClassLog::write(
                            "file could not be stored - ".$file->id,
                            \ezcLog::SUCCESS_AUDIT,
                            array(
                                'source' => 'mailconv',
                                'category' => 'mailconv',
                                'line' => __LINE__,
                                'file' => __FILE__,
                                'object_id' => $file->id
                            )
                        );
                    }

                    if (isset($fileData['mail_img_download_policy']) && $fileData['img_download_policy'] === 1 && in_array($file->extension,['jpg','jpeg','png','jfif'])) {
                        echo file_get_contents('design/defaulttheme/images/general/sensitive-information.png');
                    } else {
                        echo $fileBody;
                    }
                }
            }
        }
    }

} catch (Exception $e) {
    http_response_code(404);
    exit;
}
exit;

?>