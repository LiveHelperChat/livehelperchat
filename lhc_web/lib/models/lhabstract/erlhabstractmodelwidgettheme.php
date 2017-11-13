<?php

class erLhAbstractModelWidgetTheme {

	public function getState()
	{
		$stateArray = array (
			'id'         				=> $this->id,
			'name'  					=> $this->name,
			'name_company'  			=> $this->name_company,
			'onl_bcolor'				=> $this->onl_bcolor,			
			'bor_bcolor'				=> $this->bor_bcolor,			
			'text_color'				=> $this->text_color,				
			'online_image'				=> $this->online_image,
			'online_image_path'			=> $this->online_image_path,
			'offline_image'				=> $this->offline_image,
			'offline_image_path'		=> $this->offline_image_path,					
			'operator_image'			=> $this->operator_image,
			'operator_image_path'		=> $this->operator_image_path,											
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
			'custom_popup_css'			=> $this->custom_popup_css,
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
			'intro_operator_text'		=> $this->intro_operator_text,		    
			'minimize_image'		    => $this->minimize_image,
		    'minimize_image_path'		=> $this->minimize_image_path,		    
			'restore_image'		        => $this->restore_image,
		    'restore_image_path'		=> $this->restore_image_path,		    
			'close_image'		        => $this->close_image,
		    'close_image_path'		    => $this->close_image_path,		    
			'popup_image'		        => $this->popup_image,			
			'popup_image_path'		    => $this->popup_image_path,		    
			'hide_close'		        => $this->hide_close,
			'hide_popup'		        => $this->hide_popup,
			'header_height'		        => $this->header_height,
			'header_padding'		    => $this->header_padding,
			'widget_border_width'		=> $this->widget_border_width,
			'support_joined'		    => $this->support_joined,
			'support_closed'		    => $this->support_closed,
			'pending_join'		        => $this->pending_join,
			'noonline_operators'		=> $this->noonline_operators,
			'noonline_operators_offline'=> $this->noonline_operators_offline,
			'show_need_help'            => $this->show_need_help,
			'show_need_help_timeout'    => $this->show_need_help_timeout,
		    
			'show_voting'               => $this->show_voting,
			'department_title'          => $this->department_title,
			'department_select'         => $this->department_select,
			'buble_visitor_background'  => $this->buble_visitor_background,
			'buble_visitor_title_color' => $this->buble_visitor_title_color,
			'buble_visitor_text_color'  => $this->buble_visitor_text_color,
			'buble_operator_background' => $this->buble_operator_background,
			'buble_operator_title_color'=> $this->buble_operator_title_color,
			'buble_operator_text_color' => $this->buble_operator_text_color,

			'hide_ts'                   => $this->hide_ts,
			'widget_response_width'     => $this->widget_response_width,
		);

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.getstate',array('state' => & $stateArray, 'object' => & $this));
		
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
   		return include('lib/core/lhabstract/fields/erlhabstractmodelwidgettheme.php');
	}
	
	public function getContentAttribute($attr)
	{
		$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.download_image.'.$attr, array('theme' => $this, 'attr' => $attr));
		if ($response === false) {
			return file_get_contents($this->{$attr.'_path'}.'/'.$this->$attr);
		} else {
			return $response['filedata'];
		}
	}
								
	public function movePhoto($attr, $isLocal = false, $localFile = false)
	{
		$this->deletePhoto($attr);
	
		if ($this->id != null){
			$dir = 'var/storagetheme/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $this->id . '/';
	
			$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.'.$attr.'_path',array('dir' => & $dir, 'storage_id' => $this->id));
	
			erLhcoreClassFileUpload::mkdirRecursive( $dir );
						
			if ($isLocal == false) {
				$this->$attr = erLhcoreClassSearchHandler::moveUploadedFile('AbstractInput_'.$attr, $dir . '/','.' );
			} else {
				$this->$attr = erLhcoreClassSearchHandler::moveLocalFile($localFile, $dir . '/','.' );
			}
			
			$this->{$attr.'_path'} = $dir;
			
			$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.store_'.$attr,array(
					'theme' => & $this, 
					'path_attr' => $attr.'_path', 
					'name' => $this->$attr,
			        'name_attr' => $attr,				
					'file_path' => $this->{$attr.'_path'} . $this->$attr));
			
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
		
			if ($this->{$attr.'_path'} != '') {
				erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/storagetheme/',str_replace('var/storagetheme/','',$this->{$attr.'_path'}));
			}
			
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.remove_'.$attr,array(
					'theme' => & $this,
					'path_attr' => $attr.'_path',
					'name' => $this->$attr));
			
			$this->$attr = '';
			$this->{$attr.'_path'} = '';			
		}		
	}
	
	public function getModuleTranslations()
	{
	    $metaData = array('path' => array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')), 'permission_delete' => array('module' => 'lhchat','function' => 'administratethemes'),'permission' => array('module' => 'lhchat','function' => 'administratethemes'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget themes'));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_themes', array('object_meta_data' => & $metaData));
	    
	    return $metaData;
	}
	
	public function saveThis() {
				
		erLhcoreClassAbstract::getSession()->save($this);
		
		$movePhotos = array(
		    'need_help_image',
		    'online_image',
		    'offline_image',
		    'logo_image',
		    'copyright_image',
		    'operator_image',
		    'minimize_image',
		    'restore_image',
		    'close_image',
		    'popup_image',
		);
		
		$pendingUpdate = false;
		foreach ($movePhotos as $photoAttr) {
		    if ($this->{$photoAttr.'_pending'} == true) {
		        $this->movePhoto($photoAttr);
		        $pendingUpdate = true;		       
		    }
		}
		
		if ($pendingUpdate == true) {
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
	   	case 'minimize_image_url':
	   	case 'restore_image_url':
	   	case 'close_image_url':
	   	case 'popup_image_url':
	   	case 'operator_image_url':
	   	case 'copyright_image_url':
	   	case 'need_help_image_url':
	   	case 'online_image_url':
	   	case 'offline_image_url':
	   	       $attr = str_replace('_url', '', $var);	   	       	   	       
	   	       $this->$var = false;	   	        
	   	       if ($this->$attr != ''){
	   	           $this->$var =  ($this->{$attr.'_path'} != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'.$this->{$attr.'_path'} . $this->$attr;
	   	       }	   	        
	   	       return $this->$var;
	   	    break;
	   		   			   		
	   	case 'need_help_image_url_img':
	   	case 'online_image_url_img':
	   	case 'offline_image_url_img':
	   	case 'logo_image_url_img':
	   	case 'copyright_image_url_img':
	   	case 'operator_image_url_img':
	   	case 'popup_image_url_img':
	   	case 'close_image_url_img':
	   	case 'restore_image_url_img':
	   	case 'minimize_image_url_img':	   			 
	   	        $attr = str_replace('_url_img', '', $var);	   	    
	   			$this->$var = false;	   		
	   			if($this->$attr != ''){
	   				$this->$var = '<img src="'.($this->{$attr.'_path'} != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'.$this->{$attr.'_path'} . $this->$attr.'"/>';
	   			}
	   			return $this->$var;
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
	    $imagesToRemove = array(
            'online_image',
            'offline_image',
            'logo_image',
            'need_help_image',
            'copyright_image',
            'operator_image',
            'minimize_image',
            'restore_image',
            'close_image',
            'popup_image'
        );
	    
	    foreach ($imagesToRemove as $img) {
	        $this->deletePhoto($img);
	    }
		
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
	public $operator_image = '';
	public $operator_image_path = '';
	public $copyright_image = '';
	public $copyright_image_path = '';
	public $show_copyright = '1';
	public $widget_copyright_url = '';
	public $explain_text = '';
	public $intro_operator_text = '';
	public $widget_border_color = 'cccccc';	
	public $hide_close = 0;
	public $hide_popup = 0;	
	public $minimize_image = '';
	public $minimize_image_path = '';	
	public $restore_image = '';
	public $restore_image_path = '';	
	public $close_image = '';
	public $close_image_path = '';	
	public $popup_image = '';
	public $popup_image_path = '';	
	public $custom_popup_css = '';
	public $name_company = '';
	public $header_height = 0;
	public $header_padding = 0;
	public $widget_border_width = 0;
	public $show_need_help = 1;
	public $show_need_help_timeout = 24;
	
	public $support_joined = '';
	public $support_closed = '';
	public $pending_join = '';
	public $noonline_operators = '';
	public $noonline_operators_offline = '';
	
	public $show_voting = 1;
	public $department_title = '';
	public $department_select = '';

	public $buble_visitor_background = ''; //BFE9F9
	public $buble_visitor_title_color = '';//99BE7B
	public $buble_visitor_text_color = ''; //333333

	public $buble_operator_background = ''; //DCF2FA
	public $buble_operator_title_color = '';//8EC1D9
	public $buble_operator_text_color = ''; //333333

    public $hide_ts = 0;
    public $widget_response_width = 0;

	public $hide_add = false;
	public $hide_delete = false;

}

?>