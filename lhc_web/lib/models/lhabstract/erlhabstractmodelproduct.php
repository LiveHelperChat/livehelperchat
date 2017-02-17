<?php
/**
 * 
 * @author Remigijus Kiminas
 * 
 * @desc Main chat product object
 *
 */

class erLhAbstractModelProduct {

	public function getState()
	{
		$stateArray = array (
			'id'         	 => $this->id,
			'name'  		 => $this->name,
			'priority'		 => $this->priority,
			'departament_id' => $this->departament_id,
			'disabled'       => $this->disabled,
		);

		return $stateArray;
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
		return $this->name;
	}

   	public function getFields()
   	{
   	    return include('lib/core/lhabstract/fields/erlhabstractmodelproduct.php');
	}

	public function getModuleTranslations()
	{
	    $metaData = array('path' => array('url' => erLhcoreClassDesign::baseurl('product/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Products')), 'permission_delete' => array('module' => 'lhproduct','function' => 'manage_product'), 'permission' => array('module' => 'lhproduct','function' => 'manage_product'), 'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/product','Product'));
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_product', array('object_meta_data' => & $metaData));

		return $metaData;
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_product" );

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
	   		$conditions = array();

		   	foreach ($params['filter'] as $field => $fieldValue)
		   	{
		    	$conditions[] = $q->expr->eq( $field, $fieldValue );
		   	}

	   		$q->where( $conditions );
		}

		$stmt = $q->prepare();
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}

	public function __get($var)
	{
	   switch ($var) {

	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;

	   	case 'departament':
	   	       try {
	   	           $this->departament = erLhcoreClassModelDepartament::fetch($this->departament_id);
	   	       } catch (Exception $e) {
	   	           $this->departament = false;
	   	       }
	   		   return $this->departament;
	   		break;

	   	case 'name_department':
	   	       $this->name_department = $this->name.' ('.$this->departament.')';
	   		   return $this->name_department;
	   		break;

	   	default:
	   		break;
	   }
	}

	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelProduct'.$id])) return $GLOBALS['erLhAbstractModelProduct'.$id];

		try {
			$GLOBALS['erLhAbstractModelProduct'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelProduct', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelProduct'.$id] = false;
		}

		return $GLOBALS['erLhAbstractModelProduct'.$id];
	}

	public function removeThis()
	{
	    $q = ezcDbInstance::get()->createDeleteQuery();
	    
	    // Delete related product departments
	    $q->deleteFrom( 'lh_abstract_product_departament' )->where( $q->expr->eq( 'product_id', $this->id ) );
	    $stmt = $q->prepare();
	    $stmt->execute();
	    
		erLhcoreClassAbstract::getSession()->delete($this);
	}

	public static function getList($paramsSearch = array())
   	{
       	$paramsDefault = array('limit' => 500, 'offset' => 0);

       	$params = array_merge($paramsDefault,$paramsSearch);

       	$session = erLhcoreClassAbstract::getSession();

       	$q = $session->createFindQuery( 'erLhAbstractModelProduct' );

		$conditions = array();

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
			foreach ($params['filter'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->eq( $field, $fieldValue );
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
				$conditions[] = $q->expr->lt( $field, $fieldValue );
			}
		}

		if (isset($params['filtergt']) && count($params['filtergt']) > 0)
		{
			foreach ($params['filtergt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->gt( $field, $fieldValue );
			}
		}

		if (count($conditions) > 0)
		{
			$q->where( $conditions );
		}

      	$q->limit($params['limit'],$params['offset']);

      	$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id ASC' );

       	$objects = $session->find( $q );

    	return $objects;
	}

	public function updateThis(){
		erLhcoreClassAbstract::getSession()->update($this);
	}

   	public $id = null;
	public $name = '';
	public $priority = 0;
	public $departament_id = 0;
	public $disabled = 0;
		
	public $hide_add = false;
	public $hide_delete = false;

}

?>