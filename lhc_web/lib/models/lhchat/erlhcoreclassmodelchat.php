<?

class erLhcoreClassModelChat {
        
    public function getState()
   {
       return array(
               'id'         => $this->id,
               'nick'       => $this->nick,
               'status'     => $this->status,
               'time'       => $this->time,
               'user_id'    => $this->user_id,
               'hash'       => $this->hash,
               'ip'         => $this->ip,
               'referrer'   => $this->referrer,
               'dep_id'     => $this->dep_id,
               'email'     => $this->email,
               'user_status'=> $this->user_status,
               'support_informed'=> $this->support_informed
       );
   }
   
   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   } 
   
   public function setIP()
   {
       $this->ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
   }
   
   public function getChatOwner()
   {
       try {
           $user = erLhcoreClassUser::getSession()->load('erLhcoreClassModelUser', $this->user_id);
           return $user;
       } catch (Exception $e) {
           return false;
       }
   }  
    
    public $id = null;
    public $nick = '';
    public $status = 0;
    public $time = '';
    public $user_id = '';
    public $hash = '';
    public $ip = '';
    public $referrer = '';
    public $dep_id = '';
    public $email = '';
    public $user_status = '';
    public $support_informed = '';
}

?>