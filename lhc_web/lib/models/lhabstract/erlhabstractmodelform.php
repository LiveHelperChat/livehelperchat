<?php

class erLhAbstractModelForm {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'content'  		=> $this->content,
			'recipient'  	=> $this->recipient,
			'active' 		=> $this->active,
			'name_attr' 	=> $this->name_attr,
			'intro_attr' 	=> $this->intro_attr,
			'xls_columns' 	=> $this->xls_columns,
			'pagelayout' 	=> $this->pagelayout,
			'post_content' 	=> $this->post_content
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
   		return array(
   				'name' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Name for personal purposes'),
   						'required' => true,
   						'link' => erLhcoreClassDesign::baseurl('form/collected'),
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'content' => array(
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Content'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				   				 
   				  				   				 
   				'name_attr' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Name attributes'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				 
   				'intro_attr' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Introduction attributes'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),  
   				'xls_columns' => array (
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','XLS Columns'),
   						'required' => false,
   						'height'	=> '100px',
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),  
   				'recipient' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Recipient'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'post_content' => array(
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Post content after form is submitted'),
   						'required' => false,
   						'hidden' => true,
   						'height'	=> '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'pagelayout' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Custom pagelayout'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'active' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Active'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						))
   				   				
   		);
	}

	public function getModuleTranslations()
	{
	    $metaData = array('path' => array('url' => erLhcoreClassDesign::baseurl('form/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Form')),'permission_delete' => array('module' => 'lhform','function' => 'delete_fm'), 'permission' => array('module' => 'lhform','function' => 'manage_fm'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Forms list'));
	    
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_forms', array('object_meta_data' => & $metaData));
	    
		return $metaData;
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_form" );

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
	   		
	   	case 'content_rendered':
	   			return erLhcoreClassFormRenderer::renderForm($this);
	   		break;
	   		
	   	case 'xls_columns_data':
	   			$parts = explode('||',$this->xls_columns);
	   			$totalParts = array();
	   			
	   			foreach ($parts as $part) {
	   				$subParts = explode(';', $part);
	   				$dataParts = array();
	   				foreach ($subParts as $subPart) {
	   					$data = explode('=', $subPart);
	   					$dataParts[$data[0]] = $data[1];
	   				}
	   				$totalParts[] = $dataParts;
	   			}
	   			
	   			return $this->xls_columns_data = $totalParts;
	   		break;	
	   		
	   	case 'hide_delete':
	   			return $this->hide_delete = !erLhcoreClassUser::instance()->hasAccessTo('lhform','delete_fm');
	   		break;
	   			   		
	   	default:
	   		break;
	   }
	}
	
	public function updateThis(){
		$this->saveThis();
	}
	
	public function saveThis()
	{	
		erLhcoreClassAbstract::getSession()->saveOrUpdate($this);
	}
	
	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelForm_'.$id])) return $GLOBALS['erLhAbstractModelForm_'.$id];

		try {
			$GLOBALS['erLhAbstractModelForm_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelForm', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelForm_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelForm_'.$id];
	}

	public function removeThis()
	{
		erLhcoreClassAbstract::getSession()->delete($this);
	}

	public static function getList($paramsSearch = array())
   	{
       	$paramsDefault = array('limit' => 500, 'offset' => 0);

       	$params = array_merge($paramsDefault,$paramsSearch);

       	$session = erLhcoreClassAbstract::getSession();

       	$q = $session->createFindQuery( 'erLhAbstractModelForm' );

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
				$conditions[] = $q->expr->gt( $field, $q->bindValue($fieldValue) );
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

	
   	public $id = null;
	public $name = '';
	public $content = '';	
	public $active = 1;
	public $recipient = '';
	public $name_attr = '';
	public $intro_attr = '';	
	public $xls_columns = '';	
	public $pagelayout = '';	
	public $post_content = '';	
	
	public $hide_add = false;

}

?>