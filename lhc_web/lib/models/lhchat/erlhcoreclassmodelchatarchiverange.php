<?php

class erLhcoreClassModelChatArchiveRange
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_chat_archive_range';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';

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
            'first_id' => $this->first_id
        );
    }

    public function removeThis()
    {

        // Set proper archive tables
        $this->setTables();

        // Drop archive tables
        $db = ezcDbInstance::get();
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveTable . "`");
        $db->query("DROP TABLE IF EXISTS `" . self::$archiveMsgTable . "`");

        erLhcoreClassChat::getSession()->delete($this);
    }

    public function process()
    {
        if ($this->range_to > 0 && $this->range_from > 0 && $this->older_than == 0) {
            $list = erLhcoreClassChat::getList(array('sort' => 'id ASC', 'limit' => 100, 'filterlt' => array('time' => $this->range_to), 'filtergt' => array('time' => $this->range_from)));
        } elseif ($this->older_than > 0) {
            $list = erLhcoreClassChat::getList(array('sort' => 'id ASC', 'limit' => 100, 'filterlt' => array('time' => time() - ($this->older_than * 24 *3600))));
        } else {
            throw new Exception('Could not determine archive logic!');
        }

        self::$archiveTable = "lh_chat_archive_{$this->id}";
        self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";

        $pending_archive = count($list);
        $messagesArchived = 0;
        $firstChatID = 0;
        $lastChatID = 0;

        foreach ($list as $item) {

            if ($firstChatID == 0) {
                $firstChatID = $item->id;
            }

            $archive = new erLhcoreClassModelChatArchive();
            $archive->setState(get_object_vars($item));
            $archive->saveThis();

            $messages = erLhcoreClassModelmsg::getList(array('limit' => 1000, 'filter' => array('chat_id' => $item->id)));
            $messagesArchived += count($messages);

            foreach ($messages as $msg) {
                $msgArchive = new erLhcoreClassModelChatArchiveMsg();
                $msgArchive->setState(get_object_vars($msg));
                $msgArchive->saveThis();
            }

            $lastChatID = $item->id;

            if ($lastChatID > $this->last_id) {
                $this->last_id = $lastChatID;
            }

            $q = ezcDbInstance::get()->createDeleteQuery();

            // Messages
            $q->deleteFrom( 'lh_msg' )->where( $q->expr->eq( 'chat_id', $item->id ) );
            $stmt = $q->prepare();
            $stmt->execute();

            // Transfered chats
            $q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'chat_id', $item->id ) );
            $stmt = $q->prepare();
            $stmt->execute();

            // Delete screen sharing
            $q->deleteFrom( 'lh_cobrowse' )->where( $q->expr->eq( 'chat_id', $item->id ) );
            $stmt = $q->prepare();
            $stmt->execute();

            // Dispatch event if chat is archived
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.archived',array('chat' => & $item));

            erLhcoreClassChat::getSession()->delete($item);

            $item->afterRemove();
        }

        $this->updateFirstId();

        return array('error' => 'false', 'fcid' => $firstChatID, 'lcid' => $lastChatID, 'messages_archived' => $messagesArchived, 'chats_archived' => count($list), 'pending_archive' => ($pending_archive == 100 ? 'true' : 'false'));
    }

    public function updateFirstId()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT min(id) FROM " . self::$archiveTable);
        $stmt->execute();

        $this->first_id = (int)$stmt->fetchColumn();
        $this->saveThis();
    }

    public function setTables()
    {
        erLhcoreClassModelChatArchive::$dbTable = self::$archiveTable = "lh_chat_archive_{$this->id}";
        erLhcoreClassModelChatArchiveMsg::$dbTable = self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";
    }

    public function __get($var)
    {
        switch ($var) {

            case 'range_from_edit':
                if ($this->range_from != 0) {
                    return date(erLhcoreClassModule::$dateFormat , $this->range_from);
                }
                return '';
                break;
            
            case 'range_from_front':
                if ($this->first_id > 0) {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT time FROM lh_chat_archive_" . $this->id . ' WHERE id = ' . $this->first_id);
                    $stmt->execute();
                    return date(erLhcoreClassModule::$dateDateHourFormat, (int)$stmt->fetchColumn()); 
                } elseif ($this->range_from != 0) {
                    return date(erLhcoreClassModule::$dateDateHourFormat , $this->range_from);
                }
                return '';
                break;

            case 'range_to_front':
                if ($this->last_id > 0) {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT time FROM lh_chat_archive_" . $this->id . ' WHERE id = ' . $this->last_id);
                    $stmt->execute();
                    return date(erLhcoreClassModule::$dateDateHourFormat, (int)$stmt->fetchColumn());
                } else if ($this->range_to != 0) {
                    return date(erLhcoreClassModule::$dateFormat, $this->range_to);
                }
                return '';
                break;
                
            case 'range_to_edit':
                if ($this->range_to != 0) {
                    return date(erLhcoreClassModule::$dateFormat, $this->range_to);
                }
                return '';
                break;
                
            case 'potential_chats_count':

                if ($this->range_to > 0 && $this->range_from > 0){
                    $this->potential_chats_count = erLhcoreClassChat::getCount(array('filterlt' => array('time' => $this->range_to), 'filtergt' => array('time' => $this->range_from)));
                } else {
                    $this->potential_chats_count = 0;
                }

                return $this->potential_chats_count;
                break;

            case 'chats_in_archive':

                $this->chats_in_archive = 0;

                if ($this->id > 0) {
                    self::$archiveTable = "lh_chat_archive_{$this->id}";
                    self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";
                    $this->chats_in_archive = erLhcoreClassChat::getCount(array(), self::$archiveTable);
                }

                return $this->chats_in_archive;
                break;

            case 'messages_in_archive':

                $this->messages_in_archive = 0;

                if ($this->id > 0) {
                    self::$archiveTable = "lh_chat_archive_{$this->id}";
                    self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";
                    $this->messages_in_archive = erLhcoreClassChat::getCount(array(), self::$archiveMsgTable);
                }

                return $this->messages_in_archive;
                break;


            default:
                break;
        }
    }

    public function createArchive()
    {

        $items = erLhcoreClassChat::getList(array('filter' => array('range_from' => $this->range_from, 'range_to' => $this->range_to)), 'erLhcoreClassModelChatArchiveRange', 'lh_chat_archive_range');

        if (empty($items)) {
            $this->saveThis();
        } else {
            $item = array_shift($items);
            $this->id = $item->id;
        }

        $db = ezcDbInstance::get();

        $stmt = $db->prepare("SHOW TABLES LIKE 'lh_chat_archive_{$this->id}'");
        $stmt->execute();
        $exists = $stmt->fetch();

        if ($exists === false) {

            // Create archive chat table
            $stmt = $db->prepare('SHOW CREATE TABLE `lh_chat`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lh_chat`", "`lh_chat_archive_{$this->id}`", $command);
            $db->query($command);

            // Create archive msg table
            $stmt = $db->prepare('SHOW CREATE TABLE `lh_msg`;');
            $stmt->execute();
            $rows = $stmt->fetch();
            $command = $rows[1];
            $command = preg_replace('/AUTO_INCREMENT\=[0-9]+/i', 'AUTO_INCREMENT=1', $command);
            $command = str_replace("`lh_msg`", "`lh_chat_archive_msg_{$this->id}`", $command);
            $db->query($command);
        }

        return $this->id;
    }

    public $id = null;
    public $range_from = 0;
    public $range_to = 0;
    public $year_month = 0;
    public $older_than = 0;
    public $last_id = 0;
    public $first_id = 0;

    public static $archiveTable;
    public static $archiveMsgTable;
}

?>