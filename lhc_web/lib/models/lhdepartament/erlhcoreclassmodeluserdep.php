<?php

class erLhcoreClassModelUserDep {
        
    public function getState()
   {
       return array(
               'id'             => $this->id,
               'user_id'        => $this->user_id,
               'dep_id'         => $this->dep_id,
               'last_activity'  => $this->last_activity,
               'hide_online'    => $this->hide_online
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
   public $user_id = 0;
   public $dep_id = 0;
   public $hide_online = 0;
   public $last_activity = 0;
}

?>