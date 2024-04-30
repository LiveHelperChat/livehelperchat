<?php
namespace LiveHelperChat\mailConv\workers;
#[\AllowDynamicProperties]
class SentCopyWorker
{
    public function perform()
    {
        $db = \ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        $messageId = $this->args['copy_id'];
        $db->beginTransaction();

        $message = \LiveHelperChat\Models\mailConv\SentCopy::fetchAndLock($messageId);
        $message->status = 1;
        $message->updateThis(['update' => ['status']]);

        $db->commit();

        if (!($message instanceof \LiveHelperChat\Models\mailConv\SentCopy)) {
            return null;
        }

        $mailbox = \erLhcoreClassModelMailconvMailbox::fetch($message->mailbox_id);

        $path = null;

        foreach ($mailbox->mailbox_sync_array as $syncArray) {
            if (isset($syncArray['send_folder']) && $syncArray['send_folder'] === true) {
                $path = $syncArray['path'];
                break;
            }
        }

        if ($path === null) {
            return null;
        }

        echo "asdasd";
        exit;

        // Append to IMAP server
        if ($mailbox->auth_method == \erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
            $mailboxHandler = \LiveHelperChat\mailConv\OAuth\OAuth::getClient($mailbox);
            $mailboxFolderOAuth = $mailboxHandler->getFolderByPath($path);
            $mailboxFolderOAuth->appendMessage($message->body);
        } else {
            \imap_errors();

            // Create a copy in send folder
            $imapStream = imap_open($path, $mailbox->username, $mailbox->password);

            // Retry
            if ($imapStream === false) {
                sleep(1);
                $imapStream = imap_open($path, $mailbox->username, $mailbox->password);
            }

            if ($imapStream !== false) {
                $result = imap_append($imapStream, $path, $message->body);
                imap_close($imapStream);
            } else {
                $result = false;
            }

            if ($result !== true) {
                // @todo log error - return ['success' => false, 'reason' => implode("\n",imap_errors())];
            }
        }

    }

    public static $lastCallDebug = array();
    public static $apiTimeout = 10;
}
