<?

class erLhcoreClassModelGroupUser {
        
    public function getState()
   {
       return array(
               'id'          => $this->id,
               'group_id'    => $this->group_id,             
               'user_id'     => $this->user_id             
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
   public $group_id = '';
   public $user_id = '';

}



?>