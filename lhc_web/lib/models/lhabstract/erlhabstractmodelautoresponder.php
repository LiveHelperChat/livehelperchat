<?php

class erLhAbstractModelAutoResponder {

	public function getState()
	{
		$stateArray = array (
			'id'         		=> $this->id,
			'siteaccess'  		=> $this->siteaccess,
			'wait_message'		=> $this->wait_message,
			'timeout_message'	=> $this->timeout_message,
			'wait_timeout'		=> $this->wait_timeout,
			'position'			=> $this->position
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
		return $this->siteaccess;
	}

   	public function getFields()
   	{
   		return array(
   				'siteaccess' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Language, leave empty for all. E.g lit, rus, ger etc...'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'position' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Position'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_message' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Wait message. Visible when users starts chat and is waiting for someone to accept a chat.'),
   						'required' => false,
   						'hidden' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Wait timeout. Time in seconds before timeout message is shown.'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'timeout_message' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show visitor this message then wait timeout passes.'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   		);
	}

	public function getModuleTranslations()
	{
		return array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Auto responder'));
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_auto_responder" );

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

	   	default:
	   		break;
	   }
	}

	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelAutoResponder_'.$id])) return $GLOBALS['erLhAbstractModelAutoResponder_'.$id];

		try {
			$GLOBALS['erLhAbstractModelAutoResponder_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelAutoResponder', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelAutoResponder_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelAutoResponder_'.$id];
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

       	$q = $session->createFindQuery( 'erLhAbstractModelAutoResponder' );

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

	public static function processAutoResponder() {

		$session = erLhcoreClassAbstract::getSession();
		$q = $session->createFindQuery( 'erLhAbstractModelAutoResponder' );
		$q->where(  $q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR `siteaccess` = \'\'')
		->orderBy('position ASC')
		->limit( 1 );

		$messagesToUser = $session->find( $q );

		if ( !empty($messagesToUser) ) {
			$message = array_shift($messagesToUser);
			return $message;
		}

		return false;
	}

	public function updateThis(){
		erLhcoreClassAbstract::getSession()->update($this);
	}

   	public $id = null;
	public $siteaccess = '';
	public $position = 0;
	public $wait_message = '';
	public $wait_timeout = 0;
	public $timeout_message = '';

	public $hide_add = false;
	public $hide_delete = false;

}

?>