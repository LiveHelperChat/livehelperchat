<?php

class erLhAbstractModelProactiveChatInvitation {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'siteaccess'  	=> $this->siteaccess,
			'time_on_site'  => $this->time_on_site,
			'referrer' 		=> $this->referrer,
			'pageviews' 	=> $this->pageviews,
			'message' 			=> $this->message,
			'repeat_number' 	=> $this->repeat_number,
			'message_returning' => $this->message_returning,
			'message_returning_nick' => $this->message_returning_nick,
			'identifier' 	=> $this->identifier,
			'dep_id' 		=> $this->dep_id,
			'executed_times'=> $this->executed_times,
			'position'		=> $this->position,
			'operator_name'	=> $this->operator_name,
			'wait_message'		    => $this->wait_message,
			'timeout_message'	    => $this->timeout_message,
			'wait_timeout'		    => $this->wait_timeout,
			'requires_email'		=> $this->requires_email,
			'requires_username'		=> $this->requires_username,
			'show_random_operator'	=> $this->show_random_operator,
			'hide_after_ntimes'	    => $this->hide_after_ntimes,
			'operator_ids'	    => $this->operator_ids,
			'requires_phone'	=> $this->requires_phone
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
	
	public function checkPermission(){
		
		$currentUser = erLhcoreClassUser::instance();
		
		/**
		 * Append user departments filter
		 * */
		$departmentParams = array();
		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
		if ($userDepartments !== true) {
			if (!in_array($this->dep_id, $userDepartments)) {
				return false;
			}
		}
	}
	
	public static function getFilter(){
		
		$currentUser = erLhcoreClassUser::instance();
		$departmentParams = array();
		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
		if ($userDepartments !== true){
			$departmentParams['filterin']['dep_id'] = $userDepartments;
		}
		
		return $departmentParams;
	}
	
   	public function getFields()
   	{
   		$currentUser = erLhcoreClassUser::instance();
   		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
   		
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
   				'referrer' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Referrer domain without www, E.g google keyword will match any of google domain'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'hide_after_ntimes' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','How many times user show invitation, 0 - untill users closes it, > 0 limits.'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'requires_email' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Requires e-mail'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'requires_username' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Requires name'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'requires_phone' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Requires phone'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'show_random_operator' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Show random operator profile'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'operator_ids' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Enter operators IDs from whom random operator should be shown, separated by comma'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),
   				'identifier' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Identifier, for what identifier this message should be shown, leave empty for all'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),   				
   				'dep_id' => array (
   						'type' => 'combobox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Department'),
   						'required' => false,
   						'hidden' => true,
   						'source' => 'erLhcoreClassModelDepartament::getList',
   						'hide_optional' => $userDepartments !== true,
   						'params_call' => ($userDepartments === true) ? array() : array('filterin' => array('id' => $userDepartments)),
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
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
   				'message_returning' => array(
   								'type' => 'textarea',
   								'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Message to returning user'),
   								'required' => false,
   								'hidden' => true,
   								'validation_definition' => new ezcInputFormDefinitionElement(
   										ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   								)),
   				'message_returning_nick' => array(
   								'type' => 'text',
   								'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Nick which will be used if we cannot determine returning user name'),
   								'required' => false,
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
   		       'repeat_number' => array(
           		        'type' => 'text',
           		        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','How many times repeat message?'),
           		        'required' => true,
   		                'hidden' => true,
           		        'validation_definition' => new ezcInputFormDefinitionElement(
           		            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
   		        )),
   		);
	}

	public function getModuleTranslations()
	{
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    $metaData = array('permission_delete' => array('module' => 'lhchat','function' => 'administrateinvitations'),'permission' => array('module' => 'lhchat','function' => 'administrateinvitations'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Pro active chat invitations'));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_proactive', array('object_meta_data' => & $metaData));	
	    
		return $metaData;
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_proactive_chat_invitation" );
		
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
		
		if ( count($conditions) > 0)
		{
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

	public static function getHost($url) {
		$url = parse_url($url);
		if (isset($url['host'])) {
			return str_replace('www.','',$url['host']);
		}
		
		return '';
	}
	
	public static function processProActiveInvitation(erLhcoreClassModelChatOnlineUser & $item) {

		$referrer = self::getHost($item->referrer);
				
		$session = erLhcoreClassAbstract::getSession();			
				
		$q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );
		$q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $item->dep_id ) ).' OR dep_id = 0)
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
		)
		->orderBy('position ASC')
		->limit( 1 );		
		
		$messagesToUser = $session->find( $q );

		if ( !empty($messagesToUser) ) {
			$message = array_shift($messagesToUser);
			
			// Use default message if first time visit or returning message is empty
			if ($item->total_visits == 1 || $message->message_returning == '') {			
				$item->operator_message = $message->message;
			} else {				
				if ($item->chat !== false && $item->chat->nick != '') {
					$nick = $item->chat->nick;
				} elseif ($message->message_returning_nick != '') {
					$nick = $message->message_returning_nick;
				} else {
					$nick = '';
				}
				
				$item->operator_message = str_replace('{nick}', $nick, $message->message_returning);				
			}
			
			$item->operator_user_proactive = $message->operator_name;
			$item->invitation_id = $message->id;
			$item->invitation_seen_count = 0;
			$item->requires_email = $message->requires_email;
			$item->requires_username = $message->requires_username;
			$item->requires_phone = $message->requires_phone;
			$item->invitation_count++;
			$item->store_chat = true;
			$item->invitation_assigned = true;
			$item->last_visit = time();

			if ($message->show_random_operator == 1) {
				$item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => explode(',',trim($message->operator_ids))));				
			}

			$message->executed_times += 1;
			$message->updateThis();
			
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('message' => & $message, 'ou' => & $item));
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
	public $message_returning = '';
	public $message_returning_nick = '';
	public $position = 0;
	public $requires_email = 0;
	public $requires_username = 0;
	public $requires_phone = 0;
	public $name = '';
	public $identifier = '';
	public $executed_times = 0;
	public $operator_name = '';
	public $wait_message = '';
	public $timeout_message = '';
	public $wait_timeout = 0;
	public $show_random_operator = 0;
	public $hide_after_ntimes = 0;
	public $repeat_number = 1;
	public $dep_id = 0;
	public $referrer = '';
	public $operator_ids = '';

	public $hide_add = false;
	public $hide_delete = false;

}

?>