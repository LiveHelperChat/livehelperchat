<?php

class erLhcoreClassModelUser {
        
    public function getState()
   {
       return array(
               'id'              => $this->id,
               'username'        => $this->username,
               'password'        => $this->password,
               'email'           => $this->email,
               'name'            => $this->name,
               'surname'         => $this->surname,
               'disabled'        => $this->disabled,
               'hide_online'     => $this->hide_online,
               'all_departments' => $this->all_departments
       );
   }
   
   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }
   
   public function setPassword($password)
   {
       $cfgSite = erConfigClassLhConfig::getInstance();
	   $secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
       $this->password = sha1($password.$secretHash.sha1($password));
   } 
   
   public static function fetch($user_id)
   {
   	 $user = erLhcoreClassUser::getSession('slave')->load( 'erLhcoreClassModelUser', (int)$user_id );
   	 return $user;
   }
   
   public function __toString()
   {
   		return $this->username.' ('.$this->email.')';
   }
   public static function getUserCount($params = array())
   {
       $session = erLhcoreClassUser::getSession();
       $q = $session->database->createSelectQuery();  
       $q->select( "COUNT(id)" )->from( "lh_users" );   
         
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
   
   public function __get($param)
   {
       switch ($param) {
       	         		
       	case 'user_groups_id':
       		   $userGroups = erLhcoreClassModelGroupUser::getList(array('filter' => array('user_id' => $this->id)));
       		   $this->user_groups_id = array();
       		   
       		   if (!empty($userGroups)) {
       		   		foreach ($userGroups as $userGroup) {
       		   	 		$this->user_groups_id[] = $userGroup->group_id;
       		   		}
       		   }

       		   return $this->user_groups_id;
       		break;
       		
       	case 'lastactivity':
       	        $db = ezcDbInstance::get();       	        
       	        $stmt = $db->prepare('SELECT last_activity FROM lh_userdep WHERE user_id = :user_id LIMIT 1');  
       	        $stmt->bindValue(':user_id',$this->id);                
                $stmt->execute();
                
                $this->lastactivity = (int)$stmt->fetchColumn();
                return $this->lastactivity;                
       	            
       	    break;
       	    			
       	case 'lastactivity_front':
       		   $this->lastactivity_front = '';
       		   
       		   if ( $this->lastactivity > 0 ) {
       		       $this->lastactivity_front = date('Y-m-d H:i:s');
       		   };
       		   
       		   return $this->lastactivity_front;
       		break;
       		
       	case 'lastactivity_ago':
       		   $this->lastactivity_ago = '';
       		   
       		   if ( $this->lastactivity > 0 ) {
       		               		       
                    $periods         = array("s.", "m.", "h.", "d.", "w.", "m.", "y.", "dec.");
                    $lengths         = array("60","60","24","7","4.35","12","10");
                                                                                                 
                    $difference     = time() - $this->lastactivity;
                                         
                    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
                        $difference /= $lengths[$j];
                    }
                 
                    $difference = round($difference);
                    
                   $this->lastactivity_ago = "$difference $periods[$j]";       		       
       		   };
       		   
       		   return $this->lastactivity_ago;
       		break;
              
       	default:
       		break;
       }
   }
   
   public static function getUserList($paramsSearch = array())
   {             
       $paramsDefault = array('limit' => 32, 'offset' => 0);
       
       $params = array_merge($paramsDefault,$paramsSearch);
       
       $session = erLhcoreClassUser::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelUser' );  
       
       $conditions = array(); 
       if (!isset($paramsSearch['smart_select'])) {
             
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
       } else {
           $q2 = $q->subSelect();
           $q2->select( 'pid' )->from( 'lh_users' );
           
           if (isset($params['filter']) && count($params['filter']) > 0)
          {                     
               foreach ($params['filter'] as $field => $fieldValue)
               {
                   $conditions[] = $q2->expr->eq( $field, $q->bindValue($fieldValue ));
               }
          } 
          
          if (isset($params['filterin']) && count($params['filterin']) > 0)
          {
               foreach ($params['filterin'] as $field => $fieldValue)
               {
                   $conditions[] = $q2->expr->in( $field, $fieldValue );
               } 
          }
          
          if (isset($params['filterlt']) && count($params['filterlt']) > 0)
          {
               foreach ($params['filterlt'] as $field => $fieldValue)
               {
                   $conditions[] = $q2->expr->lt( $field, $q->bindValue($fieldValue ));
               } 
          }
          
          if (isset($params['filtergt']) && count($params['filtergt']) > 0)
          {
               foreach ($params['filtergt'] as $field => $fieldValue)
               {
                   $conditions[] = $q2->expr->gt( $field,$q->bindValue( $fieldValue) );
               } 
          }      
          
          if (count($conditions) > 0)
          {
              $q2->where( 
                         $conditions   
              );
          }
           
          $q2->limit($params['limit'],$params['offset']);
          $q2->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC');
          $q->innerJoin( $q->alias( $q2, 'items' ), 'lh_users.id', 'items.id' );          
       }
              
       $objects = $session->find( $q );
         
      return $objects; 
   }
   
   public static function userExists($username)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT count(*) as foundusers FROM lh_users WHERE username = :username');
       $stmt->bindValue( ':username',$username);       
       $stmt->execute();
       $rows = $stmt->fetchAll();
       
       return $rows[0]['foundusers'] > 0;        
   }
   
   public static function fetchUserByEmail($email)
   {
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('SELECT id FROM lh_users WHERE email = :email');
       $stmt->bindValue( ':email',$email);       
       $stmt->execute();
       $rows = $stmt->fetchAll();
       
       if (isset($rows[0]['id'])) {
            return $rows[0]['id']; 
       } else {
            return false; 
       }
   }
      
    public $id = null;
    public $username = '';
    public $password = '';
    public $email = '';
    public $name = '';
    public $surname = '';
    public $disabled = 0;
    public $hide_online = 0;
    public $all_departments = 0;
}

?>