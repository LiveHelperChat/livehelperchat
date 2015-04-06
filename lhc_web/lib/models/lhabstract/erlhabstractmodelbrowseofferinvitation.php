<?php

class erLhAbstractModelBrowseOfferInvitation {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'siteaccess'  	=> $this->siteaccess,
			'time_on_site'  => $this->time_on_site,
			'referrer' 		=> $this->referrer,
			'content' 		=> $this->content,
			'identifier' 	=> $this->identifier,
			'executed_times'=> $this->executed_times,						
			'url'			=> $this->url,						
			'active'		=> $this->active,						
			'has_url'				=> $this->has_url,						
			'custom_iframe_url'		=> $this->custom_iframe_url,						
			'lhc_iframe_content'	=> $this->lhc_iframe_content,						
			'is_wildcard'			=> $this->is_wildcard,						
			'referrer'				=> $this->referrer,						
			'priority'				=> $this->priority,						
			'hash'					=> $this->hash,
			'width'					=> $this->width,
			'height'				=> $this->height,
			'unit'					=> $this->unit,
			'callback_content'		=> $this->callback_content
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

	public static function getUnitOptions(){
		$items = array();
		
		$unit = new stdClass();
		$unit->id = 'pixels';
		$unit->name = 'pixels';
		$items[] = $unit;
		
		$unit = new stdClass();
		$unit->id = 'percents';
		$unit->name = 'percents';
		$items[] = $unit;
		
		return $items;
	}
	
   	public function getFields()
   	{
   		return array(
   				'name' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Name for personal purposes'),
   						'required' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'siteaccess' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Language, leave empty for all. E.g lit, rus, ger etc...'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'time_on_site' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Time on site on single page in seconds'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'priority' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Priority, the lower the higher'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'width' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Popup width'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'height' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Popup height'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'int'
   						)),
   				'unit' => array (
   						'type' => 'combobox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Measure units, px or percents'),
   						'required' => false,
   						'hidden' => true,
   						'name_attr' => 'name',
   						'source' => 'erLhAbstractModelBrowseOfferInvitation::getUnitOptions',
   						'params_call' => array(),
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),
   				'referrer' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Referrer domain without www, E.g google keyword will match any of google domain'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'url' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','URL, enter * at the end for the wildcard'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_iframe_url' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Custom iframe URL, takes priority over default content'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'content' => array(
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Default popup content'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'callback_content' => array(
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Callback content, must be valid json'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'lhc_iframe_content' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Load content in lhc iframe'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)), 
   				'active' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Active'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'identifier' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Identifier, for what identifier this message should be shown, leave empty for all'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'string'
   						)),   				
   				'executed_times' => array (
   						'type' => 'none',
   						'hide_edit' => true,
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Matched times'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						))
   				   				
   		);
	}

	public function getModuleTranslations()
	{
	    $metaData = array('path' => array('url' => erLhcoreClassDesign::baseurl('browseoffer/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Browse offers')),'permission_delete' => array('module' => 'lhbrowseoffer','function' => 'manage_bo'),'permission' => array('module' => 'lhbrowseoffer','function' => 'manage_bo'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Browser offer invitations'));
	    
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_browse_offers', array('object_meta_data' => & $metaData));
	    
		return $metaData;
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_browse_offer_invitation" );

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
	
	public function updateThis(){
		$this->saveThis();
	}
	
	public function saveThis()
	{
		if ($this->url != '') {
	
			$matchStringURL = '';
	
			$parts = parse_url($this->url);
			if (isset($parts['path'])) {
				$matchStringURL = $parts['path'];
			}
	
			if (isset($parts['query'])) {
				$matchStringURL .= '?'.$parts['query'];
			}
	
			$this->url = $matchStringURL;
			$this->has_url = 1;
	
			if (substr($this->url, -1) == '*'){
				$this->is_wildcard = 1;
			}
	
		} else {
			$this->has_url = 0;
			$this->is_wildcard = 0;
		}
	
		if ($this->hash == '') {
			$this->hash = erLhcoreClassModelForgotPassword::randomPassword(30);
		}
		
		erLhcoreClassAbstract::getSession()->saveOrUpdate($this);
	}
	
	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelBrowseOfferInvitation_'.$id])) return $GLOBALS['erLhAbstractModelBrowseOfferInvitation_'.$id];

		try {
			$GLOBALS['erLhAbstractModelBrowseOfferInvitation_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelBrowseOfferInvitation', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelBrowseOfferInvitation_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelBrowseOfferInvitation_'.$id];
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

       	$q = $session->createFindQuery( 'erLhAbstractModelBrowseOfferInvitation' );

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
	
	public static function processInvitation($params) {

		$referrer = self::getHost($params['r']);
				
		$session = erLhcoreClassAbstract::getSession();			
		
		$matchStringURL = '';
		if ($params['l'] != '') {
			$parts = parse_url($params['l']);
			if (isset($parts['path'])) {
				$matchStringURL = $parts['path'];
			}
		
			if (isset($parts['query'])) {
				$matchStringURL .= '?'.$parts['query'];
			}
		}
		
		$q = $session->createFindQuery( 'erLhAbstractModelBrowseOfferInvitation' );
		$q->where( 'active = 1 AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $params['identifier'] ) ).')
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')
				AND ('.$q->expr->lOr(
					$q->expr->eq( 'url', $q->bindValue('') ),
					$q->expr->eq( 'url', $q->bindValue( trim($matchStringURL) ) ),
					$q->expr->lAnd(
							$q->expr->eq( 'is_wildcard', $q->bindValue(1) ),
							$q->expr->like( $session->database->quote(trim($matchStringURL)),'concat(left(url,length(url)-1),\'%\')'))
				).')'
		)
		->orderBy('priority ASC')
		->limit( 1 );		
		
		$messagesToUser = $session->find( $q );

		if ( !empty($messagesToUser) ) {
			return array_shift($messagesToUser);
		}
		
		return false;
	}

   	public $id = null;
	public $siteaccess = '';
	public $time_on_site = 0;
	public $content = '';
	public $name = '';
	public $identifier = '';
	public $executed_times = 0;
	public $referrer = '';
	public $is_wildcard = 0;
	public $has_url = 0;
	public $active = 1;
	public $priority = 1;
	public $lhc_iframe_content = 0;
	public $custom_iframe_url = '';
	public $hash = '';
	public $width = 0;
	public $height = 0;
	public $unit = '';
	public $callback_content = '';

	
	public $hide_add = false;
	public $hide_delete = false;

}

?>