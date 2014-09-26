<?php

class erLhcoreClassGroup{
    
   
   function getGroupList()
   {
     $db = ezcDbInstance::get();
                 
     $stmt = $db->prepare('SELECT * FROM lh_group ORDER BY id ASC');           
     $stmt->execute();
     $rows = $stmt->fetchAll();
            
     return $rows;
   }
   
   public static function deleteGroup($group_id)
   {
       $Group = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelGroup', $group_id);
       erLhcoreClassUser::getSession()->delete($Group);
       
       $q = ezcDbInstance::get()->createDeleteQuery();
       
       // Transfered chats to user
       $q->deleteFrom( 'lh_groupuser' )->where( $q->expr->eq( 'group_id', $group_id ) );
       $stmt = $q->prepare();
       $stmt->execute();
       
       // Transfered chats to user
       $q->deleteFrom( 'lh_grouprole' )->where( $q->expr->eq( 'group_id', $group_id ) );
       $stmt = $q->prepare();
       $stmt->execute();
       
   }

}


?>