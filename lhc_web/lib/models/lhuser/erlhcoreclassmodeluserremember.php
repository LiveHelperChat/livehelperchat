<?php

class erLhcoreClassModelUserRemember {

   public function getState()
   {
       return array (
               'id'           => $this->id,
               'user_id'      => $this->user_id,
               'mtime'        => $this->mtime,
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public static function fetch($user_id)
   {
   	 $user = erLhcoreClassUser::getSession('slave')->load( 'erLhcoreClassModelUserRemember', (int)$user_id );
   	 return $user;
   }

   public function removeThis()
   {
   	    erLhcoreClassUser::getSession()->delete($this );
   }

   public function saveThis()
   {
   	    erLhcoreClassUser::getSession()->save($this );
   }

   public function updateThis()
   {
   	    erLhcoreClassUser::getSession()->update($this );
   }

   public static function getUserCount($params = array())
   {
       $session = erLhcoreClassUser::getSession('slave');
       $q = $session->database->createSelectQuery();
       $q->select( "COUNT(id)" )->from( "lh_users_remember" );

       $conditions = array();

       if (isset($params['filter']) && count($params['filter']) > 0)
       {
           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }
       }

       if (isset($params['filternot']) && count($params['filternot']) > 0)
       {
           foreach ($params['filternot'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->neq( $field, $q->bindValue($fieldValue) );
           }
       }


       if (!empty($conditions)){

           $q->where(
                 $conditions
           );
       }


      return $q->execute()->count();
   }

   public static function getUserList($paramsSearch = array())
   {
       $paramsDefault = array('limit' => 32, 'offset' => 0);

       $params = array_merge($paramsDefault,$paramsSearch);

       $session = erLhcoreClassUser::getSession('slave');
       $q = $session->createFindQuery( 'erLhcoreClassModelUserRemember' );

       $conditions = array();

      if (isset($params['filter']) && count($params['filter']) > 0)
      {
           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }
      }

      if (isset($params['filternot']) && count($params['filternot']) > 0)
      {
           foreach ($params['filternot'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->neq( $field, $q->bindValue($fieldValue) );
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

   public $id = null;
   public $user_id = null;
   public $mtime = null;
}

?>