<?php

class erLhcoreClassModelUserDep {
        
    public function getState()
   {
       return array(
               'id'          => $this->id,
               'user_id'     => $this->user_id,
               'dep_id'      => $this->dep_id
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
    public $user_id = '';
    public $dep_id = '';

}

?>