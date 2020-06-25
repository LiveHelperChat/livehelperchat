<?php

class erLhAbstractModelWidgetTheme {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_widget_theme';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

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
			'show_need_help_delay'		=> $this->show_need_help_delay,
			'show_status_delay'		    => $this->show_status_delay,
			'custom_status_css'			=> $this->custom_status_css,
			'custom_container_css'		=> $this->custom_container_css,
			'custom_widget_css'			=> $this->custom_widget_css,
			'custom_popup_css'			=> $this->custom_popup_css,
			'need_help_header'			=> $this->need_help_header,
			'need_help_text'			=> $this->need_help_text,
			'bot_status_text'			=> $this->bot_status_text,
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
			'pending_join_queue'		=> $this->pending_join_queue,
			'noonline_operators'		=> $this->noonline_operators,
			'noonline_operators_offline'=> $this->noonline_operators_offline,
			'show_need_help'            => $this->show_need_help,
			'show_need_help_timeout'    => $this->show_need_help_timeout,
			'modern_look'               => $this->modern_look,

			'show_voting'               => $this->show_voting,
			'department_title'          => $this->department_title,
			'department_select'         => $this->department_select,
			'buble_visitor_background'  => $this->buble_visitor_background,
			'buble_visitor_title_color' => $this->buble_visitor_title_color,
			'buble_visitor_text_color'  => $this->buble_visitor_text_color,
			'buble_operator_background' => $this->buble_operator_background,
			'buble_operator_title_color'=> $this->buble_operator_title_color,
			'buble_operator_text_color' => $this->buble_operator_text_color,

			'bot_configuration'         => $this->bot_configuration,
			'notification_configuration'=> $this->notification_configuration,

			'hide_ts'                   => $this->hide_ts,
			'widget_response_width'     => $this->widget_response_width,
			'modified'                  => $this->modified,
		);

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('lhabstract.erlhabstractmodelwidgettheme.getstate',array('state' => & $stateArray, 'object' => & $this));
		
		return $stateArray;
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

			if ($attr == 'notification_icon') {
			    $noteConfigurationArray = $this->notification_configuration_array;
                $noteConfigurationArray[$attr.'_path'] = $this->{$attr.'_path'};
                $noteConfigurationArray[$attr] = $this->{$attr};

                $this->notification_configuration_array = $noteConfigurationArray;
            }

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

			if ($attr == 'notification_icon') {
                $noteConfigurationArray = $this->notification_configuration_array;
                $noteConfigurationArray[$attr.'_path'] = '';
                $noteConfigurationArray[$attr] = '';
                $this->notification_configuration_array = $noteConfigurationArray;
            }
		}		
	}
	
	public function getModuleTranslations()
	{
	    $metaData = array(
	    'path' => array(
            array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
	        array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes'))
        ), 'permission_delete' => array('module' => 'lhtheme','function' => 'administratethemes'),'permission' => array('module' => 'lhtheme','function' => 'administratethemes'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget themes'));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_themes', array('object_meta_data' => & $metaData));
	    
	    return $metaData;
	}



	public function afterSave()
    {
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
            'notification_icon',
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

	public function __get($var)
	{
	   switch ($var) {
	   	
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;

           case 'custom_status_css_front':
               $attr = str_replace('_front', '', $var);
               $this->$var = false;
               if ($this->$attr != '') {
                   $this->$var =  str_replace($this->replace_array['search'], $this->replace_array['replace'], $this->$attr);
               }
               return $this->$var;
               break;

           case 'replace_array':

               $host = '//'.$_SERVER['HTTP_HOST'];

               $this->replace_array = array(
                   'search' => array(
                       '{{host}}',
                       '{{logo_image_url}}',
                       '{{minimize_image_url}}',
                       '{{restore_image_url}}',
                       '{{close_image_url}}',
                       '{{popup_image_url}}',
                       '{{operator_image_url}}',
                       '{{copyright_image_url}}',
                       '{{need_help_image_url}}',
                       '{{online_image_url}}',
                       '{{offline_image_url}}',
                   ),
                   'replace' => array(
                       $host,
                       $this->logo_image_url,
                       $this->minimize_image_url,
                       $this->restore_image_url,
                       $this->close_image_url,
                       $this->popup_image_url,
                       $this->operator_image_url,
                       $this->copyright_image_url,
                       $this->need_help_image_url,
                       $this->online_image_url,
                       $this->offline_image_url,
                   ));
               return $this->replace_array;
               break;

	   	case 'notification_icon_url':
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
	   	           $this->$var =  ($this->{$attr.'_path'} != '' ? '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) . '/' . $this->{$attr.'_path'} . $this->$attr;
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
	   				$this->$var = '<img src="'.($this->{$attr.'_path'} != '' ? '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'.$this->{$attr.'_path'} . $this->$attr.'"/>';
	   			}
	   			return $this->$var;
	   		break;

       case 'notification_icon':
       case 'notification_icon_path':
           $configurationArray = $this->notification_configuration_array;
           if (isset($configurationArray[$var]) && $configurationArray[$var] != '') {
               $this->$var = $configurationArray[$var];
           } else {
               $this->$var = '';
           }
           return $this->$var;
           break;

       case 'notification_icon_url_img':
           $attr = str_replace('_url_img', '', $var);
           $configurationArray = $this->notification_configuration_array;
           if (isset($configurationArray[$attr]) && $configurationArray[$attr] != '') {
               $this->$var = '<img src="'.($this->{$attr.'_path'} != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'.$this->{$attr.'_path'} . $configurationArray[$attr].'"/>';
           } else {
               $this->$var = false;
           }
           return $this->$var;
           break;

       case 'bot_configuration_array':
       case 'notification_configuration_array':
           $attr = str_replace('_array','',$var);
           if (!empty($this->{$attr})) {
               $jsonData = json_decode($this->{$attr},true);
               if ($jsonData !== null) {
                   $this->{$var} = $jsonData;
               } else {
                   $this->{$var} = array();
               }
           } else {
               $this->{$var} = array();
           }
           return $this->{$var};
           break;
	   		
	   	default:
	   		break;
	   }
	}

	public function beforeRemove()
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
    }

    public function beforeUpdate()
    {
        $this->bot_configuration = json_encode($this->bot_configuration_array);
        $this->notification_configuration = json_encode($this->notification_configuration_array);
        $this->modified = time();
    }

    public function beforeSave()
    {
        $this->bot_configuration = json_encode($this->bot_configuration_array);
        $this->notification_configuration = json_encode($this->notification_configuration_array);
        $this->modified = time();
    }
    
	public function dependCss()
    {
		return '<link rel="stylesheet" type="text/css" href="'.erLhcoreClassDesign::design('css/colorpicker.css').'" />';
	}

	public function dependJs()
    {
		return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/colorpicker.js;js/ace/ace.js').'"></script>';
	}

    public function dependFooterJs()
    {
        return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.lhc.theme.js').'"></script>';
    }
	
	public function customForm()
    {
		return 'widget_theme.tpl.php';
	}

	public function translate() {
        $chatLocale = null;
        $chatLocaleFallback = erConfigClassLhConfig::getInstance()->getDirLanguage('content_language');

        // Detect user locale
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $parts = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $languages = explode(',',$parts[0]);
            if (isset($languages[0])) {
                $chatLocale = $languages[0];
            }
        }

        $attributesDirect = array(
            'pending_join_queue',
            'bot_status_text',
            'support_joined',
            'support_closed',
            'pending_join',
            'noonline_operators',
            'noonline_operators_offline',
            'department_title',
            'department_select',
            'explain_text',
            'need_help_text',
            'need_help_header',
        );

        $translatableAttributes = array_merge(array(
            'custom_start_button_offline',
            'custom_start_button_bot',
            'custom_start_button',
            'inject_html',
            'custom_html_status',
            'custom_html_header_body',
            'custom_html_header',
            'custom_html_widget_bot',
            'custom_html_bot',
            'custom_html_widget',
            'custom_html',
            'thank_feedback',
            'placeholder_message',
            'need_help_html',
        ),$attributesDirect);

        $attributes = $this->bot_configuration_array;

        foreach ($translatableAttributes as $attr) {
            if (isset($attributes[$attr . '_lang'])) {

                $translated = false;

                if ($chatLocale !== null) {
                    foreach ($attributes[$attr . '_lang'] as $attrTrans) {
                        if (in_array($chatLocale, $attrTrans['languages']) && $attrTrans['content'] != '') {
                            $attributes[$attr] = $attrTrans['content'];
                            $translated = true;
                            break;
                        }
                    }
                }

                if ($translated == false) {
                    foreach ($attributes[$attr . '_lang'] as $attrTrans) {
                        if (in_array($chatLocaleFallback, $attrTrans['languages']) && $attrTrans['content'] != '') {
                            $attributes[$attr] = $attrTrans['content'];
                            $translated = true;
                            break;
                        }
                    }
                }

                if ($translated === true && in_array($attr,$attributesDirect)) {
                    $this->$attr = $attributes[$attr];
                }
            }
        }

        $this->bot_configuration_array = $attributes;
    }

   	public $id = null;
	public $name = '';
	public $onl_bcolor = '0c8fc4';
	public $text_color = '000000';
	public $bor_bcolor = 'e3e3e3';
	public $online_image = '';
	public $offline_image = '';
	public $online_image_path = '';
	public $offline_image_path = '';
	public $header_background = '525252';
	public $need_help_bcolor = '';
	public $need_help_hover_bg = '';
	public $need_help_image = '';	
	public $need_help_tcolor = '';
	public $need_help_border = '';
	public $need_help_close_bg = '';
	public $need_help_close_hover_bg = '';
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
	public $widget_border_width = 1;
	public $show_need_help = 1;
	public $show_need_help_timeout = 24;
	public $show_need_help_delay = 0;
	public $show_status_delay = 0;

	public $support_joined = '';
	public $bot_status_text = '';
	public $support_closed = '';
	public $pending_join = '';
	public $pending_join_queue = '';
	public $noonline_operators = '';
	public $noonline_operators_offline = '';
	public $notification_configuration = '';

	public $bot_configuration = '';

	public $show_voting = 1;
	public $modern_look = 1;
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

    // Theme modified time. We will use this attribute for E-Tag
    public $modified = 0;

	public $hide_add = false;
	public $hide_delete = false;
}

?>