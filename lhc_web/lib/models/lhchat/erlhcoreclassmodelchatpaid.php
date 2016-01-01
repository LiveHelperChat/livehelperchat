<?php

class erLhcoreClassModelChatPaid {

   use erLhcoreClassDBTrait;
    
   public static $dbTable = 'lh_chat_paid';
   
   public static $dbTableId = 'id';
   
   public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
   
   public static $dbSortOrder = 'DESC';
      
   public function getState()
   {
       return array(
               'id'              		=> $this->id,
               'chat_id'            	=> $this->chat_id,
               'hash'          		    => $this->hash
       );
   }

   public function __get($var) {

       switch ($var) {

       	case 'chat':
       			$this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
       			return $this->chat;
       		break;

       	default:
       		break;
       }
   }

   public $id = null;
   public $chat_id = '';
   public $hash = '';
      
   public $updateIgnoreColumns = array();
}

?>