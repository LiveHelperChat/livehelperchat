<?php
namespace LiveHelperChat\Models\mailConv\Archive;

#[\AllowDynamicProperties]
class Range
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_mail_archive_range';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'range_from' => $this->range_from,
            'range_to' => $this->range_to,
            'year_month' => $this->year_month,
            'older_than' => $this->older_than,
            'last_id' => $this->last_id,
            'first_id' => $this->first_id,
            'type' => $this->type,
            'name' => $this->name
        );
    }

    public function removeThis()
    {

        // Set proper archive tables
        $this->setTables();

        // Drop archive tables
        $db = \ezcDbInstance::get();
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveConversationTable . "`");
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveConversationMsgTable . "`");
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveConversationFileTable . "`");
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveConversationMsgSubjectTable . "`");
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveConversationMsgInternalTable . "`");

        \erLhcoreClassAbstract::getSession()->delete($this);
    }

    public function inArchive($itemId)
    {
        return \erLhcoreClassChat::getCount(['filter' => ['id' => $itemId]], "lhc_mailconv_conversation_archive_{$this->id}") > 0;
    }

    public function process($mailsToArchive = [], $executionParams = [])
    {
        if ($this->type == self::ARCHIVE_TYPE_DEFAULT) {
            if ($this->range_to > 0 && $this->range_from > 0 && $this->older_than == 0) {
                $list = \erLhcoreClassModelMailconvConversation::getList(array('sort' => 'id ASC', 'limit' => 50, 'filterlt' => array('ctime' => $this->range_to), 'filtergt' => array('ctime' => $this->range_from)));
            } elseif ($this->older_than > 0) {
                $list = \erLhcoreClassModelMailconvConversation::getList(array('sort' => 'id ASC', 'limit' => 50, 'filterlt' => array('ctime' => time() - ($this->older_than * 24 *3600))));
            } else {
                throw new \Exception('Could not determine archive logic!');
            }
        } else {
            $list = $mailsToArchive;
        }

        self::$archiveConversationTable = "lhc_mailconv_conversation_archive_{$this->id}";
        self::$archiveConversationMsgTable = "lhc_mailconv_msg_archive_{$this->id}";
        self::$archiveConversationFileTable = "lhc_mailconv_file_archive_{$this->id}";
        self::$archiveConversationMsgSubjectTable = "lhc_mailconv_msg_subject_archive_{$this->id}";
        self::$archiveConversationMsgInternalTable = "lhc_mailconv_msg_internal_archive_{$this->id}";

        $pending_archive = count($list);
        $messagesArchived = 0;
        $firstChatID = 0;
        $lastChatID = 0;

        $db = \ezcDbInstance::get();

        foreach ($list as $item) {
            try {

                $db->beginTransaction();

                if ($firstChatID == 0) {
                    $firstChatID = $item->id;
                }

                $archive = new Conversation();
                $archive->setState(get_object_vars($item));
                $archive->saveThis();

                $messages = \erLhcoreClassModelMailconvMessage::getList(array('limit' => 1000, 'filter' => array('conversation_id' => $item->id)));
                $messagesArchived += count($messages);

                foreach ($messages as $msg) {
                    $msgArchive = new Message();
                    $msgArchive->setState(get_object_vars($msg));
                    $msgArchive->saveThis();

                    if (!(isset($executionParams['ignore_imap']) && $executionParams['ignore_imap'] === true)) {
                        \erLhcoreClassMailconvParser::purgeMessage($msg, true);
                    }
                }

                $lastChatID = $item->id;

                if ($lastChatID > $this->last_id) {
                    $this->last_id = $lastChatID;
                }

                if (!empty($messages)) {
                    // Files
                    $files = \erLhcoreClassModelMailconvFile::getList(array('limit' => 1000, 'filterin' => array('message_id' => array_keys($messages))));
                    foreach ($files as $file) {
                        $fileArchive = new File();
                        $fileArchive->setState(get_object_vars($file));
                        $fileArchive->saveThis();
                    }
                }

                // Messages Subjects
                $msgSubjects = \erLhcoreClassModelMailconvMessageSubject::getList(array('limit' => 1000, 'filter' => array('conversation_id' => $item->id)));
                foreach ($msgSubjects as $msgSubject) {
                    $msgSubjectArchive = new MessageSubject();
                    $msgSubjectArchive->setState(get_object_vars($msgSubject));
                    $msgSubjectArchive->saveThis();
                }

                // Messages Internal
                $msgInternals = \erLhcoreClassModelMailconvMessageInternal::getList(array('limit' => 1000, 'filter' => array('chat_id' => $item->id)));
                foreach ($msgInternals as $msgInternal) {
                    $msgInternalArchive = new MessageInternal();
                    $msgInternalArchive->setState(get_object_vars($msgInternal));
                    $msgInternalArchive->saveThis();
                }


                // Messages
                $q = $db->createDeleteQuery();
                $q->deleteFrom('lhc_mailconv_msg')->where($q->expr->eq('conversation_id', $item->id));
                $stmt = $q->prepare();
                $stmt->execute();

                // Files
                foreach ($messages as $message) {
                    $q = $db->createDeleteQuery();
                    $q->deleteFrom('lhc_mailconv_file')->where($q->expr->eq('message_id', $message->id));
                    $stmt = $q->prepare();
                    $stmt->execute();

                    // If it's backup archive dispatch event for messages being removed
                    // This way elasticSEarch will remove relevant record also
                    if ($this->type == self::ARCHIVE_TYPE_BACKUP) {
                        $message->afterRemove();
                    }
                }

                // Messages Subjects
                $q = $db->createDeleteQuery();
                $q->deleteFrom('lhc_mailconv_msg_subject')->where($q->expr->eq('conversation_id', $item->id));
                $stmt = $q->prepare();
                $stmt->execute();

                // Messages Internal
                $q = $db->createDeleteQuery();
                $q->deleteFrom('lhc_mailconv_msg_internal')->where($q->expr->eq('chat_id', $item->id));
                $stmt = $q->prepare();
                $stmt->execute();

                // Transfer table
                $q = $db->createDeleteQuery();
                $q->deleteFrom('lh_transfer')->where($q->expr->eq('chat_id', $item->id), $q->expr->eq('transfer_scope', 1));
                $stmt = $q->prepare();
                $stmt->execute();

                // Dispatch event if chat is archived
                \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.archived', array('mail' => & $item, 'archive' => $this));

                \erLhcoreClassMailconv::getSession()->delete($item);

                $item->afterRemove();

                $db->commit();

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        $this->updateFirstId();

        return array('error' => 'false', 'fcid' => $firstChatID, 'lcid' => $lastChatID, 'messages_archived' => $messagesArchived, 'mails_archived' => count($list), 'pending_archive' => ($pending_archive == 100 ? 'true' : 'false'));
    }

    public function updateFirstId()
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare("SELECT min(id) FROM " . self::$archiveConversationTable);
        $stmt->execute();

        $this->first_id = (int)$stmt->fetchColumn();
        $this->saveThis();
    }

    public function setTables()
    {
        Conversation::$dbTable = self::$archiveConversationTable = "lhc_mailconv_conversation_archive_{$this->id}";
        Message::$dbTable = self::$archiveConversationMsgTable = "lhc_mailconv_msg_archive_{$this->id}";
        File::$dbTable = self::$archiveConversationFileTable = "lhc_mailconv_file_archive_{$this->id}";
        MessageSubject::$dbTable = self::$archiveConversationMsgSubjectTable = "lhc_mailconv_msg_subject_archive_{$this->id}";
        MessageInternal::$dbTable = self::$archiveConversationMsgInternalTable = "lhc_mailconv_msg_internal_archive_{$this->id}";

        \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.set_archive_tables', array('archive' => & $this));
    }

    public function __get($var)
    {
        switch ($var) {

            case 'range_from_edit':
                if ($this->range_from != 0) {
                    return date(\erLhcoreClassModule::$dateFormat , $this->range_from);
                }
                return '';
                break;

            case 'range_from_front':
                if ($this->first_id > 0) {
                    $db = \ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT `ctime` FROM lhc_mailconv_conversation_archive_" . $this->id . ' WHERE id = ' . $this->first_id);
                    $stmt->execute();
                    return date(\erLhcoreClassModule::$dateDateHourFormat, (int)$stmt->fetchColumn());
                } elseif ($this->range_from != 0) {
                    return date(\erLhcoreClassModule::$dateDateHourFormat , $this->range_from);
                }
                return '';
                break;

            case 'range_to_front':
                if ($this->last_id > 0) {
                    $db = \ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT `ctime` FROM lhc_mailconv_conversation_archive_" . $this->id . ' WHERE id = ' . $this->last_id);
                    $stmt->execute();
                    return date(\erLhcoreClassModule::$dateDateHourFormat, (int)$stmt->fetchColumn());
                } else if ($this->range_to != 0) {
                    return date(\erLhcoreClassModule::$dateFormat, $this->range_to);
                }
                return '';


            case 'range_to_edit':
                if ($this->range_to != 0) {
                    return date(\erLhcoreClassModule::$dateFormat, $this->range_to);
                }
                return '';


            case 'potential_mails_count':

                if ($this->range_to > 0 && $this->range_from > 0) {
                    $this->potential_chats_count = \erLhcoreClassModelMailconvConversation::getCount(array('filterlt' => array('ctime' => $this->range_to), 'filtergt' => array('ctime' => $this->range_from)));
                } else {
                    $this->potential_chats_count = 0;
                }

                return $this->potential_chats_count;


            case 'mails_in_archive':

                $this->mails_in_archive = 0;

                if ($this->id > 0) {
                    self::$archiveConversationTable = "lhc_mailconv_conversation_archive_{$this->id}";
                    self::$archiveConversationMsgTable = "lhc_mailconv_msg_archive_{$this->id}";

                    $this->mails_in_archive = \erLhcoreClassChat::getCount(array(), self::$archiveConversationTable);
                }

                return $this->mails_in_archive;

            case 'messages_in_archive':

                $this->messages_in_archive = 0;

                if ($this->id > 0) {

                    self::$archiveConversationTable = "lhc_mailconv_conversation_archive_{$this->id}";
                    self::$archiveConversationMsgTable = "lhc_mailconv_msg_archive_{$this->id}";
                    self::$archiveConversationFileTable = "lhc_mailconv_file_archive_{$this->id}";
                    self::$archiveConversationMsgSubjectTable = "lhc_mailconv_msg_subject_archive_{$this->id}";
                    self::$archiveConversationMsgInternalTable = "lhc_mailconv_msg_internal_archive_{$this->id}";

                    $this->messages_in_archive = \erLhcoreClassChat::getCount(array(), self::$archiveConversationMsgTable);
                }

                return $this->messages_in_archive;


            default:
                break;
        }
    }

    public function createArchive()
    {
        if ($this->type == self::ARCHIVE_TYPE_DEFAULT)
        {
            $items = Range::getList(array('filter' => array('type' => self::ARCHIVE_TYPE_DEFAULT, 'range_from' => $this->range_from, 'range_to' => $this->range_to)));

            if (empty($items)) {
                $this->saveThis();
            } else {
                $item = array_shift($items);
                $this->id = $item->id;
            }
        }

        $db = \ezcDbInstance::get();

        $stmt = $db->prepare("SHOW TABLES LIKE 'lhc_mailconv_conversation_archive_{$this->id}'");
        $stmt->execute();
        $exists = $stmt->fetch();

        if ($exists === false) {
            // Create archive chat table
            $stmt = $db->prepare('SHOW CREATE TABLE `lhc_mailconv_conversation`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lhc_mailconv_conversation`", "`lhc_mailconv_conversation_archive_{$this->id}`", $command);
            $command = str_replace("ROW_FORMAT=COMPACT", "", $command);
            $db->query($command);

            // Conversation messages table
            $stmt = $db->prepare('SHOW CREATE TABLE `lhc_mailconv_msg`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lhc_mailconv_msg`", "`lhc_mailconv_msg_archive_{$this->id}`", $command);
            $command = str_replace("ROW_FORMAT=COMPACT", "", $command);
            $db->query($command);

            // Files table
            $stmt = $db->prepare('SHOW CREATE TABLE `lhc_mailconv_file`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lhc_mailconv_file`", "`lhc_mailconv_file_archive_{$this->id}`", $command);
            $command = str_replace("ROW_FORMAT=COMPACT", "", $command);
            $db->query($command);

            // Messages Subject
            $stmt = $db->prepare('SHOW CREATE TABLE `lhc_mailconv_msg_subject`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lhc_mailconv_msg_subject`", "`lhc_mailconv_msg_subject_archive_{$this->id}`", $command);
            $command = str_replace("ROW_FORMAT=COMPACT", "", $command);
            $db->query($command);

            // Messages Internal
            $stmt = $db->prepare('SHOW CREATE TABLE `lhc_mailconv_msg_internal`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lhc_mailconv_msg_internal`", "`lhc_mailconv_msg_internal_archive_{$this->id}`", $command);
            $command = str_replace("ROW_FORMAT=COMPACT", "", $command);
            $db->query($command);
        }

        \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mail.create_archive', array('archive' => & $this));

        return $this->id;
    }

    const ARCHIVE_TYPE_DEFAULT = 0;
    const ARCHIVE_TYPE_BACKUP = 1;

    public $id = null;
    public $range_from = 0;
    public $range_to = 0;
    public $year_month = 0;
    public $older_than = 0;
    public $last_id = 0;
    public $first_id = 0;
    public $type = self::ARCHIVE_TYPE_DEFAULT;
    public $name = '';

    public static $archiveConversationTable;
    public static $archiveConversationMsgTable;
    public static $archiveConversationFileTable;
    public static $archiveConversationMsgSubjectTable;
    public static $archiveConversationMsgInternalTable;

}

?>