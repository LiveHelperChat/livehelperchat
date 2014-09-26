<?php

class erLhcoreClassModelGroupRole {
        
    public function getState()
   {
       return array(
               'id'          => $this->id,
               'group_id'    => $this->group_id,             
               'role_id'     => $this->role_id             
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
   public $role_id = '';

}



?>