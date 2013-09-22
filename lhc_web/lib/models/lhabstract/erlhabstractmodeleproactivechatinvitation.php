<?php

class erLhAbstractModelProactiveChatInvitation {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'siteaccess'  	=> $this->siteaccess,
			'time_on_site'  => $this->time_on_site,
			'pageviews' 	=> $this->pageviews,
			'message' 		=> $this->message,
			'identifier' 	=> $this->identifier,
			'executed_times'=> $this->executed_times,
			'position'		=> $this->position,
			'operator_name'	=> $this->operator_name,
			'wait_message'		=> $this->wait_message,
			'timeout_message'	=> $this->timeout_message,
			'wait_timeout'		=> $this->wait_timeout
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Name for personal purposes'),
   						'required' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'operator_name' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Operator name'),
   						'required' => false,
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
   				'siteaccess' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Language, leave empty for all. E.g lit, rus, ger etc...'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'time_on_site' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Time on site in seconds'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'pageviews' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Pageviews'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'identifier' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Identifier, for what identifier this message should be shown, leave empty for all'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),
   				'executed_times' => array (
   						'type' => 'none',
   						'hide_edit' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Matched times'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'message' => array(
   								'type' => 'textarea',
   								'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message to user'),
   								'required' => true,
   								'hidden' => true,
   								'validation_definition' => new ezcInputFormDefinitionElement(
   										ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   								)),
   				'wait_message' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Wait message. Visible then users starts chat and is waiting for someone to accept a chat.'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'wait_timeout' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Wait timeout. Time in seconds before timeout message is shown.'),
   						'required' => false,
   						'hidden' => true,
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
		return array('name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Pro active chat invitations'));
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_proactive_chat_invitation" );

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
		if (isset($GLOBALS['erLhAbstractModelProactiveChatInvitation_'.$id])) return $GLOBALS['erLhAbstractModelProactiveChatInvitation_'.$id];

		try {
			$GLOBALS['erLhAbstractModelProactiveChatInvitation_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelProactiveChatInvitation', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelProactiveChatInvitation_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelProactiveChatInvitation_'.$id];
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

       	$q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );

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

	public static function processProActiveInvitation(erLhcoreClassModelChatOnlineUser & $item) {

		$session = erLhcoreClassAbstract::getSession();
		$q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );
		$q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR `siteaccess` = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR `identifier` = \'\')' )
		->orderBy('position ASC')
		->limit( 1 );

		$messagesToUser = $session->find( $q );

		if ( !empty($messagesToUser) ) {
			$message = array_shift($messagesToUser);
			$item->operator_message = $message->message;
			$item->operator_user_proactive = $message->operator_name;
			$item->invitation_id = $message->id;
			$message->executed_times += 1;
			$message->updateThis();
		}
	}

	public function updateThis(){
		erLhcoreClassAbstract::getSession()->update($this);
	}

   	public $id = null;
	public $siteaccess = '';
	public $time_on_site = 0;
	public $pageviews = 0;
	public $message = '';
	public $position = 0;
	public $name = '';
	public $identifier = '';
	public $executed_times = 0;
	public $operator_name = '';
	public $wait_message = '';
	public $timeout_message = '';
	public $wait_timeout = 0;

	public $hide_add = false;
	public $hide_delete = false;

}

?>