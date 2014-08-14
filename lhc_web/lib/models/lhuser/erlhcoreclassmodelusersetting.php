<?php

class erLhcoreClassModelUserSetting {

   public function getState()
   {
       return array(
               'id'           => $this->id,
               'user_id'      => $this->user_id,
               'identifier'   => $this->identifier,
               'value'        => $this->value
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public function __toString()
   {
   		return $this->value;
   }

   public static function fetch($id)
   {
   	 $user = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUserSetting', (int)$id );
   	 return $user;
   }

   public static function getCount($params = array())
   {
       $session = erLhcoreClassUser::getSession();
       $q = $session->database->createSelectQuery();
       $q->select( "COUNT(id)" )->from( "lh_users_setting" );

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


   public static function setSetting($identifier, $value, $user_id = false)
   {
       if ($user_id == false) {
            $currentUser = erLhcoreClassUser::instance();
            if ($currentUser->isLogged()) {
                $user_id = $currentUser->getUserID();
            }
       }

       if ($user_id !== false) {
           $list = self::getList(array('filter' => array('user_id' => $user_id, 'identifier' => $identifier)));

           if ( count($list) > 0 ) {
               $item = array_shift($list);
           } else {
               $item = new erLhcoreClassModelUserSetting();
               $item->user_id = $user_id;
               $item->identifier = $identifier;
           }

           $item->value = $value;

           $item->saveThis();

           CSCacheAPC::getMem()->store('settings_user_id_'.$user_id.'_'.$identifier, $value);
           CSCacheAPC::getMem()->setSession('settings_user_id_'.$user_id.'_'.$identifier, $value,true);

       } else {
           CSCacheAPC::getMem()->setSession('anonymous_'.$identifier,$value);
       }
   }

   public static function getSetting($identifier, $default_value, $user_id = false, $noSession = false)
   {
       if ($user_id == false) {
           $currentUser = erLhcoreClassUser::instance();
           if ($currentUser->isLogged()) {
               $user_id = $currentUser->getUserID();
           }
       }

       if ($user_id !== false){

       	   $value = CSCacheAPC::getMem()->getSession('settings_user_id_'.$user_id.'_'.$identifier,true);

           if ($value === false && ($value = CSCacheAPC::getMem()->restore('settings_user_id_'.$user_id.'_'.$identifier)) === false) {
               $value = $default_value;
               $list = self::getList(array('filter' => array('user_id' => $user_id,'identifier' => $identifier)));

               if (count($list) > 0) {
                   $item = array_shift($list);
                   $value = $item->value;
               } else {
                   $item = new erLhcoreClassModelUserSetting();
                   $item->value = $default_value;
                   $item->user_id = $user_id;
                   $item->identifier = $identifier;
                   $item->saveThis();
               }

               CSCacheAPC::getMem()->store('settings_user_id_'.$user_id.'_'.$identifier,$value);
               CSCacheAPC::getMem()->setSession('settings_user_id_'.$user_id.'_'.$identifier,$value,true);
           }
       } else {
       	   $value = $default_value;
       	          	   
           if ($noSession === false && ($value = CSCacheAPC::getMem()->getSession('anonymous_'.$identifier)) === false) {
           	   $value = $default_value;
               CSCacheAPC::getMem()->setSession('anonymous_'.$identifier,$value);
           }
       }

       return $value;
   }

   public function saveThis()
   {
       erLhcoreClassUser::getSession()->saveOrUpdate($this);
   }

   public static function getList($paramsSearch = array())
   {
       $paramsDefault = array('limit' => 32, 'offset' => 0);

       $params = array_merge($paramsDefault,$paramsSearch);

       $session = erLhcoreClassUser::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelUserSetting' );

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
          $q->innerJoin( $q->alias( $q2, 'items' ), 'lh_users_setting.id', 'items.id' );
       }

       $objects = $session->find( $q, 'erLhcoreClassModelUserSetting' );

      return $objects;
   }


    public $id = null;
    public $user_id = null;
    public $identifier = '';
    public $value = '';

}

?>