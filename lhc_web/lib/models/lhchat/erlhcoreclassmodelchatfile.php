<?php

class erLhcoreClassModelChatFile {

   public function getState()
   {
       return array(
               'id'         	=> $this->id,
               'name'        	=> $this->name,
               'upload_name'    => $this->upload_name,
               'size'    		=> $this->size,
               'type'    		=> $this->type,
               'file_path'   	=> $this->file_path,
               'extension'   	=> $this->extension,
               'chat_id'   		=> $this->chat_id,
               'user_id'   		=> $this->user_id,
               'online_user_id' => $this->online_user_id,
               'date'   		=> $this->date,
              );
   }

   public static function fetch($file_id) {
   		$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatFile', (int)$file_id );
   		return $chat;
   }

   public function setState( array $properties )
   {
   		foreach ( $properties as $key => $val )
	   	{
	   		$this->$key = $val;
	   	}
   }

   public static function deleteByChatId($chat_id) {
   		foreach (erLhcoreClassChat::getList(array('filter' => array('chat_id' => $chat_id)),'erLhcoreClassModelChatFile','lh_chat_file') as $file) {
   			$file->removeThis();
   		}
   }

   public function removeThis()
   {
	   	if (file_exists($this->file_path_server)){
	   		unlink($this->file_path_server);
	   	}

	   	if ($this->file_path != '') {
	   		erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/', str_replace('var/', '', $this->file_path));
	   	}
	   	
	   	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.remove_file', array('chat_file' => & $this));
	   	
	   	erLhcoreClassChat::getSession()->delete($this);
   }


   public function __get($var){

   		switch ($var) {
   			case 'file_path_server':
   					$this->file_path_server = $this->file_path . $this->name;
   				return $this->file_path_server;
   			;
   			break;

   			case 'security_hash':
   					$this->security_hash = md5($this->name.'_'.$this->chat_id);
   				return $this->security_hash;
   			;
   			break;

   			case 'chat':
   					$this->chat = false;
   					if ($this->chat_id > 0){
   						try {
   							$this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
   						} catch (Exception $e) {
   							$this->chat = new erLhcoreClassModelChat();
   						}
   					}
   				return $this->chat;
   			;
   			break;

   			case 'user':
   					$this->user = false;
   					if ($this->user_id > 0 ){
   						try {
   							$this->user = erLhcoreClassModelUser::fetch($this->user_id);
   						} catch (Exception $e) {
   							$this->user = false;
   						}
   					}
   					return $this->user;
   				break;

   			case 'date_front':
   					$this->date_front = date(erLhcoreClassModule::$dateDateHourFormat,$this->date);
   					return $this->date_front;
   				break;


   			default:
   				;
   			break;
   		}
   }

   public function saveThis() {
   		erLhcoreClassChat::getSession()->saveOrUpdate($this);
   }

   public $id = null;
   public $name = null;
   public $upload_name = null;
   public $type = null;
   public $file_path = null;
   public $size = null;
   public $extension = null;
   public $date = 0;
   public $user_id = 0;
   public $chat_id = 0;
   public $online_user_id = 0;
}

?>