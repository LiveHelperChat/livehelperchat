<?

class erLhcoreClassModelTransfer {
        
   public function getState()
   {
       return array(
               'id'       => $this->id,
               'user_id'  => $this->user_id,
               'chat_id'  => $this->chat_id
       );
   }
   
   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }
           
   public $id = null;
   public $user_id = null;
   public $chat_id = null;
}

?>