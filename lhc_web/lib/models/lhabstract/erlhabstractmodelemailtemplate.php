<?php

class erLhAbstractModelEmailTemplate {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'from_name'  	=> $this->from_name,
			'from_name_ac'  => $this->from_name_ac,
			'from_email' 	=> $this->from_email,
			'from_email_ac' => $this->from_email_ac,
			'reply_to' 		=> $this->reply_to,
			'reply_to_ac' 	=> $this->reply_to_ac,
			'name'       	=> $this->name,
			'subject'       => $this->subject,
			'subject_ac'    => $this->subject_ac,
			'content'    	=> $this->content
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Name, for personal purposes'),
   						'required' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'subject' => array (
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Subject'),
   						'required' => false,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'subject_ac' => array (
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Allow user to change subject'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'from_name' => array(
   								'type' => 'text',
   								'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','From name'),
   								'required' => false,
   								'validation_definition' => new ezcInputFormDefinitionElement(
   										ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   								)),
   				'from_name_ac' => array(
   								'type' => 'checkbox',
   								'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Allow to change from name'),
   								'required' => false,
   								'hidden' => true,
   								'validation_definition' => new ezcInputFormDefinitionElement(
   										ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   								)),
   				'from_email' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','From e-mail, if mail was not send for some reason, this mail will get failed delivery message'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'from_email_ac' => array(
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Allow to change from e-mail'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'reply_to' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Reply to'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'reply_to_ac' => array(
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Allow to change reply e-mail'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)),
   				'content' => array(
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','Content'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						))
   		);
	}

	public function getModuleTranslations()
	{
		return array('name' => 'E-mail templates');
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_email_template" );

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
		if (isset($GLOBALS['erLhAbstractModelEmailTemplate_'.$id])) return $GLOBALS['erLhAbstractModelEmailTemplate_'.$id];

		try {
			$GLOBALS['erLhAbstractModelEmailTemplate_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelEmailTemplate', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelEmailTemplate_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelEmailTemplate_'.$id];
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

       	$q = $session->createFindQuery( 'erLhAbstractModelEmailTemplate' );

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

   	public $id = null;
	public $name = '';
	public $subject = '';
	public $subject_ac = 0;
	public $from_name = '';
	public $from_name_ac = 0;
	public $from_email = '';
	public $from_email_ac = 0;
	public $reply_to = '';
	public $reply_to_ac = 0;
	public $content = '';

	public $hide_add = true;
	public $hide_delete = true;

}

?>