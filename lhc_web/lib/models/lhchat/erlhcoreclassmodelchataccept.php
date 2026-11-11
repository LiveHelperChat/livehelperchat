<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelChatAccept {

   public function getState()
   {
       return array(
               'id'         => $this->id,
               'chat_id'    => $this->chat_id,
               'hash'   	=> $this->hash,
               'ctime'   	=> $this->ctime,
               'wused'   	=> $this->wused,
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
   		$this->ctime = time();
   		erLhcoreClassChat::getSession()->saveOrUpdate($this);
   }
   
   public function removeThis() {
       erLhcoreClassChat::getSession()->delete( $this );
   }

   public static function fetchByHash($hash) {
   		$list = erLhcoreClassChat::getList(array('limit' => 1, 'filter' => array('hash' => $hash)),'erLhcoreClassModelChatAccept','lh_chat_accept');
   		if (!empty($list)) {
   			return array_pop($list);
   		} else {
   			return false;
   		}
   }
   
   public static function cleanup() {
	   	$db = ezcDbInstance::get();
	   	$stmt = $db->prepare('DELETE FROM lh_chat_accept WHERE ctime < :ctime');
	   	$stmt->bindValue(':ctime',(int)(time()-24*3600),PDO::PARAM_INT);
	   	$stmt->execute();   	
   }
   
   public static function generateAcceptLink(erLhcoreClassModelChat $chat) {   		
   		$accept = new self();
   		$accept->hash = erLhcoreClassModelForgotPassword::randomPassword(40);
   		$accept->chat_id = $chat->id;
   		$accept->saveThis();
   		return $accept->hash;
   }

   /**
    * Email verification hash used in chat accept links.
    * Always generate it through this method so accept link and validation stay in sync.
    */
   public static function generateEmailVerificationHash($email, $secretHash) {
   		return sha1(sha1((string)$email . (string)$secretHash) . (string)$secretHash);
   }

   /**
    * Timing safe validation of email verification hash.
    */
   public static function validateEmailVerificationHash($email, $secretHash, $providedHash) {
   		if (!is_scalar($providedHash) || (string)$providedHash === '') {
   			return false;
   		}

   		return hash_equals(self::generateEmailVerificationHash($email, $secretHash), (string)$providedHash);
   }
   
   public $id = null;
   public $chat_id = 0;
   public $hash = '';
   public $ctime = 0;
   public $wused = 0;
}

?>