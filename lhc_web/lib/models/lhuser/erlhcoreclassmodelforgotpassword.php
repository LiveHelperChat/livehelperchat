<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelForgotPassword {

    public function getState()
   {
       return array(
               'id'         => $this->id,
               'user_id'    => $this->user_id,
               'hash'       => $this->hash,
               'created'    => $this->created
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

    public static function randomPassword($length = 10)
    {
        return erLhcoreClassChat::generateHash($length);
    }

	public static function setRemindHash($user_id, $hash) {

		$db = ezcDbInstance::get();
       	$stmt = $db->prepare('INSERT INTO lh_forgotpasswordhash ( user_id , hash , created ) VALUES ( :user_id, :hash, :created);');
       	$stmt->bindValue( ':user_id',$user_id);
       	$stmt->bindValue( ':hash',$hash);
        $stmt->bindValue( ':created',time());
        $stmt->execute();
	}

	public static function checkHash($hash) {

		$db = ezcDbInstance::get();
       	$stmt = $db->prepare('SELECT * FROM lh_forgotpasswordhash WHERE hash = :hash LIMIT 1');
       	$stmt->bindValue( ':hash',$hash);
       	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $row = $stmt->fetchAll();

        if ($row) {
        	return $row[0];
        }

        return false;
	}

	public static function deleteHash($user_id) {
		$db = ezcDbInstance::get();
       	$stmt = $db->prepare('DELETE FROM lh_forgotpasswordhash WHERE user_id =:user_id');
       	$stmt->bindValue( ':user_id',$user_id);
       	$stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
	}

    public $id = null;
    public $username = '';
    public $password = '';
    public $email = '';
    public $lastactivity = '';
}

?>