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
   public $chat_id = null;
}

?>