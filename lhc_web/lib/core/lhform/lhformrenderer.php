<?php

class erLhcoreClassFormRenderer {
		
	
	private static $errors = array();
	private static $collectedInfo = array();
	private static $isCollected = false;
	private static $collectedObject = false;
	private static $mainEmail = false;
	private static $customFields = array();

	
	public static function setCollectedObject($object) {
		self::$collectedObject = $object;
	}
	
    public static function renderForm($form, $asAdmin = false) {
    	$contentForm = $form->content;

    	$inputFields = array();
    	preg_match_all('/\[\[[input|textarea|combobox|range](.*?)\]\]/i', $contentForm, $inputFields);
    	foreach ($inputFields[0] as $inputDefinition) {
    		$content = self::processInput($inputDefinition, $asAdmin);
    		$contentForm = str_replace($inputDefinition,$content,$contentForm);    		
    	};

    	if (isset($_GET['identifier']) && !empty($_GET['identifier'])) {
    		$contentForm .= "<input type=\"hidden\" name=\"identifier\" value=\"".htmlspecialchars(rawurldecode($_GET['identifier']))."\" />";
    	} elseif (isset($_POST['identifier']) && !empty($_POST['identifier'])) {
    		$contentForm .= "<input type=\"hidden\" name=\"identifier\" value=\"".htmlspecialchars($_POST['identifier'])."\" />";
    	} elseif (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    		$contentForm .= "<input type=\"hidden\" name=\"identifier\" value=\"".htmlspecialchars($_SERVER['HTTP_REFERER'])."\" />";
    	}

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.on_form_render',array('form' => & $form, 'errors' => & self::$errors));

    	if ( empty(self::$errors) && ezcInputForm::hasPostData()) {
    		self::$isCollected = true;
            self::collectCustomFields();
    	}

    	return $contentForm;    	
    }

    public static function collectCustomFields() {

	    $collectedData = array();

        if (isset($_POST['custom_fields']) && !empty($_POST['custom_fields'])) {
            $customFields = json_decode($_POST['custom_fields'], true);
            foreach ($customFields as $customField) {
                $valueStore = $customField['value'];
                if (isset($customField['encrypted']) && $customField['encrypted'] == true) {
                    try {
                        $valueStore = erLhcoreClassChatValidator::decryptAdditionalField($valueStore);
                    } catch (Exception $e) {
                        $valueStore = $e->getMessage();
                    }
                }

                $collectedData[] = array(
                    'identifier' => str_replace(' ','_',strtolower($customField['name'])),
                    'name' => $customField['name'],
                    'value' => $valueStore
                );
            }
        }

        if (isset($_POST['jsvar']) && !empty($_POST['jsvar'])) {
            foreach ($_POST['jsvar'] as $key => $value) {
                $jsVar = erLhAbstractModelChatVariable::fetch($key);
                if ($jsVar instanceof erLhAbstractModelChatVariable) {
                    $val = $value;
                    if ($jsVar->type == 0) {
                        $val = (string)$val;
                    } elseif ($jsVar->type == 1) {
                        $val = (int)$val;
                    } elseif ($jsVar->type == 2) {
                        $val = (float)$val;
                    } elseif ($jsVar->type == 3) {
                        try {
                            $val = erLhcoreClassChatValidator::decryptAdditionalField($val);
                        } catch (Exception $e) {
                            $val = $e->getMessage();
                        }
                    }
                    $collectedData[] = array(
                        'identifier' => $jsVar->var_identifier,
                        'name' => $jsVar->var_name,
                        'value' => $val
                    );
                }
            }
        }

        self::$customFields = $collectedData;
    }

    public static function getCustomFields() {
	    return self::$customFields;
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
    
    public static function setCollectedInformation($information) {
    	self::$collectedInfo = $information;
    }
    
    public static function processInput($inputDefinition, $asAdmin = false) {
    	    	
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

        $paramsParsed['as_admin'] = $asAdmin;

    	return call_user_func('erLhcoreClassFormRenderer::renderInputType'.ucfirst($paramsParsed['type']),$paramsParsed);
    }
    
    public static function renderInputTypeRange($params) {
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	 
    	$value = '';
    	if (ezcInputForm::hasPostData()) {
    
    		$validationFields = array();
    		$validationFields[$params['name'].'From'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    		$validationFields[$params['name'].'Till'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    
    		if ( !$form->hasValidData( $params['name'].'From' ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name'].'From'} == '')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name'].'From').' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'].'From' )) {
    			$valueFrom = $form->{$params['name'].'From'};
    			self::$collectedInfo[$params['name'].'From'] = array('definition' => $params,'value' => $form->{$params['name'].'From'});
    		}
    
    		if ( !$form->hasValidData( $params['name'].'Till' ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name'].'Till'} == '')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name'].'Till').' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'].'Till' )) {
    			$valueTill = $form->{$params['name'].'Till'};
    			self::$collectedInfo[$params['name'].'Till'] = array('definition' => $params,'value' => $form->{$params['name'].'Till'});
    		}
    
    	} else {
    		if (isset(self::$collectedInfo[$params['name'].'From']['value'])) {
    			$valueFrom = self::$collectedInfo[$params['name'].'From']['value'];
    		} else {
    			$valueFrom = (isset($params['StartValue']) ? $params['StartValue'] : '');
    		}
    
    		if (isset(self::$collectedInfo[$params['name'].'Till']['value'])) {
    			$valueTill = self::$collectedInfo[$params['name'].'Till']['value'];
    		} else {
    			$valueTill = (isset($params['EndValue']) ? $params['EndValue'] : '');
    		}
    	}
    	$return = '';
    	 
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';
    	 
    	$valueFromDefault = $valueFrom;
    	if ($valueFromDefault == '') {
    		$valueFromDefault = $params['min'];
    	}
    	 
    	$valueTillDefault = $valueTill;
    	if ($valueTillDefault == '') {
    		$valueTillDefault = $params['max'];
    	}
    	 
    	$return .= "<div ng-init=\"ng{$params['name']}From=".htmlspecialchars($valueFromDefault,ENT_QUOTES).";ng{$params['name']}Till=".htmlspecialchars($valueTillDefault,ENT_QUOTES)."\"><input type=\"text\" id=\"id_{$params['name']}From\" ng-model=\"ng{$params['name']}From\" name=\"{$params['name']}From\" {$additionalAttributes} {$placeholder} value=\"".htmlspecialchars($valueFrom)."\" />";
    	$return .= "<input class=\"form-control form-control-sm\" type=\"text\" id=\"id_{$params['name']}Till\" ng-model=\"ng{$params['name']}Till\" name=\"{$params['name']}Till\" {$additionalAttributes} {$placeholder} value=\"".htmlspecialchars($valueTill)."\" /></div>";
    	 
    	if ($params['usejquislider'] && $params['usejquislider'] == 'true') {
    
    		$step = isset($params['step']) ? 'step:'.$params['step'].',' : '';
    
    		$return = '<div class="hide">'.$return.'</div><script>
			$(function() {
					$( "#'.$params['name'].'Slider" ).slider({
						range: true,
						values: [ '.htmlspecialchars($valueFromDefault).', '.htmlspecialchars($valueTillDefault).' ],
						min:'.$params['min'].',
						max:'.$params['max'].',
						'.$step.'
				        slide: function (event, ui) {
				            $(\'#id_'.$params['name'].'From\').val(ui.values[0]).trigger(\'input\');
				            $(\'#id_'.$params['name'].'Till\').val(ui.values[1]).trigger(\'input\');
				        }
					});
			});
			</script>';
    	}
    	 
    	return $return;
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
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {    		
    			$value = $form->{$params['name']};
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : '');
    		}
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
    	return "<input class=\"form-control form-control-sm\" type=\"text\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} value=\"".htmlspecialchars($value)."\" />";
    }

    public static function renderInputTypeHidden($params) {
    	$additionalAttributes = self::renderAdditionalAtrributes($params);

    	$value = '';
    	if (ezcInputForm::hasPostData()) {

    		$validationFields = array();
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );

    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();

    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name']} == '')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = $form->{$params['name']};
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}

    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    		    if (isset($params['value']) && strpos($params['value'],'prefill|') !== false) {
    		        $varName = str_replace('prefill|','',$params['value']);
                    if (isset($_GET['prefill'][$varName])) {
                        $value = $_GET['prefill'][$varName];
                    } else {
                        $value = '';
                    }
                } else {
                    $value = (isset($params['value']) ? $params['value'] : '');
                }
    		}
    	}

        $returnAppend = $return = "";
        if (isset($params['as_admin']) && $params['as_admin'] == true) {
            $return = "<div class='form-group'><label class='font-weight-bold'>" . htmlspecialchars($params['name_literal']) . "</label>";
            $returnAppend = "</div>";
        }

    	return $return . "<input class=\"form-control form-control-sm\" type=\"". ((isset($params['as_admin']) && $params['as_admin'] == true) ? "text" : "hidden") ."\" name=\"{$params['name']}\" {$additionalAttributes} value=\"".htmlspecialchars($value)."\" />" . $returnAppend;
    }
        
    public static function renderInputTypeEmail($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name']} == '')) {    		
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = $form->{$params['name']};
    			self::$collectedInfo[$params['name']] = array('main' => (isset($params['main']) && $params['main'] == 'true'),'definition' => $params,'value' => $form->{$params['name']});
    			    			
    			// It's main form e-mail
    			if (self::$collectedInfo[$params['name']]['main'] == true) {
    			    self::$mainEmail = self::$collectedInfo[$params['name']]['value'];
    			}
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : '');
    		}
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
    	return "<input class=\"form-control form-control-sm\" type=\"text\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} value=\"".htmlspecialchars($value)."\" />";
    }
    
    
    
    public static function renderInputTypeNumber($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name']} == '')) {    		
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {    		
    			$value = $form->{$params['name']};
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : '');
    		}
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
    	return "<input class=\"form-control form-control-sm\" type=\"text\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} value=\"".htmlspecialchars($value)."\" />";
    }
    
    
    public static function renderInputTypeDate($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && $form->{$params['name']} == '')) {    		
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name']) && $form->{$params['name']} != '') {   
    			 		
    			$separator = strpos($form->{$params['name']}, '-') !== false ? '-' : '/';
    			$parts = explode($separator, $form->{$params['name']});

    			$pos = explode(',', $params['pos']);
    			
    			if (count($parts) == 3 && checkdate($parts[$pos[1]], $parts[$pos[2]], $parts[$pos[0]])){    			
	    			$value = $form->{$params['name']};
	    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    			} else {
    				$value = htmlspecialchars($form->{$params['name']});
    				self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','invalid date format');
    			}
    			
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : '');
    		}
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
    	return "<input class=\"form-control form-control-sm\" type=\"text\" name=\"{$params['name']}\" id=\"id_{$params['name']}\" {$additionalAttributes} {$placeholder} value=\"".htmlspecialchars($value)."\" /><script>$(function() {\$('#id_{$params['name']}').fdatepicker({format: '{$params['format']}'});});</script>";
    }
    
    public static function renderInputTypeTranslate($params)
    {        
        if (isset($params['context']) && isset($params['text'])) {           
            return erTranslationClassLhTranslation::getInstance()->getTranslation($params['context'],$params['text']);
        }
    }
    
    public static function renderInputTypeCombobox($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		 
    		$validationFields = array();
    		$validationFields[$params['name']] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    		 
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && ($form->{$params['name']} == '' || (isset($params['default']) && $params['default'] == $form->{$params['name']})))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = htmlspecialchars($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		    		 
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['default']) ? $params['default'] : '');
    		}
    	}
    	
    	$options = array();
    	if (isset($params['from']) && isset($params['till'])){
    		for ($i = $params['from']; $i <= $params['till']; $i++) {
    			$isSelected= $value == $i ? 'selected="selected"' : '';
    			$options[] = "<option =\"".htmlspecialchars($i)."\" {$isSelected}>".htmlspecialchars($i).'</option>';
    		}
    	} else {    	
	    	foreach (explode('#',$params['options']) as $option) {
	    		$isSelected= $value == $option ? 'selected="selected"' : '';
	    		$options[] = "<option =\"".htmlspecialchars($option)."\" {$isSelected}>".htmlspecialchars($option).'</option>';
	    	};
    	}
    	    	
    	return "<select class=\"form-control form-control-sm\" {$additionalAttributes} name=\"{$params['name']}\">".implode('', $options)."</select>";
    }
    
    
    public static function renderInputTypeYear($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		 
    		$validationFields = array();
    		$validationFields[$params['name']] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => $params['from']) );
    		 
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && ($form->{$params['name']} == '' || (isset($params['default']) && $params['default'] == $form->{$params['name']})))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = htmlspecialchars($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		    		 
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['default']) ? htmlspecialchars($params['default']) : '');
    		}
    	}
    	
		$options = array();
		$yearTill = (isset($params['till']) ? $params['till'] : date('Y'));
    	for ( $i = $yearTill; $i >= $params['from']; $i--) {    		    	
    		$isSelected= $value == $i ? 'selected="selected"' : '';
    		$options[] = "<option =\"".htmlspecialchars($i)."\" {$isSelected}>".htmlspecialchars($i).'</option>';    		
    	}
    		    	
    	return "<select class=\"form-control form-control-sm\" {$additionalAttributes} name=\"{$params['name']}\">".implode('', $options)."</select>";
    }
    
    public static function renderInputTypeMonth($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		 
    		$validationFields = array();
    		$validationFields[$params['name']] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1,'max_range' => 12) );
    		 
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && ($form->{$params['name']} == '' || (isset($params['default']) && $params['default'] == $form->{$params['name']})))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = $form->{$params['name']};
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    		    		 
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['default']) ? $params['default'] : '');
    		}
    	}
    	
		$options = array();
			
    	for ( $i = 1; $i <= 12; $i++) {    		    	
    		$isSelected= $value == $i ? 'selected="selected"' : '';
    		$options[] = "<option =\"".htmlspecialchars($i)."\" {$isSelected}>".htmlspecialchars($i).'</option>';    		
    	}
    		    	
    	return "<select class=\"form-control form-control-sm\" {$additionalAttributes} name=\"{$params['name']}\">".implode('', $options)."</select>";
    }
    
    
    
    public static function renderInputTypeCheckbox($params) {   
    	
    	$isChecked = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'boolean' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) && (isset($params['required']) && $params['required'] == 'required')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$isChecked = ' checked="checked" ';
    			self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => true);
    		} else {
    			self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => false);
    		}    		    		
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){    	
    			$isChecked = self::$collectedInfo[$params['name']]['value'] == true ? ' checked="checked" ' : '';
    		} else {
    			$isChecked = isset($params['value']) && $params['value'] == 'checked' ? ' checked="checked" ' : ''; 
    		}
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
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = $form->{$params['name']};
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => $form->{$params['name']});
    		}
    	
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : '');
    		}
    	}    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';
    	
    	return "<textarea class=\"form-control form-control-sm\" name=\"{$params['name']}\" {$placeholder}>" . htmlspecialchars($value) . "</textarea>";
    }
    
    public static function renderAdditionalAtrributes($params) {
    	$additionalAttributes = array();
    	foreach ($params as $type => $value) {
    		if (strpos($type, 'ng-') !== false) {
    			$additionalAttributes[] = $type.'="'.htmlspecialchars($value).'"';
    		}
    	};     	
    	return implode(' ', $additionalAttributes);
    }
    
    public static function renderInputTypeFile($params) {

    	$downloadLink = '';
    	if (ezcInputForm::hasPostData()) {
	    	if (!erLhcoreClassSearchHandler::isFile($params['name']) && (isset($params['required']) && $params['required'] == 'required')){
	    		self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
	    	} elseif (erLhcoreClassSearchHandler::isFile($params['name'])) {
	    		self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => $_FILES[$params['name']]);
	    	} 
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$valueContent = self::$collectedInfo[$params['name']]['value'];
    			$downloadLink = "<a href=\"http://".$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('form/download').'/'.self::$collectedObject->id.'/'.self::$collectedObject->hash.'/'.$params['name']."\">Download (".htmlspecialchars($valueContent['name']).")</a>";
    		}
    	}
    	
    	return "{$downloadLink}<input type=\"file\" name=\"{$params['name']}\" />";
    }

    public static function storeCollectedInformation($form, $collectedInformation, $customFields = []) {
    	
    	$formCollected = new erLhAbstractModelFormCollected();
    	$formCollected->ip = erLhcoreClassIPDetect::getIP();
    	$formCollected->ctime = time();
    	$formCollected->form_id = $form->id;  
    	$formCollected->identifier = substr(isset($_POST['identifier']) ? $_POST['identifier'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),0,250);
        $formCollected->saveThis();

        if (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && isset($_POST['hash']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_POST['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            $formCollected->chat_id = $chat->id;

            // Store as message to visitor
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand','Information collected. [baseurl]form/viewcollected/'.$formCollected->id.'[/baseurl]');
            $msg->chat_id = $chat->id;
            $msg->user_id = -1;
            $msg->time = time();
            $msg->name_support = $chat->nick;
            $msg->saveThis();

            // Update last user msg time so auto responder work's correctly
            $chat->last_op_msg_time = $chat->last_user_msg_time = time();
            $chat->last_msg_id = $msg->id;

            // All ok, we can make changes
            $chat->updateThis(array('update' => array('last_msg_id', 'last_op_msg_time', 'last_user_msg_time')));
        }

    	// Finish collect information
    	foreach ($collectedInformation as $fieldName => & $params) {
    		
    		if ($params['definition']['type'] == 'file') {    
    						
    			$dir = 'var/storageform/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$formCollected->id.'/';
    			
    			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.fill.file_path',array('path' => & $dir, 'storage_id' => $formCollected->id));
    			
    			erLhcoreClassFileUpload::mkdirRecursive( $dir );
    			    			
    			$file = erLhcoreClassSearchHandler::moveUploadedFile($params['definition']['name'],$dir);
    			
	    		$params['filename'] = $file;
	    		$params['filepath'] = $dir;
	    		
	    		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.fill.store_file',array('file_params' => & $params));
    		}
    	}
    	
    	$formCollected->content = serialize($collectedInformation);
        $formCollected->custom_fields = json_encode($customFields);
    	$formCollected->saveThis();

    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.filled',array('form' => & $formCollected));
    	
    	// Inform user about filled form
    	erLhcoreClassChatMail::informFormFilled($formCollected,array('email' => self::$mainEmail));
    }
    
}

?>