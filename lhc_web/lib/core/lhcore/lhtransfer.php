<?php

class erLhcoreClassTransfer
{

    public static function getTransferChats()
    {
       $db = ezcDbInstance::get();

       $currentUser = erLhcoreClassUser::instance();

       $stmt = $db->prepare('SELECT lh_chat.*,lh_transfer.id as transfer_id FROM lh_chat INNER JOIN lh_transfer ON lh_transfer.chat_id = lh_chat.id WHERE lh_transfer.user_id = :user_id');
       $stmt->bindValue( ':user_id',$currentUser->getUserID());
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows;
   }


   public static function getTransferByChat($chat_id)
   {
       $db = ezcDbInstance::get();

       $stmt = $db->prepare('SELECT * FROM lh_transfer WHERE lh_transfer.chat_id = :chat_id');
       $stmt->bindValue( ':chat_id',$chat_id);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows[0];
   }

   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhtransfer' )
            );
        }
        return self::$persistentSession;
   }

   private static $persistentSession;
}


?>