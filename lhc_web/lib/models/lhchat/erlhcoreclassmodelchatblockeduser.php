<?php

class erLhcoreClassModelChatBlockedUser {
        
   public function getState()
   {
       return array(
               'id'           => $this->id,
               'ip'           => $this->ip,
               'user_id'      => $this->user_id,
               'datets'       => $this->datets
       );
   }
   
   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   } 
   
   public static function fetch($chat_id) {
       	 $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatBlockedUser', (int)$chat_id );
       	 return $chat;
   }
   
   public function removeThis() {
       erLhcoreClassChat::getSession()->delete($this);
   }
   
   public function __get($var) {
       switch ($var) {
       	case 'datets_front':
       		  return date(erLhcoreClassModule::$dateDateHourFormat,$this->datets);
       		break;
       		
       	case 'user':
       	       try {
       	           $this->user = erLhcoreClassModelUser::fetch($this->user_id);
       	       } catch (Exception $e) {
       	           $this->user = '-';
       	       }
       		  return $this->user;
       		break;
       
       	default:
       		break;
       }
   }
   
   public static function getCount($params = array())
   {
       $session = erLhcoreClassChat::getSession();
       $q = $session->database->createSelectQuery();  
       $q->select( "COUNT(id)" )->from( "lh_chat_blocked_user" );   
         
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
       $paramsDefault = array('limit' => 32, 'offset' => 0);
       
       $params = array_merge($paramsDefault,$paramsSearch);
       
       $session = erLhcoreClassChat::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelChatBlockedUser' );  
       
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
                
      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' ); 
              
      $objects = $session->find( $q );
         
      return $objects; 
   }
   
    
   public function saveThis() {
       $this->datets = time();
       erLhcoreClassChat::getSession()->saveOrUpdate( $this );
   }
   
   public $id = null;
   public $ip = '';
   public $user_id = 0;
   public $datets = '';
}

?>