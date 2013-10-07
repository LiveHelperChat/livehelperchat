<?php

class erLhcoreClassModelChatArchive extends erLhcoreClassModelChat{

   public function removeThis()
   {
	   	$q = ezcDbInstance::get()->createDeleteQuery();

	   	// Messages
	   	$q->deleteFrom( erLhcoreClassModelChatArchiveRange::$archiveMsgTable )->where( $q->expr->eq( 'chat_id', $this->id ) );
	   	$stmt = $q->prepare();
	   	$stmt->execute();

	   	erLhcoreClassChat::getSession()->delete($this);
   }

   public static function fetch($chat_id) {
       	 $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatArchive', (int)$chat_id );
       	 return $chat;
   }

}

?>