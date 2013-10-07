<?php

class erLhcoreClassModelChatArchiveRange {

   public function getState()
   {
       return array(
               'id'            => $this->id,
               'range_from'    => $this->range_from,
               'range_to'      => $this->range_to
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public static function fetch($chat_id) {
       	 $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatArchiveRange', (int)$chat_id );
       	 return $chat;
   }

   public function removeThis() {

   	   // Set proper archive tables
   	   $this->setTables();

   	   // Drop archive tables
   	   $db = ezcDbInstance::get();
   	   $db->query("DROP TABLE IF EXISTS `". self::$archiveTable . "`");
   	   $db->query("DROP TABLE IF EXISTS `". self::$archiveMsgTable . "`");

       erLhcoreClassChat::getSession()->delete($this);
   }

   public function setTables(){
   		self::$archiveTable = "lh_chat_archive_{$this->id}";
   		self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";
   }

   public function process() {

   		$list = erLhcoreClassChat::getList(array('limit' => 100, 'filterlt' => array('time' => $this->range_to),'filtergt' => array('time' => $this->range_from)));

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
   			$archive->id = null;
   			$archive->saveThis();

   			$messages = erLhcoreClassModelmsg::getList(array('limit' => 1000, 'filter' => array('chat_id' => $item->id)));
   			$messagesArchived += count($messages);

   			foreach ($messages as $msg) {
   				$msgArchive = new erLhcoreClassModelChatArchiveMsg();
   				$msgArchive->setState(get_object_vars($msg));
   				$msgArchive->id = null;
   				$msgArchive->chat_id = $archive->id;
   				erLhcoreClassChat::getSession()->save($msgArchive);
   			}

   			$lastChatID = $item->id;

   			$item->removeThis();
   		}

   		return array('error' => 'false','fcid' => $firstChatID, 'lcid' => $lastChatID, 'messages_archived' => $messagesArchived, 'chats_archived' => count($list), 'pending_archive' => ($pending_archive == 100 ? 'true' : 'false'));
   }

   public function __get($var) {
       switch ($var) {

       	case 'range_from_front':
       		  if ($this->range_from != 0){
       		  		return date('Y-m-d',$this->range_from);
       		  }
       		  return '';
       		break;

       	case 'range_to_front':
       		  if ($this->range_to != 0){
       		  		return date('Y-m-d',$this->range_to);
       		  }
       		  return '';
       		break;

       	case 'potential_chats_count':
       			$this->potential_chats_count = erLhcoreClassChat::getCount(array('filterlt' =>  array('time' => $this->range_to),'filtergt' => array('time' => $this->range_from)));
       			return $this->potential_chats_count;
       		break;

       	case 'chats_in_archive':
       			self::$archiveTable = "lh_chat_archive_{$this->id}";
       			self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";
       			$this->chats_in_archive = erLhcoreClassChat::getCount(array(),self::$archiveTable);

       			return $this->chats_in_archive;
       		break;

       	case 'messages_in_archive':
       			self::$archiveTable = "lh_chat_archive_{$this->id}";
       			self::$archiveMsgTable = "lh_chat_archive_msg_{$this->id}";

       			$this->messages_in_archive = erLhcoreClassChat::getCount(array(),self::$archiveMsgTable);
       			return $this->messages_in_archive;
       		break;


       	default:
       		break;
       }
   }

   public function saveThis() {
       erLhcoreClassChat::getSession()->saveOrUpdate( $this );
   }

   public function createArchive(){

   		$items = erLhcoreClassChat::getList(array('filter' => array('range_from' => $this->range_from,'range_to' => $this->range_to)),'erLhcoreClassModelChatArchiveRange','lh_chat_archive_range');

   		if (empty($items)){
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
   public $ip = '';
   public $range_from = 0;
   public $range_to = 0;

   public static $archiveTable;
   public static $archiveMsgTable;
}

?>