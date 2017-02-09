<?php

class erLhcoreClassTransfer
{



	public static function getDepartmentLimitation(){
		$currentUser = erLhcoreClassUser::instance();
		$LimitationDepartament = '';
		$userData = $currentUser->getUserData(true);
		if ( $userData->all_departments == 0 )
		{
			$userDepartaments = erLhcoreClassUserDep::getUserDepartaments($currentUser->getUserID());

			if (count($userDepartaments) == 0) return false;

			$LimitationDepartament = '(lh_transfer.dep_id IN ('.implode(',',$userDepartaments).'))';

			return $LimitationDepartament;
		}

		return true;
	}

    public static function getTransferChats($params = array())
    {
       $db = ezcDbInstance::get();
       $currentUser = erLhcoreClassUser::instance();
       $limitationSQL = '';

       if (isset($params['department_transfers']) && $params['department_transfers'] == true) {

	       	$limitation = self::getDepartmentLimitation();

	       	// Does not have any assigned department
	       	if ($limitation === false) {
	       		return array();
	       	}

	       	if ($limitation !== true) {
	       		$limitationSQL = ' AND '.$limitation;
	       	}

	       	$stmt = $db->prepare('SELECT lh_chat.*,lh_transfer.id as transfer_id FROM lh_chat INNER JOIN lh_transfer ON lh_transfer.chat_id = lh_chat.id WHERE transfer_user_id != :transfer_user_id '.$limitationSQL.' ORDER BY lh_transfer.id DESC LIMIT 10');
	       	$stmt->bindValue( ':transfer_user_id',$currentUser->getUserID());
	       	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	       	$stmt->execute();
	       	$rows = $stmt->fetchAll();
       } else {
	       	$stmt = $db->prepare('SELECT lh_chat.*,lh_transfer.id as transfer_id FROM lh_chat INNER JOIN lh_transfer ON lh_transfer.chat_id = lh_chat.id WHERE lh_transfer.transfer_to_user_id = :user_id ORDER BY lh_transfer.id DESC LIMIT 10');
	       	$stmt->bindValue( ':user_id',$currentUser->getUserID());
	       	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	       	$stmt->execute();
	       	$rows = $stmt->fetchAll();
       }

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

       return (isset($rows[0])) ? $rows[0] : false;
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