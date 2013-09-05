<?php

class erLhcoreClassModelDepartament {

    public function getState()
   {
       return array(
               'id'       => $this->id,
               'name'     => $this->name,
               'email'     => $this->email,
               'priority'     => $this->priority,
       );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

   public function fetch($dep_id, $useCache = false) {

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
       $paramsDefault = array('limit' => 32, 'offset' => 0);

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
}

?>