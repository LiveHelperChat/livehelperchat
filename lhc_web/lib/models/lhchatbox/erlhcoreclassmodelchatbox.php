<?php

class erLhcoreClassModelChatbox {

   public function getState()
   {
       return array(
               'id'              	=> $this->id,
               'identifier'         => $this->identifier,
               'name'          		=> $this->name,
               'active'          	=> $this->active,
               'chat_id'            => $this->chat_id
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
       	 $chat = erLhcoreClassChatbox::getSession()->load( 'erLhcoreClassModelChatbox', (int)$chat_id );
       	 return $chat;
   }

   public function saveThis() {
       	 erLhcoreClassChatbox::getSession()->saveOrUpdate($this);
   }

   public function updateThis() {
       	erLhcoreClassChatbox::getSession()->update($this);
   }

   public function removeThis() {
   		$this->chat->removeThis();
   		erLhcoreClassChatbox::getSession()->delete($this);
   }

   
   
   public function __get($var) {

       switch ($var) {

       	case 'chat':
       			$this->chat = false;
       			if ($this->chat_id > 0) {
       				try {
       					$this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
       				} catch (Exception $e) {
       					erLhcoreClassChatbox::getSession()->delete($this);
       				}
       			} else {
       				$this->chat = new erLhcoreClassModelChat();
       				$this->chat->hash = erLhcoreClassChat::generateHash();
       			}
       			return $this->chat;
       		break;

       	default:
       		break;
       }

   }

   public $id = null;
   public $identifier = '';
   public $name = '';
   public $chat_id = 0;
   public $active = 1;

}

?>