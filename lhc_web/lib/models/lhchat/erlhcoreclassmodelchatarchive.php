<?php

class erLhcoreClassModelChatArchive extends erLhcoreClassModelChat {

   /*
    * Method override to delete proper messages from archive table
    * */
   public function removeThis()
   {
	   	$q = ezcDbInstance::get()->createDeleteQuery();

	   	// Messages
	   	$q->deleteFrom( erLhcoreClassModelChatArchiveRange::$archiveMsgTable )->where( $q->expr->eq( 'chat_id', $this->id ) );
	   	$stmt = $q->prepare();
	   	$stmt->execute();

	   	erLhcoreClassChat::getSession()->delete($this);
   }

   /**
    * Method override to delete proper archive messages
    * */
   public static function fetch($chat_id, $useCache = false) {
       	 $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatArchive', (int)$chat_id );
       	 return $chat;
   }

}

?>