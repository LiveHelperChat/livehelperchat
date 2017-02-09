<?php

class erLhAbstractModelAdminTheme {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_admin_theme';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function removeThis()
    {
        $attributes = array (
            'static_content',
            'static_js_content',
            'static_css_content'
        );
    
        foreach ($attributes as $attr) {
            foreach ($this->{$attr . '_array'} as $key => $data) {
                $this->removeResource($attr, $key);
            }
        }
    
        erLhcoreClassAbstract::getSession()->delete($this);
    }
    
    
	public function getState()
	{
		$stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'header_content' => $this->header_content,
            'header_css' => $this->header_css,
            'static_content' => $this->static_content,
            'static_js_content' => $this->static_js_content,
            'static_css_content' => $this->static_css_content,
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}

	/**
	 * Removes attributes if required
	 * */
	public function removeResource($scope, $key) {
	    $content = $this->{$scope . '_array'};
	
	    if (isset($content[$key]['file']) && isset($content[$key]['file_dir'])) {
	
	        if (file_exists($content[$key]['file_dir'] . $content[$key]['file']))
	        {
	            unlink($content[$key]['file_dir'] . $content[$key]['file']);
	            erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/storageadmintheme/',str_replace('var/storageadmintheme/','',$content[$key]['file_dir']));
	        }

	        $std = new stdClass();
	        $std->name = $content[$key]['file'];
	        
	        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.remove_file', array('chat_file' => & $std, 'files_path_storage' => 'images_path' ));
	        
	        // Remove removed attribute
	        unset($content[$key]);
	
	        // Set attr
	        $this->{$scope . '_array'} = $content;
	        $this->{$scope} = json_encode($content,JSON_HEX_APOS);
	        $this->saveThis();
	    }
	}
		
	public function __get($var)
	{
	    switch ($var) {
	
	        case 'static_content_array':
	            $this->static_content_array = array();
	            if ($this->static_content != '') {
	                $this->static_content_array = json_decode($this->static_content,true);
	            }
	            return $this->static_content_array;
	            break;
	
	        case 'static_css_content_array':
	            $this->static_css_content_array = array();
	            if ($this->static_css_content != '') {
	                $this->static_css_content_array = json_decode($this->static_css_content,true);
	            }
	            return $this->static_css_content_array;
	            break;
	
	        case 'static_js_content_array':
	            $this->static_js_content_array = array();
	            if ($this->static_js_content != '') {
	                $this->static_js_content_array = json_decode($this->static_js_content,true);
	            }
	            return $this->static_js_content_array;
	            break;
	
	        case 'replace_array_static':
	        case 'replace_array_css':
	        case 'replace_array_js':
	            $varAttr = array(
    	            'replace_array_static' => 'static_content_array',
    	            'replace_array_css' => 'static_css_content_array',
    	            'replace_array_js' => 'static_js_content_array',
	            );
	
	            $return = array('search' => array(), 'replace' => array());
	
	            foreach ($this->{$varAttr[$var]} as $content) {
	                $return['search'][] = '{{'.$content['name'].'}}';
	                $return['replace'][] = ($content['file_dir'] != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) . '/' . $content['file_dir'] . $content['file'];
	            }
	
	            $this->$var = $return;
	
	            return $this->$var;
	            break;
	
	        case 'replace_array_all':
	            $this->replace_array_all = array_merge_recursive($this->replace_array_static,$this->replace_array_css,$this->replace_array_js);
	            return $this->replace_array_all;
	            break;

            case 'header_content_front':
                $this->header_content_front = '';
                if ($this->header_content != '') {
                    $this->header_content_front = str_replace($this->replace_array_all['search'], $this->replace_array_all['replace'], $this->header_content);
                }
                return $this->header_content_front;
                break;
	        default:
	            ;
	            break;
	    }
	}

	public $id = null;
	public $name = '';
	public $header_content = '';
	public $header_css = '';
	public $static_content = '';
	public $static_js_content = '';
	public $static_css_content = '';
}