<?php

class erLhcoreClassModelRole {
        
    public function getState()
   {
       return array(
               'id'          => $this->id,
               'name'        => $this->name             
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
   public $name = '';

}



?>