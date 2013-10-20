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
               'chat_id'   		=> $this->chat_id,
              );
   }

   public function setState( array $properties )
   {
   		foreach ( $properties as $key => $val )
	   	{
	   		$this->$key = $val;
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
   public $chat_id = null;
}

?>