<?php

class erLhAbstractModelFormCollected {

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'form_id'  		=> $this->form_id,
			'ctime'  		=> $this->ctime,
			'ip'  			=> $this->ip,
			'content' 		=> $this->content,
			'identifier' 	=> $this->identifier
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
	
	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_form_collected" );

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
	   		
	   	case 'ctime_front':
	   			return $this->ctime_front = date('Ymd') == date('Ymd',$this->ctime) ? date(erLhcoreClassModule::$dateHourFormat,$this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat,$this->ctime);
	   		break;
	   		
	   	case 'ctime_full_front':
	   			return $this->ctime_full_front = date(erLhcoreClassModule::$dateDateHourFormat,$this->ctime);
	   		break;
	   		
	   	case 'content_array':
	   			return $this->content_array = unserialize($this->content);
	   		break;
	   		
	   	case 'form':
	   			return $this->form = erLhAbstractModelForm::fetch($this->form_id);
	   		break;
	   		
	   	case 'form_content':
	   	       return $this->getFormattedContent();
	   	    break;					
	   	default:
	   		break;
	   }
	}
	
	public function getFormattedContent()
	{	    
	    $dataCollected = array();
	    foreach ($this->content_array as $nameAttr => $contentArray) {
	        if (isset($contentArray['definition']['type']) && $contentArray['definition']['type'] == 'file') {
	            $dataCollected[] = $contentArray['definition']['name_literal']." - " . erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('user/login').'/(r)/'.rawurlencode(base64_encode('form/download/'.$this->id.'/'.$nameAttr));
	        } elseif (isset($contentArray['definition']['type']) && $contentArray['definition']['type'] == 'checkbox') {
	            $dataCollected[] = $contentArray['definition']['name_literal']." - ".($contentArray['value'] == 1 ? 'Y' : 'N');
	        } else {
	            $dataCollected[] = $contentArray['definition']['name_literal']." - ".$contentArray['value'];
	        }
	    }
	  
	    return implode("\n", $dataCollected);
	}
	
	public function getAttrValue($attrDesc) {		
		$attrs = explode(',',$attrDesc);
		
		$attrCollected = array();
		
		foreach ($attrs as $attr) {
			$attrCollected[] = $this->content_array[$attr]['value'];
		}
		
		return implode(', ', $attrCollected);
	}
	
	public function updateThis() {
		$this->saveThis();
	}
	
	public function saveThis() {
		erLhcoreClassAbstract::getSession()->saveOrUpdate($this);
	}
	
	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelFormCollected_'.$id])) return $GLOBALS['erLhAbstractModelFormCollected_'.$id];

		try {
			$GLOBALS['erLhAbstractModelFormCollected_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelFormCollected', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelFormCollected_'.$id] = '-';
		}

		return $GLOBALS['erLhAbstractModelFormCollected_'.$id];
	}

	public function removeThis()
	{
		foreach ($this->content_array as $key => $content) {
			if ($content['definition']['type'] == 'file') {
							
				if ($content['filename'] != '') {
					erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.remove_file', array('filepath' => $content['filepath'], 'filename' => $content['filename']));
				}
				
				if ($content['filepath'] != '' && file_exists($content['filepath'] . $content['filename'])){
					unlink($content['filepath'] . $content['filename']);				
					erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/', str_replace('var/', '', $content['filepath']));
				}
			}
		}
				
		erLhcoreClassAbstract::getSession()->delete($this);
	}

	public static function getList($paramsSearch = array())
   	{
       	$paramsDefault = array('limit' => 500, 'offset' => 0);

       	$params = array_merge($paramsDefault,$paramsSearch);

       	$session = erLhcoreClassAbstract::getSession();

       	$q = $session->createFindQuery( 'erLhAbstractModelFormCollected' );

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
	public $form_id = null;
	public $ctime = null;	
	public $ip = '';
	public $content = '';
	public $identifier = '';

}

?>