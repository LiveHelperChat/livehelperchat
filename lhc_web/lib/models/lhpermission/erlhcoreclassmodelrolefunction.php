<?php

class erLhcoreClassModelRoleFunction {
        
   public function getState()
   {
       return array(
               'id'       => $this->id,
               'role_id'  => $this->role_id,
               'module'   => $this->module,
               'function' => $this->function,
               'limitation' => $this->limitation,
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
   public $role_id = null;
   public $module = null;
   public $function = null;
   public $limitation = '';

}

?>