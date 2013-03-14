<?php

class erLhcoreClassModelUserSettingOption {

   public function getState()
   {
       return array(
               'identifier'   => $this->identifier,
               'class'        => $this->class,
               'attribute'        => $this->attribute,
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
   	    $user = erLhcoreClassUser::getSession()->load( 'erLhcoreClassModelUserSettingOption', $id );
   	    return $user;
   }

   public static function getCount($params = array())
   {
       $session = erLhcoreClassUser::getSession();
       $q = $session->database->createSelectQuery();
       $q->select( "COUNT(id)" )->from( "lh_users_setting_option" );

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

   public function saveThis()
   {
       erLhcoreClassUser::getSession()->saveOrUpdate($this);
   }

   public function __get($var)
   {
       switch ($var) {
       	case 'options':
       		   $options = call_user_func($this->class,array());
       		   return $options;
       		break;

       	default:
       		break;
       }
   }

   public static function getList($paramsSearch = array())
   {
       $paramsDefault = array('limit' => 32, 'offset' => 0);

       $params = array_merge($paramsDefault,$paramsSearch);

       $session = erLhcoreClassUser::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelUserSetting' );

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

      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'identifier DESC' );


       $objects = $session->find( $q, 'erLhcoreClassModelUserSettingOption' );

      return $objects;
   }

    public $identifier = null;
    public $class = null;
    public $attribute = null;

}

?>