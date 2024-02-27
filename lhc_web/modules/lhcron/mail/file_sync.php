<?php

/*
 * php cron.php -s site_admin -c cron/mail/file_sync -p <last_file_id>
 *
 * If for some reason files were not fetched you can force sync
 * */

if (is_numeric($cronjobPathOption->value)) {
    $lastId = (int)$cronjobPathOption->value;
} else {
    $lastId = 0;
}

echo "Checking files\n";

$pageLimit = 500;

for ($i = 0; $i < 1000000; $i++) {

    echo "[[Saving files pages - ",($i + 1),"]]\n";

    $files = erLhcoreClassModelMailconvFile::getList(array('offset' => 0, 'filtergt' => array('id' => $lastId), 'limit' => $pageLimit, 'sort' => 'id ASC'));

    if (!empty($files))
    {
        end($files);
        $lastChat = current($files);

        $lastId = $lastChat->id;

        echo "INDEX - ",$lastId,'-',count($files),"\n";

        if (empty($files)) {
            exit;
        }

        foreach ($files as $file) {

            if (file_exists($file->file_path_server)) {
                echo "File exists - {$file->file_path_server} | ",$file->id,"\n";
                continue;
            } else {
                echo "File missing - {$file->file_path_server} | ",$file->id,"\n";
            }

            $mail = erLhcoreClassModelMailconvMessage::fetch($file->message_id);

            if (!($mail instanceof erLhcoreClassModelMailconvMessage)){
                echo "Missing message - ",$file->message_id,"\n";
                continue;
            }

            $mailbox = $mail->mailbox;

            $mailboxHandler = new PhpImap\Mailbox(
                $mailbox->imap, // IMAP server incl. flags and optional mailbox folder
                $mailbox->username, // Username for the before configured mailbox
                $mailbox->password, // Password for the before configured username
                false
            );

            try {
                $mail = $mailboxHandler->getMail($mail->uid, false);
            } catch (Exception $e) {
                echo "Could not fetch a mail message - ",$mail->uid,"\n";
                echo $e->getMessage(),"\n";
                continue;
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
                        $fileBody = $attachment->getContents();

                        // Try to save file again
                        $cfg = erConfigClassLhConfig::getInstance();

                        $defaultGroup = $cfg->getSetting( 'site', 'default_group', false );
                        $defaultUser = $cfg->getSetting( 'site', 'default_user', false );

                        $dir = $file->file_path;
                        erLhcoreClassFileUpload::mkdirRecursive( $dir, true, $defaultUser, $defaultGroup);

                        if (!file_exists($dir)) {
                            die('Could not create a folder - ' . $dir);
                        }

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
                            die('Could not store a file - ' . $file->file_path_server);
                        }
                    }
                }
            }
        }
        
    } else {
        echo "No files to sync!\n";
        exit;
    }
}

?>
