<?php

class erLhcoreClassModelDepartament {

    public function getState()
   {
       return array(
               'id'       				=> $this->id,
               'name'     				=> $this->name,
               'email'     				=> $this->email,
               'xmpp_recipients'     	=> $this->xmpp_recipients,
               'xmpp_group_recipients'  => $this->xmpp_group_recipients,
               'priority'     			=> $this->priority,
               'department_transfer_id' => $this->department_transfer_id,
               'transfer_timeout'    	=> $this->transfer_timeout,
               'identifier'    			=> $this->identifier,
               'mod'    				=> $this->mod,
               'tud'    				=> $this->tud,
               'wed'    				=> $this->wed,
               'thd'    				=> $this->thd,
               'frd'    				=> $this->frd,
               'sad'    				=> $this->sad,
               'sud'    				=> $this->sud,
               'start_hour'    			=> $this->start_hour,
               'end_hour'    			=> $this->end_hour,
               'inform_options'    		=> $this->inform_options,
               'inform_delay'    		=> $this->inform_delay,
               'inform_close'    		=> $this->inform_close,
               'online_hours_active'    => $this->online_hours_active,
               'disabled'    			=> $this->disabled,
               'hidden'    				=> $this->hidden,
               'delay_lm' 				=> $this->delay_lm,
               'inform_unread' 			=> $this->inform_unread,
               'inform_unread_delay' 	=> $this->inform_unread_delay,
               'na_cb_execute' 			=> $this->na_cb_execute,
               'nc_cb_execute' 			=> $this->nc_cb_execute,
               'active_balancing' 		=> $this->active_balancing,
               'max_active_chats' 		=> $this->max_active_chats,
               'max_timeout_seconds' 	=> $this->max_timeout_seconds,
               'attr_int_1' 	        => $this->attr_int_1,
               'attr_int_2' 	        => $this->attr_int_2,
               'attr_int_3' 	        => $this->attr_int_3,
               'active_chats_counter' 	=> $this->active_chats_counter,
               'pending_chats_counter' 	=> $this->pending_chats_counter,
               'closed_chats_counter' 	=> $this->closed_chats_counter,
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public static function fetch($dep_id, $useCache = false) {

   		if ($useCache == true && isset($GLOBALS['lhCacheDepartmentDepartaments_'.$dep_id])) return $GLOBALS['lhCacheDepartmentDepartaments_'.$dep_id];

   		$GLOBALS['lhCacheDepartmentDepartaments_'.$dep_id] = erLhcoreClassDepartament::getSession()->load( 'erLhcoreClassModelDepartament', (int)$dep_id );

   		return $GLOBALS['lhCacheDepartmentDepartaments_'.$dep_id];
   }

   public function __toString() {
   		return $this->name;
   }

   public function removeThis() {
	   	erLhcoreClassDepartament::getSession()->delete($this);

	   	// Delete user assigned departaments
	   	$q = ezcDbInstance::get()->createDeleteQuery();
	   	$q->deleteFrom( 'lh_departament' )->where( $q->expr->eq( 'id', $this->id ) );
	   	$stmt = $q->prepare();
	   	$stmt->execute();
   }

   public function __get($var) {
	   	switch ($var) {
	   		case 'is_online':
	   			$this->is_online = erLhcoreClassChat::isOnline($this->id);
	   			return $this->is_online;
	   		break;

	   		case 'is_online_exclipic':
	   			$this->is_online_exclipic = erLhcoreClassChat::isOnline($this->id,true);
	   			return $this->is_online_exclipic;
	   		break;

	   		case 'inform_options_array':
	   			$this->inform_options_array = $this->inform_options != '' ? unserialize($this->inform_options) : array();
	   			return $this->inform_options_array;
	   		break;

	   		case 'can_delete':
	   			$this->can_delete = erLhcoreClassChat::getCount(array('filter' => array('dep_id' => $this->id))) == 0;
	   			return $this->can_delete;
	   		break;

	   		case 'department_transfer':

	   			$this->department_transfer = false;

	   			if ($this->department_transfer_id > 0) {
	   				try {
	   					$this->department_transfer = self::fetch($this->department_transfer_id,true);
	   				} catch (Exception $e) {

	   				}
	   			}

	   			return $this->department_transfer;
	   		break;

	   		case 'start_hour_front':
	   				return floor($this->start_hour/100);
	   			break;

	   		case 'start_minutes_front':
	   				return $this->start_hour - ($this->start_hour_front * 100);
	   			break;

	   		case 'end_hour_front':
	   				return floor($this->end_hour/100);
	   			break;

	   		case 'end_minutes_front':
	   				return $this->end_hour - ($this->end_hour_front * 100);
	   			break;
	   		
	   		default:
	   			;
	   		break;
	   	}
   }

   public static function getCount($params = array())
   {
       $session = erLhcoreClassDepartament::getSession();
       $q = $session->database->createSelectQuery();
       $q->select( "COUNT(id)" )->from( "lh_departament" );

       if (isset($params['filter']) && count($params['filter']) > 0)
       {
           $conditions = array();

           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }

           $q->where(
                 $conditions
           );
      }

      $stmt = $q->prepare();
      $stmt->execute();
      $result = $stmt->fetchColumn();

      return $result;
   }

   public static function getList($paramsSearch = array())
   {
       $paramsDefault = array('limit' => 500000, 'offset' => 0);

       $params = array_merge($paramsDefault,$paramsSearch);

       $session = erLhcoreClassDepartament::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelDepartament' );

       $conditions = array();

      if (isset($params['filter']) && count($params['filter']) > 0)
      {
           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }
      }

      if (isset($params['filterin']) && count($params['filterin']) > 0)
      {
           foreach ($params['filterin'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->in( $field, $fieldValue );
           }
      }

      if (isset($params['filterlt']) && count($params['filterlt']) > 0)
      {
           foreach ($params['filterlt'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->lt( $field, $q->bindValue($fieldValue) );
           }
      }

      if (isset($params['filtergt']) && count($params['filtergt']) > 0)
      {
           foreach ($params['filtergt'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->gt( $field,$q->bindValue( $fieldValue ));
           }
      }

      if (count($conditions) > 0)
      {
          $q->where(
                     $conditions
          );
      }

      $q->limit($params['limit'],$params['offset']);

      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id ASC' );


       $objects = $session->find( $q );

      return $objects;
   }

    public $id = null;
    public $name = '';
    public $email = '';
    public $priority = 0;
    public $department_transfer_id = 0;
    public $transfer_timeout = 0;
    public $identifier = '';    
    public $xmpp_recipients = '';    
    public $xmpp_group_recipients = '';    
    public $mod = 0;
    public $tud = 0;
    public $wed = 0;
    public $thd = 0;
    public $frd = 0;
    public $sad = 0;
    public $sud = 0;
    public $inform_delay = 0;
    public $start_hour = 0;
    public $end_hour = 0;
    public $inform_options = '';    
    public $inform_close = 0;    
    public $online_hours_active = 0;
    public $disabled = 0;
    public $hidden = 0;
    public $inform_unread = 0;
    public $inform_unread_delay = 0;
    public $na_cb_execute = 0;
    public $nc_cb_execute = 0;
    public $active_balancing = 0;
    public $max_active_chats = 0;
    public $max_timeout_seconds = 0;
    public $attr_int_1 = 0;
    public $attr_int_2 = 0;
    public $attr_int_3 = 0;
    
    public $active_chats_counter = 0;
    public $pending_chats_counter = 0;
    public $closed_chats_counter = 0;
    
    // 0 - disabled
    // > 0 - delay in seconds
    public $delay_lm = 0;
}

?>