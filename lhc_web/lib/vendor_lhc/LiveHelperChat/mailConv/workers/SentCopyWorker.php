<?php
namespace LiveHelperChat\mailConv\workers;
#[\AllowDynamicProperties]
class SentCopyWorker
{
    public function perform()
    {
        $db = \ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if (isset($this->args['inst_id']) && $this->args['inst_id'] > 0) {
            $cfg = \erConfigClassLhConfig::getInstance();
            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $this->args['inst_id']);
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT id FROM lhc_mailconv_sent_copy WHERE status = 0 LIMIT :limit FOR UPDATE ');
            $stmt->bindValue(':limit',10,\PDO::PARAM_INT);
            $stmt->execute();
            $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            // Someone is already processing. So we just ignore and retry later
            return;
        }

        if (empty($ids)) {
            return;
        }

        $stmt = $db->prepare('UPDATE lhc_mailconv_sent_copy SET status = 1 WHERE id IN (' . implode(',', $ids) . ')');
        $stmt->execute();
        $db->commit();

        $messages = \LiveHelperChat\Models\mailConv\SentCopy::getList(['filterin' => ['id' => $ids]]);

        foreach ($messages as $message) {
            if (self::sentCopy($message)) {
                $stmt = $db->prepare('DELETE FROM lhc_mailconv_sent_copy WHERE id IN (' . $message->id . ')');
                $stmt->execute();
            }
        }

        if ((count($ids) >= 10) && \erLhcoreClassRedis::instance()->llen('resque:queue:lhc_imap_copy') <= 4) {
            $inst_id = class_exists('\erLhcoreClassInstance') ? \erLhcoreClassInstance::$instanceChat->id : 0;
            \erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_imap_copy', '\LiveHelperChat\mailConv\workers\SentCopyWorker', array('inst_id' => $inst_id));
        }
    }

    public static function sentCopy($message)
    {
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

        // Append to IMAP server
        if ($mailbox->auth_method == \erLhcoreClassModelMailconvMailbox::AUTH_OAUTH2) {
            $mailboxHandler = \LiveHelperChat\mailConv\OAuth\OAuth::getClient($mailbox);
            $mailboxFolderOAuth = $mailboxHandler->getFolderByPath($path);
            $mailboxFolderOAuth->appendMessage($message->body);
        } else {
            \imap_errors();

            // Create a copy in send folder
            $imapStream = \imap_open($path, $mailbox->username, $mailbox->password);

            // Retry
            if ($imapStream === false) {
                sleep(1);
                $imapStream = \imap_open($path, $mailbox->username, $mailbox->password);
            }

            if ($imapStream !== false) {
                $result = \imap_append($imapStream, $path, $message->body);
                \imap_close($imapStream);
            } else {
                $result = false;
            }

            if ($result !== true) {
                \erLhcoreClassLog::write(implode("\n",\imap_errors()),
                    \ezcLog::SUCCESS_AUDIT,
                    array(
                        'source' => 'lhc',
                        'category' => 'resque_fatal',
                        'line' => 0,
                        'file' => 0,
                        'object_id' => $message->id
                    )
                );
                return false;
            }
        }

        return true;
    }
}
