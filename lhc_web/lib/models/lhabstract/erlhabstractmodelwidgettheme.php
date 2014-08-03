<?php

class erLhAbstractModelWidgetTheme {

	public function getState()
	{
		$stateArray = array (
			'id'         				=> $this->id,
			'name'  					=> $this->name,
			'onl_bcolor'				=> $this->onl_bcolor,			
			'bor_bcolor'				=> $this->bor_bcolor,			
			'text_color'				=> $this->text_color,				
			'online_image'				=> $this->online_image,
			'online_image_path'			=> $this->online_image_path,
			'offline_image'				=> $this->offline_image,
			'offline_image_path'		=> $this->offline_image_path,							
			'header_background'			=> $this->header_background,
			'widget_border_color'		=> $this->widget_border_color,
			'need_help_image'			=> $this->need_help_image,
			'need_help_bcolor'			=> $this->need_help_bcolor,
			'need_help_hover_bg'		=> $this->need_help_hover_bg,
			'need_help_tcolor'			=> $this->need_help_tcolor,
			'need_help_border'			=> $this->need_help_border,
			'need_help_close_bg'		=> $this->need_help_close_bg,
			'need_help_close_hover_bg'	=> $this->need_help_close_hover_bg,
			'need_help_image_path'		=> $this->need_help_image_path,
			'custom_status_css'			=> $this->custom_status_css,
			'custom_container_css'		=> $this->custom_container_css,
			'custom_widget_css'			=> $this->custom_widget_css,
			'need_help_header'			=> $this->need_help_header,
			'need_help_text'			=> $this->need_help_text,
			'online_text'				=> $this->online_text,
			'offline_text'				=> $this->offline_text,
			'logo_image'				=> $this->logo_image,
			'logo_image_path'			=> $this->logo_image_path,
			'copyright_image'			=> $this->copyright_image,
			'copyright_image_path'		=> $this->copyright_image_path,
			'show_copyright'			=> $this->show_copyright,
			'widget_copyright_url'		=> $this->widget_copyright_url,
			'explain_text'				=> $this->explain_text,
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
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Name'),
   						'required' => true,   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_header' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help header text'),
   						'required' => false,   
   						'nginit' => true,	
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   			
   				'need_help_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help standard text'),
   						'required' => false,
   						'nginit' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'online_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online status text'),
   						'required' => false,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'offline_text' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline status text'),
   						'required' => false,
   						'nginit' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'onl_bcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'bor_bcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget border color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'text_color' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text color'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'logo_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Logo image, visible in popup'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'logo_image_url_img',
   						'backend_call' => 'moveLogoPhoto',
   						'delete_call' => 'deleteLogoPhoto',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   				
   				'copyright_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Logo image, visible in widget left corner, 16x16'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'copyright_image_url_img',
   						'backend_call' => 'moveCopyrightPhoto',
   						'delete_call' => 'deleteCopyrightPhoto',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   				
   				'show_copyright' => array(
   						'type' => 'checkbox',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Show copyright widget logo in left corner'),
   						'required' => false,
   						'hidden' => true,   						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
   						)), 	
   				'widget_copyright_url' => array(
   						'type' => 'text',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget copyright link'),
   						'required' => false,  
   						'hidden' => true,
   						'nginit' => true,						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),  				
   				'explain_text' => array(
   						'type' => 'textarea',
   						'height' => '50px',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text above start chat form fields'),
   						'required' => false,  
   						'hidden' => true,
   						'nginit' => true,						
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),  				
   				'online_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online image'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'online_image_url_img',
   						'backend_call' => 'moveOnlinePhoto',
   						'delete_call' => 'deleteOnlinePhoto',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),
   				'offline_image' => array(
   						'type' => 'file',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline image'),
   						'required' => false,
   						'hidden' => true,
   						'frontend' => 'offline_image_url_img',
   						'backend_call' => 'moveOfflinePhoto',
   						'delete_call' => 'deleteOfflinePhoto',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),   			
   				'header_background' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget header background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'widget_border_color' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget border color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),   				
   				'need_help_bcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_hover_bg' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help hover background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_tcolor' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help text color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_border' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help border color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_close_bg' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help close background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_close_hover_bg' => array(
   						'type' => 'colorpicker',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help close hover background color'),
   						'required' => true,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'need_help_image' => array(
   						'type' => 'file',
   						'frontend' => 'url_operator_photo',
   						'backend_call' => 'moveOperatorPhoto',
   						'delete_call' => 'deleteOperatorPhoto',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help operator image'),
   						'required' => false,
   						'hidden' => true,
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								 ezcInputFormDefinitionElement::OPTIONAL, 'callback','erLhcoreClassSearchHandler::isImageFile()'
   						)),
   				'custom_status_css' => array(
   						'type' => 'textarea',
   						'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget additional CSS, takes effect after save'),
   						'required' => true,
   						'placeholder' => '#lhc_status_container:hover{}',
   						'hidden' => true,
   						'height' => '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_container_css' => array(
   						'type' 			=> 'textarea',
   						'trans' 		=> erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget container additional CSS, takes effect after save'),
   						'required' 		=> true,
   						'hidden' 		=> true,
   						'placeholder'	=>'#lhc_container #lhc_iframe_container{border:0};',
   						'height' => '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   				'custom_widget_css' => array(
   						'type' 			=> 'textarea',
   						'trans' 		=> erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget body additional CSS, takes effect after save'),
   						'required' 		=> true,
   						'placeholder' 	=> 'body {background-color:#84A52E;}',
   						'hidden' 		=> true,
   						'height' 		=> '150px',
   						'validation_definition' => new ezcInputFormDefinitionElement(
   								ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
   						)),
   		);
	}
		
			
	public function deleteOnlinePhoto()
	{
		$this->deletePhoto('online_image');				
	}
	
	public function moveOnlinePhoto()
	{
		$this->movePhoto('online_image');				
	}
			
	public function deleteOfflinePhoto()
	{
		$this->deletePhoto('offline_image');				
	}
	
	public function moveOfflinePhoto()
	{
		$this->movePhoto('offline_image');				
	}
	
	public function deleteLogoPhoto()
	{
		$this->deletePhoto('logo_image');				
	}
	
	public function moveLogoPhoto()
	{
		$this->movePhoto('logo_image');				
	}
	
	public function deleteOperatorPhoto()
	{
		$this->deletePhoto('need_help_image');
	}
	
	public function deleteCopyrightPhoto()
	{
		$this->deletePhoto('copyright_image');
	}
	
	public function moveOperatorPhoto()
	{
		$this->movePhoto('need_help_image');
	}
	
	public function moveCopyrightPhoto()
	{
		$this->movePhoto('copyright_image');
	}
								
	public function movePhoto($attr, $isLocal = false, $localFile = false)
	{
		$this->deletePhoto($attr);
	
		if ($this->id != null){
			$dir = 'var/storagetheme/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $this->id . '/';
	
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.'.$attr.'_path',array('dir' => & $dir, 'storage_id' => $this->id));
	
			erLhcoreClassFileUpload::mkdirRecursive( $dir );
	
			if ($isLocal == false) {
				$this->$attr = erLhcoreClassSearchHandler::moveUploadedFile('AbstractInput_'.$attr, $dir . '/','.' );
			} else {
				$this->$attr = erLhcoreClassSearchHandler::moveLocalFile($localFile, $dir . '/','.' );
			}
			
			$this->{$attr.'_path'} = $dir;
		} else {
			$this->{$attr.'_pending'} = true;
		}
	}	
		
	public function deletePhoto($attr)
	{
		if ($this->$attr != '') {
			if ( file_exists($this->{$attr.'_path'} . $this->$attr) ) {
				unlink($this->{$attr.'_path'} . $this->$attr);
			}
		
			erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/storagetheme/',str_replace('var/storagetheme/','',$this->{$attr.'_path'}));
		
			$this->$attr = '';
			$this->{$attr.'_path'} = '';			
		}		
	}
	
	public function getModuleTranslations()
	{
		return array('path' => array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')), 'permission_delete' => array('module' => 'lhchat','function' => 'administratethemes'),'permission' => array('module' => 'lhchat','function' => 'administratethemes'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget themes'));
	}
	
	public function saveThis() {
				
		erLhcoreClassAbstract::getSession()->save($this);
		
		if ($this->need_help_image_pending == true) {
			$this->moveOperatorPhoto();
			$this->updateThis();
		}
		
		if ($this->online_image_pending == true) {
			$this->moveOnlinePhoto();
			$this->updateThis();
		}
		
		if ($this->offline_image_pending == true) {
			$this->moveOfflinePhoto();
			$this->updateThis();
		}
		
		if ($this->logo_image_pending == true) {
			$this->moveLogoPhoto();
			$this->updateThis();
		}
		
		if ($this->copyright_image_pending == true) {
			$this->moveCopyrightPhoto();
			$this->updateThis();
		}
	}
	
	
	public static function getCount($params = array())
	{
		$session = erLhcoreClassAbstract::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_abstract_widget_theme" );

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
	   		
	   	case 'logo_image_url':
	   			$this->logo_image_url = false;
	   			
	   			if ($this->logo_image != ''){
	   				$this->logo_image_url =  erLhcoreClassSystem::instance()->wwwDir().'/'.$this->logo_image_path . $this->logo_image;
	   			}
	   			
	   			return $this->logo_image_url;
	   		break;	
	   		
	   	case 'copyright_image_url':
	   			$this->copyright_image_url = false;
	   			
	   			if ($this->copyright_image != ''){
	   				$this->copyright_image_url =  erLhcoreClassSystem::instance()->wwwDir().'/'.$this->copyright_image_path . $this->copyright_image;
	   			}
	   			
	   			return $this->copyright_image_url;
	   		break;	
	   		
	   		
	   	case 'need_help_image_url':
	   			$this->need_help_image_url = false;
	   			
	   			if ($this->need_help_image != ''){
	   				$this->need_help_image_url = erLhcoreClassSystem::instance()->wwwDir().'/'.$this->need_help_image_path . $this->need_help_image;
	   			}
	   			
	   			return $this->need_help_image_url;
	   		break;	
	   		
	   	case 'online_image_url':
	   			$this->online_image_url = false;
	   			
	   			if ($this->online_image != ''){
	   				$this->online_image_url = erLhcoreClassSystem::instance()->wwwDir().'/'.$this->online_image_path . $this->online_image;
	   			}
	   			
	   			return $this->online_image_url;
	   		break;	
	   		
	   	case 'offline_image_url':
	   			$this->offline_image_url = false;
	   			
	   			if ($this->offline_image != ''){
	   				$this->offline_image_url = erLhcoreClassSystem::instance()->wwwDir().'/'.$this->offline_image_path . $this->offline_image;
	   			}
	   			
	   			return $this->offline_image_url;
	   		break;	
	   		
	   		
	   	case 'url_operator_photo':
	   			 
	   			$this->url_operator_photo = false;
	   		
	   			if($this->need_help_image != ''){
	   				$this->url_operator_photo = '<img src="'.erLhcoreClassSystem::instance()->wwwDir().'/'.$this->need_help_image_path . $this->need_help_image.'"/>';
	   			}
	   			return $this->url_operator_photo;
	   		break;
	   		
	   	case 'online_image_url_img':
	   			 
	   			$this->online_image_url_img = false;
	   		
	   			if($this->online_image != ''){
	   				$this->online_image_url_img = '<img src="'.erLhcoreClassSystem::instance()->wwwDir().'/'.$this->online_image_path . $this->online_image.'"/>';
	   			}
	   			return $this->online_image_url_img;
	   		break;
	   		
	   	case 'offline_image_url_img':
	   			 
	   			$this->offline_image_url_img = false;
	   		
	   			if($this->offline_image != ''){
	   				$this->offline_image_url_img = '<img src="'.erLhcoreClassSystem::instance()->wwwDir().'/'.$this->offline_image_path . $this->offline_image.'"/>';
	   			}
	   			return $this->offline_image_url_img;
	   		break;
	   		
	   	case 'logo_image_url_img':
	   			 
	   			$this->logo_image_url_img = false;
	   		
	   			if ($this->logo_image != '') {
	   				$this->logo_image_url_img = '<img src="'.erLhcoreClassSystem::instance()->wwwDir().'/'.$this->logo_image_path . $this->logo_image.'"/>';
	   			}
	   			
	   			return $this->logo_image_url_img;
	   		break;
	   		
	   	case 'copyright_image_url_img':
	   			 
	   			$this->copyright_image_url_img = false;
	   		
	   			if ($this->copyright_image != '') {
	   				$this->copyright_image_url_img = '<img src="'.erLhcoreClassSystem::instance()->wwwDir().'/'.$this->copyright_image_path . $this->copyright_image.'"/>';
	   			}
	   			
	   			return $this->copyright_image_url_img;
	   		break;
	   		
	   	default:
	   		break;
	   }
	}

	public static function fetch($id)
	{
		if (isset($GLOBALS['erLhAbstractModelWidgetTheme_'.$id])) return $GLOBALS['erLhAbstractModelWidgetTheme_'.$id];

		try {
			$GLOBALS['erLhAbstractModelWidgetTheme_'.$id] = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModelWidgetTheme', (int)$id );
		} catch (Exception $e) {
			$GLOBALS['erLhAbstractModelWidgetTheme_'.$id] = false;
		}

		return $GLOBALS['erLhAbstractModelWidgetTheme_'.$id];
	}

	public function removeThis()
	{
		$this->deletePhoto('online_image');
		$this->deletePhoto('offline_image');
		$this->deletePhoto('logo_image');
		$this->deletePhoto('need_help_image');
		$this->deletePhoto('copyright_image');
		
		erLhcoreClassAbstract::getSession()->delete($this);
	}

	public static function getList($paramsSearch = array())
   	{
       	$paramsDefault = array('limit' => 500, 'offset' => 0);

       	$params = array_merge($paramsDefault,$paramsSearch);

       	$session = erLhcoreClassAbstract::getSession();

       	$q = $session->createFindQuery( 'erLhAbstractModelWidgetTheme' );

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

	public function dependCss(){
		return '<link rel="stylesheet" type="text/css" href="'.erLhcoreClassDesign::design('css/colorpicker.css').'" />';
	}

	public function dependJs(){
		return '<script type="text/javascript" src="'.erLhcoreClassDesign::design('js/colorpicker.js').'"></script>';
	}
	
	public function customForm(){
		return 'widget_theme.tpl.php';
	}
	
   	public $id = null;
	public $name = '';
	public $onl_bcolor = 'f6f6f6';
	public $text_color = '000000';
	public $bor_bcolor = 'e3e3e3';
	public $online_image = '';
	public $offline_image = '';
	public $online_image_path = '';
	public $offline_image_path = '';
	public $header_background = '525252';
	public $need_help_bcolor = '92B830';
	public $need_help_hover_bg = '84A52E';
	public $need_help_image = '';	
	public $need_help_tcolor = 'ffffff';
	public $need_help_border = 'dbe257';
	public $need_help_close_bg = '435A05';
	public $need_help_close_hover_bg = '74990F';
	public $need_help_image_path = '';
	public $custom_status_css = '';
	public $custom_container_css = '';
	public $custom_widget_css = '';
	public $need_help_header = '';
	public $need_help_text = '';
	public $online_text = '';
	public $offline_text = '';	
	public $logo_image = '';
	public $logo_image_path = '';
	public $copyright_image = '';
	public $copyright_image_path = '';
	public $show_copyright = '1';
	public $widget_copyright_url = '';
	public $explain_text = '';
	public $widget_border_color = 'cccccc';
	
	public $hide_add = false;
	public $hide_delete = false;

}

?>