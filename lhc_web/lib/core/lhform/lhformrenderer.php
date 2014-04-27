<?php

class erLhcoreClassFormRenderer {
		
	
	private static $errors = array();
	private static $collectedInfo = array();
	private static $isCollected = false;

    public static function renderForm($form) {    	 	 
    	$contentForm = $form->content;

    	$inputFields = array();
    	preg_match_all('/\[\[[input|textarea|combobox](.*?)\]\]/i', $contentForm, $inputFields);
    	foreach ($inputFields[0] as $inputDefinition) {
    		$content = self::processInput($inputDefinition);    		
    		$contentForm = str_replace($inputDefinition,$content,$contentForm);    		
    	};

    	if ( empty(self::$errors)) {
    		self::$isCollected = true;
    	}
    	
    	return $contentForm;    	
    }    

    public static function getErrors() {
    	return self::$errors;
    }

    public static function getCollectedInfo() {
    	return self::$collectedInfo;
    }
    
    public static function isCollected() {
    	return self::$isCollected;
    }
    
    public static function processInput($inputDefinition) {
    	    	
    	$inputDefinition = str_replace(array('[[',']]'), '', $inputDefinition);
    	$paramsInput = explode('||', $inputDefinition);    	
    	$defaultType = array_shift($paramsInput);

    	$paramsParsed = array();
    	foreach ($paramsInput as $param) {
    		$paramsItem = explode('=', $param);
    		$paramsParsed[$paramsItem[0]] = $paramsItem[1];
    	}
        	
    	if (!isset($paramsParsed['type'])) {
    		$paramsParsed['type'] = $defaultType;
    	}    	
    	
    	return call_user_func('erLhcoreClassFormRenderer::renderInputType'.ucfirst($paramsParsed['type']),$paramsParsed);
    }
    
    
    public static function renderInputTypeText($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name']} == '')) {    		
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' is required';
    		} elseif ($form->hasValidData( $params['name'] )) {    		
    			$value = htmlspecialchars($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		
    	} else {
    		$value = (isset($params['value']) ? htmlspecialchars($params['value']) : '');
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
    	return "<input type=\"text\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} value=\"".$value."\" />";    	
    }
    
    public static function renderInputTypeCombobox($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		 
    		$validationFields = array();
    		$validationFields[$params['name']] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    		 
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && ($form->{$params['name']} == '' || $params['default'] == $form->{$params['name']}))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' is required';
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = htmlspecialchars($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		    		 
    	} else {
    		$value = (isset($params['default']) ? htmlspecialchars($params['default']) : '');
    	}
    	    	
    	
    	$options = [];
    	foreach (explode('#',$params['options']) as $option) {
    		$isSelected= $value == $option ? 'selected="selected"' : '';
    		$options[] = "<option =\"".htmlspecialchars($option)."\" {$isSelected}>".htmlspecialchars($option).'</option>';
    	};
    	    	
    	return "<select {$additionalAttributes} name=\"{$params['name']}\">".implode('', $options)."</select>";  	
    }
    
    public static function renderInputTypeCheckbox($params) {   
    	
    	$isChecked = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'boolean' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) && (isset($params['required']) && $params['required'] == 'required')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' is required';
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$isChecked = ' checked="checked" ';
    			self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => true);
    		} else {
    			self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => false);
    		}    		    		
    		
    	} else {
    		$isChecked = isset($params['value']) && $params['value'] == 'checked' ? ' checked="checked" ' : ''; 
    	}
    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	return "<input type=\"checkbox\" name=\"{$params['name']}\"{$isChecked} {$additionalAttributes} value=\"on\" />";
    }
    
    public static function renderInputTypeTextarea($params) {    	
    	
    	$value = '';
    	if (ezcInputForm::hasPostData()) {
    	
    		$validationFields = array();
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    	
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    	
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name']} == '')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' is required';
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = htmlspecialchars($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    	
    	} else {
    		$value = (isset($params['value']) ? htmlspecialchars($params['value']) : '');
    	}    	
    	
    	return "<textarea name=\"{$params['name']}\">" . $value . "</textarea>";
    }
    
    public static function renderAdditionalAtrributes($params) {
    	$additionalAttributes = [];    	    	
    	foreach ($params as $type => $value) {
    		if (strpos($type, 'ng-') !== false) {
    			$additionalAttributes[] = $type.'="'.htmlspecialchars($value).'"';
    		}
    	};     	
    	return implode(' ', $additionalAttributes);
    }
    
    public static function renderInputTypeFile($params) {

    	if (!erLhcoreClassSearchHandler::isFile($params['name']) && (isset($params['required']) && $params['required'] == 'required')){
    		self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' is required';
    	} elseif (erLhcoreClassSearchHandler::isFile($params['name'])) {
    		self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => $_FILES[$params['name']]);
    	}
    	
    	return "<input type=\"file\" name=\"{$params['name']}\" />";
    }
    
    public static function storeCollectedInformation($form, $collectedInformation) {
    	
    	$formCollected = new erLhAbstractModelFormCollected();
    	$formCollected->ip = erLhcoreClassIPDetect::getIP();
    	$formCollected->ctime = time();
    	$formCollected->form_id = $form->id;
    	
    	$formCollected->saveThis();
    	
    	// Finish collect information
    	foreach ($collectedInformation as $fieldName => & $params) {
    		
    		if ($params['definition']['type'] == 'file') {    
    						
    			$dir = 'var/storageform/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$formCollected->id.'/';
    			erLhcoreClassFileUpload::mkdirRecursive( $dir );
    			    			
    			$file = erLhcoreClassSearchHandler::moveUploadedFile($params['definition']['name'],$dir);
    			
	    		$params['filename'] = $file;
	    		$params['filepath'] = $dir;
    		}
    	}
    	
    	$formCollected->content = serialize($collectedInformation);    	
    	$formCollected->saveThis();
    }
    
}

?>