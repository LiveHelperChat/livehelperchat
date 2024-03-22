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

    if (isset($fileData['mail_file_policy']) && $fileData['mail_file_policy'] === 1) {
        if ($file->is_archive === false) {
            $mail = erLhcoreClassModelMailconvMessage::fetch($file->message_id);
        } else {
            $mail = \LiveHelperChat\Models\mailConv\Archive\Message::fetch($file->message_id);
        }

        $conv = $mail->conversation;

        if (!($conv instanceof erLhcoreClassModelMailconvConversation) || !erLhcoreClassChat::hasAccessToRead($conv)) {
            $validRequest = false;
        }
    }

    if ($validRequest === false) {
        if (in_array($file->extension,['jpg','jpeg','png'])) {
            header('Content-type: image/png; charset=binary');
            echo file_get_contents('design/defaulttheme/images/general/denied.png');
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

        $mailboxHandler = new PhpImap\Mailbox(
            $mailbox->imap, // IMAP server incl. flags and optional mailbox folder
            $mailbox->username, // Username for the before configured mailbox
            $mailbox->password, // Password for the before configured username
            false
        );

        $mail = $mailboxHandler->getMail($mail->uid, false);

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
                    $fileBody = $attachment->getContents();

                    // Try to save file again
                    $cfg = erConfigClassLhConfig::getInstance();

                    $defaultGroup = $cfg->getSetting( 'site', 'default_group', false );
                    $defaultUser = $cfg->getSetting( 'site', 'default_user', false );

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

                    echo $fileBody;
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