<?php

class erLhcoreClassFormRenderer {
		
	
	private static $errors = array();
	private static $errorsInternal = array();
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

        // Ignore previous errors if it's a form submit

        $inputFields = array();
        preg_match_all('/\[\[json_content_errors\{(.*?)\]\]/i', $contentForm, $inputFields);
        foreach ($inputFields[1] as $index => $inputDefinition) {
            $inputDefinition = json_decode('{'.$inputDefinition, true);
            if (!ezcInputForm::hasPostData()) {
                self::extractErrors($inputDefinition);
            }
            $contentForm = str_replace($inputFields[0][$index], '', $contentForm);
        }

        // Fields definition in JSON format
        $inputFields = array();
        preg_match_all('/\[\[json_content\{(.*?)\]\]/i', $contentForm, $inputFields);
        foreach ($inputFields[1] as $index => $inputDefinition) {
            $inputDefinition = json_decode('{'.$inputDefinition, true);
            $content = self::processInput($inputDefinition, $asAdmin);
            $contentForm = str_replace($inputFields[0][$index], $content, $contentForm);
        }

    	$inputFields = array();
    	preg_match_all('/\[\[[input|textarea|combobox|range](.*?)\]\]/i', $contentForm, $inputFields);
    	foreach ($inputFields[0] as $inputDefinition) {
    		$content = self::processInput($inputDefinition, $asAdmin);
    		$contentForm = str_replace($inputDefinition,$content,$contentForm);
    	}

    	if (isset($_GET['identifier']) && !empty($_GET['identifier'])) {
    		$contentForm .= "<input type=\"hidden\" name=\"identifier\" value=\"".htmlspecialchars(rawurldecode($_GET['identifier']))."\" />";
    	} elseif (isset($_POST['identifier']) && !empty($_POST['identifier'])) {
    		$contentForm .= "<input type=\"hidden\" name=\"identifier\" value=\"".htmlspecialchars($_POST['identifier'])."\" />";
    	} elseif (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    		$contentForm .= "<input type=\"hidden\" name=\"identifier\" value=\"".htmlspecialchars($_SERVER['HTTP_REFERER'])."\" />";
    	}

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.on_form_render',array('form' => & $form, 'errors_internal' => self::$errorsInternal, 'errors' => & self::$errors));

    	if ( empty(self::$errors) && empty(self::$errorsInternal) && ezcInputForm::hasPostData()) {
    		self::$isCollected = true;
            self::collectCustomFields();
    	}

        // Support {args.form.*} and {args.form_collected.*} variable substitution
        $translationArgs = array('form' => $form);
        if (self::$collectedObject !== false) {
            $formCollected = clone self::$collectedObject;
            $contentArray = $formCollected->content_array;
            if (isset($contentArray['lhc_field_changes'])) {
                if (isset($contentArray['lhc_field_changes']['field_history']) && is_array($contentArray['lhc_field_changes']['field_history'])) {
                    foreach ($contentArray['lhc_field_changes']['field_history'] as $field => $history) {
                        if (isset($history['user_id'])) {
                            $contentArray['lhc_field_changes']['field_history'][$field]['user'] = erLhcoreClassModelUser::fetch($history['user_id']);
                        }
                    }
                }
                if (isset($contentArray['lhc_field_changes']['modified_operators']) && is_array($contentArray['lhc_field_changes']['modified_operators'])) {
                    $modifierNames = [];
                    foreach ($contentArray['lhc_field_changes']['modified_operators'] as $userId => $modifierData) {
                        $user = erLhcoreClassModelUser::fetch($userId);
                        if ($user instanceof erLhcoreClassModelUser) {
                            $modifierNames[] = $user->name_official;
                        }
                    }
                    $contentArray['lhc_field_changes']['modifiers'] = implode(',', $modifierNames);
                }
                $formCollected->content_array = $contentArray;
            }
            $translationArgs['form_collected'] = $formCollected;
        }
        $contentForm = erLhcoreClassGenericBotWorkflow::translateMessage($contentForm, array('args' => $translationArgs));

    	return $contentForm;    	
    }

    public static function extractErrors($errorsDefinition)
    {
        foreach ($errorsDefinition as $errorLocation => $errorValue) {
            $path = explode('.', $errorLocation);
            if ($path[0] == 'chat_variable') {
                if (
                    (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && isset($_POST['hash']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_POST['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) ||
                    (isset($_GET['chat_id']) && is_numeric($_GET['chat_id']) && isset($_GET['hash']) && ($chat = erLhcoreClassModelChat::fetch($_GET['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_GET['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT)
                ) {
                    if (isset($chat->chat_variables_array[$path[1]]) && $chat->chat_variables_array[$path[1]] === true) {
                        self::$errors[] = $errorValue;
                    }
                }
            }
        }
    }

    public static function collectCustomFields() {

	    $collectedData = array();

        if (isset($_POST['custom_fields']) && !empty($_POST['custom_fields'])) {
            $customFields = json_decode($_POST['custom_fields'], true);
            if (is_array($customFields)) {
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
	    if (is_array($inputDefinition)) {
            $paramsParsed = $inputDefinition;
        } elseif (is_string($inputDefinition)) {
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
        }

        $paramsParsed['as_admin'] = $asAdmin;

        $method = 'renderInputType'.ucfirst(isset($paramsParsed['type']) ? $paramsParsed['type'] : '');
        if (!method_exists('erLhcoreClassFormRenderer', $method)) {
            return 'INVALID_FIELD';
        }

    	return call_user_func('erLhcoreClassFormRenderer::'.$method,$paramsParsed);
    }
    
    public static function renderInputTypeRange($params) {

    	$additionalAttributes = self::renderAdditionalAtrributes($params);

    	if (ezcInputForm::hasPostData()) {
    
    		$validationFields = array();
    		$validationFields[$params['name'].'From'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    		$validationFields[$params['name'].'Till'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    
    		$form = new ezcInputForm( INPUT_POST, $validationFields );

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
        $isInvalid = false;
        $errorInline = '';
    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		if (!$form->hasValidData($params['name']) || (isset($params['required']) && $params['required'] == 'required' && trim($form->{$params['name']}) == '') || (((!isset($params['required']) && $form->hasValidData($params['name']) && trim($form->{$params['name']}) != '') || isset($params['required']) && $params['required'] == 'required') && isset($params['validation_rule']) && $params['validation_rule'] != '' && !preg_match($params['validation_rule'],trim($form->{$params['name']})))) {
                $errorString = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');

                if (isset($params['error_style']) && $params['error_style'] == 'field') {
                    $errorInline = "<div class=\"invalid-feedback\">{$errorString}</div>";
                    self::$errorsInternal[] = $errorString;
                } else {
                    self::$errors[] = $errorString;
                }

                $isInvalid = true;
                if ($form->hasValidData($params['name'])) {
                    $value = trim($form->{$params['name']});
                }
    		} elseif ($form->hasValidData( $params['name'] ) && (!isset($params['validation_rule']) || $params['validation_rule'] == '' || preg_match($params['validation_rule'],trim($form->{$params['name']})))) {
    			$value = trim($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = self::extractValue($params);
    		}
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';
        $readonly = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' readonly ' : '';
    	return "<input class=\"form-control form-control-sm" . ($isInvalid == true ? ' is-invalid' : '') . "\" type=\"text\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} {$readonly} value=\"".htmlspecialchars($value)."\" />" . $errorInline;
    }

    public static function extractValue($params)
    {
        if (isset($params['chat_attr']) && (isset($_GET['chat_id']) && is_numeric($_GET['chat_id']) && isset($_GET['hash']) && ($chat = erLhcoreClassModelChat::fetch($_GET['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_GET['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT)) {
            $path = explode('.', $params['chat_attr']);
            if ($path[0] == 'chat' && $chat->{$path[1]} != '') {
                return $chat->{$path[1]};
            } elseif ($path[0] == 'chat_variable') {
                $chatVariablesArray = $chat->chat_variables_array;
                if (isset($chatVariablesArray[$path[1]]) && $chatVariablesArray[$path[1]] != '')
                return $chatVariablesArray[$path[1]];
            }
        }

        return (isset($params['value']) ? $params['value'] : '');
    }

    public static function renderInputTypeHidden($params) {
    	$additionalAttributes = self::renderAdditionalAtrributes($params);

    	$value = '';
    	if (ezcInputForm::hasPostData()) {

    		$validationFields = array();
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );

    		$form = new ezcInputForm( INPUT_POST, $validationFields );

    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && trim($form->{$params['name']}) == '')) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = trim($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
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

        if (isset($params['keep_hidden'])) {
            $params['as_admin'] = false;
        }
        $returnAppend = $return = "";
        if (isset($params['as_admin']) && $params['as_admin'] == true && isset($params['name_literal'])) {
            $return = "<div class='form-group'><label class='fw-bold'>" . htmlspecialchars($params['name_literal']) . "</label>";
            $returnAppend = "</div>";
        }

    	return $return . "<input class=\"form-control form-control-sm\" type=\"". ((isset($params['as_admin']) && $params['as_admin'] == true) ? "text" : "hidden") ."\" id=\"id_{$params['name']}\" name=\"{$params['name']}\" {$additionalAttributes} " . ((isset($params['as_admin']) && $params['as_admin'] == true) ? ' readonly ' : '') . "value=\"".htmlspecialchars($value)."\" />" . $returnAppend;
    }
        
    public static function renderInputTypeEmail($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
        $isInvalid = false;
        $errorInline = '';

    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && trim($form->{$params['name']}) == '')) {
                $errorString = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
                if (isset($params['error_style']) && $params['error_style'] == 'field') {
                    $errorInline = "<div class=\"invalid-feedback\">{$errorString}</div>";
                    self::$errorsInternal[] = $errorString;
                } else {
                    self::$errors[] = $errorString;
                }
                $isInvalid = true;
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = trim($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('main' => (isset($params['main']) && $params['main'] == 'true'),'definition' => $params, 'value' => trim($form->{$params['name']}));
    			    			
    			// It's main form e-mail
    			if (self::$collectedInfo[$params['name']]['main'] == true) {
    			    self::$mainEmail = self::$collectedInfo[$params['name']]['value'];
    			}
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = self::extractValue($params);
    		}
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
        $readonly = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' readonly ' : '';
    	return "<input class=\"form-control form-control-sm" . ($isInvalid == true ? ' is-invalid' : '') . "\" type=\"text\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} {$readonly} value=\"".htmlspecialchars($value)."\" />" . $errorInline;
    }

    public static function renderInputTypeNumber($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
        $isInvalid = false;
        $errorInline = '';

    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );

    		if (!$form->hasValidData( $params['name'] ) && isset($params['required']) && $params['required'] == 'required') {
                $errorString = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
                $isInvalid = true;
                if (isset($params['error_style']) && $params['error_style'] == 'field') {
                    $errorInline = "<div class=\"invalid-feedback\">{$errorString}</div>";
                    self::$errorsInternal[] = $errorString;
                } else {
                    self::$errors[] = $errorString;
                }
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = trim($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
    		}
    		
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = self::extractValue($params);;
    		}
    	}

    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
        $readonly = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' readonly ' : '';
    	return "<input class=\"form-control form-control-sm" . ($isInvalid == true ? ' is-invalid' : '') . "\" type=\"number\" name=\"{$params['name']}\" {$additionalAttributes} {$placeholder} {$readonly} value=\"".htmlspecialchars($value)."\" />" . $errorInline;
    }

    public static function renderInputTypeDate($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	$valueFrontEnd = '';
        $errorInline = '';
        $isInvalid = false;

    	if (ezcInputForm::hasPostData()) {
    		
    		$validationFields = array();    		    		
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
    		
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && trim($form->{$params['name']}) == '')) {
                $isInvalid = true;
                $errorString = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
                if (isset($params['error_style']) && $params['error_style'] == 'field') {
                    $errorInline = "<div class=\"invalid-feedback\">{$errorString}</div>";
                    self::$errorsInternal[] = $errorString;
                } else {
                    self::$errors[] = $errorString;
                }
    		} elseif ($form->hasValidData( $params['name']) && trim($form->{$params['name']}) != '') {
    			if (strtotime(trim($form->{$params['name']})) !== false) {
                    $d = new DateTime($form->{$params['name']});
                    $value = $d->getTimestamp();
                    $valueFrontEnd = $form->{$params['name']};
	    			self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => $value);
    			} else {
                    $isInvalid = true;
                    $valueFrontEnd = $value = htmlspecialchars(trim($form->{$params['name']}));
                    $errorString = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','invalid date format');
                    if (isset($params['error_style']) && $params['error_style'] == 'field') {
                        $errorInline = "<div class=\"invalid-feedback\">{$errorString}</div>";
                        self::$errorsInternal[] = $errorString;
                    } else {
                        self::$errors[] = $errorString;
                    }

    			}
    		}
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])) {
                $value = self::$collectedInfo[$params['name']]['value'];
    		} else {
                $value = self::extractValue($params);
    		}

            if (is_numeric($value)) {
                $valueFrontEnd = date('Y-m-d',$value);
            }
    	}
    	    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';    
        $readonly = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' readonly ' : '';
    	return "<input class=\"form-control form-control-sm" . ($isInvalid == true ? ' is-invalid' : '') . "\" type=\"date\" name=\"{$params['name']}\" id=\"id_{$params['name']}\" {$additionalAttributes} {$placeholder} {$readonly} value=\"".htmlspecialchars($valueFrontEnd)."\" />" . $errorInline;
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
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && (trim($form->{$params['name']}) == '' || (isset($params['default']) && $params['default'] == trim($form->{$params['name']}))))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = htmlspecialchars(trim($form->{$params['name']}));
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
    		}
    		    		 
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : (isset($params['default']) ? $params['default'] : ''));
    		}
    	}
    	
    	$options = array();
    	if (isset($params['from']) && isset($params['till'])){
    		for ($i = $params['from']; $i <= $params['till']; $i++) {
    			$isSelected= $value == $i ? 'selected="selected"' : '';
    			$options[] = "<option value=\"".htmlspecialchars($i)."\" {$isSelected}>".htmlspecialchars($i).'</option>';
    		}
    	} else {    	
	    	foreach (explode('#',$params['options']) as $option) {
	    		$optionParts = explode('___', $option);
	    		$optionValue = $optionParts[0];
	    		$optionName = isset($optionParts[1]) ? $optionParts[1] : $optionParts[0];
	    		$isSelected= $value == $optionValue ? 'selected="selected"' : '';
	    		$options[] = "<option value=\"".htmlspecialchars($optionValue)."\" {$isSelected}>".htmlspecialchars($optionName).'</option>';
	    	};
    	}
    	
    	$cssClass = 'form-select form-select-sm';
    	if (isset($params['css_class'])) {
    		$cssClass .= ' ' . htmlspecialchars($params['css_class']);
    	}
    	    	
        $disabled = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' disabled ' : '';
    	return "<select class=\"{$cssClass}\" {$additionalAttributes} {$disabled} name=\"{$params['name']}\">".implode('', $options)."</select>";
    }

    public static function renderInputTypeYear($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		 
    		$validationFields = array();
    		$validationFields[$params['name']] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => $params['from']) );
    		 
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && (trim($form->{$params['name']}) == '' || (isset($params['default']) && $params['default'] == trim($form->{$params['name']}))))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = htmlspecialchars(trim($form->{$params['name']}));
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
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
    		    	
        $disabled = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' disabled ' : '';
    	return "<select class=\"form-control form-control-sm\" {$additionalAttributes} {$disabled} name=\"{$params['name']}\">".implode('', $options)."</select>";
    }
    
    public static function renderInputTypeMonth($params) {    	
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	
    	$value = '';
    	
    	if (ezcInputForm::hasPostData()) {
    		 
    		$validationFields = array();
    		$validationFields[$params['name']] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1,'max_range' => 12) );
    		 
    		$form = new ezcInputForm( INPUT_POST, $validationFields );
    		$Errors = array();
    		 
    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && (trim($form->{$params['name']}) == '' || (isset($params['default']) && $params['default'] == trim($form->{$params['name']}))))) {
    			self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = trim($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
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
    		    	
        $disabled = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' disabled ' : '';
    	return "<select class=\"form-control form-control-sm\" {$additionalAttributes} {$disabled} name=\"{$params['name']}\">".implode('', $options)."</select>";
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
        $disabled = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' disabled ' : '';
    	return "<input type=\"checkbox\" name=\"{$params['name']}\"{$isChecked} {$additionalAttributes} {$disabled} value=\"on\" />";
    }
    
    public static function renderInputTypeTextarea($params) {    	
    	
    	$value = '';
        $isInvalid = false;
        $errorInline = '';

    	if (ezcInputForm::hasPostData()) {
    	
    		$validationFields = array();
    		$validationFields[$params['name']] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    	
    		$form = new ezcInputForm( INPUT_POST, $validationFields );

    		if ( !$form->hasValidData( $params['name'] ) || (isset($params['required']) && $params['required'] == 'required' && trim($form->{$params['name']}) == '')) {
                $errorString = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']).' '.erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
                $isInvalid = true;
                if (isset($params['error_style']) && $params['error_style'] == 'field') {
                    $errorInline = "<div class=\"invalid-feedback\">{$errorString}</div>";
                    self::$errorsInternal[] = $errorString;
                } else {
                    self::$errors[] = $errorString;
                }

    		} elseif ($form->hasValidData( $params['name'] )) {
    			$value = trim($form->{$params['name']});
    			self::$collectedInfo[$params['name']] = array('definition' => $params,'value' => trim($form->{$params['name']}));
    		}
    	
    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$value = self::$collectedInfo[$params['name']]['value'];
    		} else {
    			$value = (isset($params['value']) ? $params['value'] : '');
    		}
    	}    	
    	$placeholder = isset($params['placeholder']) ? ' placeholder="'.htmlspecialchars($params['placeholder']).'" ' : '';
    	$rows = isset($params['rows']) ? ' rows="'.htmlspecialchars($params['rows']).'" ' : '';
    	$additionalAttributes = self::renderAdditionalAtrributes($params);
    	$cssClass = 'form-control form-control-sm';
    	if (isset($params['css_class'])) {
    		$cssClass .= ' ' . htmlspecialchars($params['css_class']);
    	}

        $readonly = (isset($params['as_admin']) && $params['as_admin'] == true) ? ' readonly ' : '';
    	return "<textarea class=\"{$cssClass}" . ($isInvalid == true ? ' is-invalid' : '') . "\" name=\"{$params['name']}\" {$placeholder}{$rows}{$additionalAttributes} {$readonly}>" . htmlspecialchars($value) . "</textarea>" . $errorInline;
    }
    
    public static function renderAdditionalAtrributes($params) {
    	$additionalAttributes = array();
    	foreach ($params as $type => $value) {
    		if (strpos($type, 'ng-') !== false || strpos($type, 'data-') !== false) {
    			$additionalAttributes[] = $type.'="'.htmlspecialchars($value).'"';
    		}
    	};     	
    	return implode(' ', $additionalAttributes);
    }
    
    public static function renderInputTypeFile($params) {

    	$downloadLink = '';

        $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
        $data = (array)$fileData->data;

        // Is file uses different method to check is valid file
        $data['ft_us'] = str_replace('jpe?g','jpg|jpeg',$data['ft_us']);
        
    	if (ezcInputForm::hasPostData()) {
	    	if (!erLhcoreClassSearchHandler::isFile($params['name'], explode('|',$data['ft_us']), $data['fs_max'] * 1024) && (isset($params['required']) && $params['required'] == 'required')) {
                if (!empty(erLhcoreClassSearchHandler::$lastError)) {
                    self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']) . ' ' . erLhcoreClassSearchHandler::$lastError;
                } else {
                    self::$errors[] = (isset($params['name_literal']) ? $params['name_literal'] : $params['name']) . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('form/fill','is required');
                }
	    	} elseif (erLhcoreClassSearchHandler::isFile($params['name'], explode('|',$data['ft_us']), $data['fs_max'] * 1024)) {
	    		self::$collectedInfo[$params['name']] = array('definition' => $params, 'value' => $_FILES[$params['name']]);
	    	}

    	} else {
    		if (isset(self::$collectedInfo[$params['name']]['value'])){
    			$valueContent = self::$collectedInfo[$params['name']]['value'];
    			$downloadLink = "<a href=\"".erLhcoreClassSystem::getHost().erLhcoreClassDesign::baseurl('form/download').'/'.self::$collectedObject->id.'/'.self::$collectedObject->hash.'/'.$params['name']."\">Download (".htmlspecialchars($valueContent['name']).")</a>";
    		}
    	}
    	
    	$acceptTypes = '';
        if (isset($data['ft_us']) && $data['ft_us'] != '') {
            $acceptTypes = '.' . str_replace('|', ',.', $data['ft_us']);
        }

    	return "{$downloadLink}<input type=\"file\" accept=\"{$acceptTypes}\" name=\"{$params['name']}\" />";
    }

    public static function storeCollectedInformation($form, $collectedInformation, $customFields = [], $chat = null) {

	    $chatForm = true;
        $chatAttributes = [];
        $formAttributes = [];

        if (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && ((isset($_POST['hash']) && $chat->hash == $_POST['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) || ($form->form_type == erLhAbstractModelForm::FORM_TYPE_INTERNAL && erLhcoreClassUser::instance()->hasAccessTo('lhform', 'fill_private')))) {
            // Check does this form only chat modifying form or also information collecting form
            foreach ($collectedInformation as $fieldName => $params) {
                $chatFormElement = false;
                if (isset($params['definition']['chat_attr'])) {
                    $chatAttributes[] = $fieldName;
                    $chatFormElement = true;
                    $path = explode('.', $params['definition']['chat_attr']);
                    if ($path[0] == 'chat') {
                        $chat->{$path[1]} = (string)$params['value'];
                        $chat->updateThis(['update' => [$path[1]]]);
                    } elseif ($path[0] == 'chat_variable') {
                        $chatVariablesArray = $chat->chat_variables_array;
                        $chatVariablesArray[$path[1]] = is_numeric($params['value']) ? $params['value'] : (string)$params['value'];
                        $chat->chat_variables_array = $chatVariablesArray;
                        $chat->chat_variables = json_encode($chatVariablesArray);
                        $chat->updateThis(['update' => ['chat_variables']]);
                    }
                } elseif (isset($params['definition']['chat_additional']) && $params['definition']['chat_additional'] != '') {
                    $chatFormElement = true;
                    $chatAttributes[] = $fieldName;
                    $paramsAdditions = json_decode($params['definition']['chat_additional'],true);
                    if (isset($paramsAdditions['identifier'])) {
                        $additionalData = $chat->additional_data_array;
                        $attributesUpdates = [];
                        foreach ($additionalData as $index => $dataAdditional) {
                            if (isset($dataAdditional['identifier']) && $dataAdditional['identifier'] == $paramsAdditions['identifier']) {
                                $additionalData[$index]['value'] = is_numeric($params['value']) ? $params['value'] : (string)$params['value'];

                                if (isset($paramsAdditions['key'])) {
                                    $additionalData[$index]['key'] = $paramsAdditions['key'];
                                }

                                $attributesUpdates[] = $dataAdditional['identifier'];
                            }
                        }

                        if (!in_array($paramsAdditions['identifier'], $attributesUpdates)) {
                            $additionalData[] = [
                                'value' => is_numeric($params['value']) ? $params['value'] : (string)$params['value'],
                                'key' => (isset($paramsAdditions['key']) ? $paramsAdditions['key'] : $paramsAdditions['identifier']),
                                'identifier' => $paramsAdditions['identifier'],
                            ];
                        }

                        $chat->additional_data_array = $additionalData;
                        $chat->additional_data = json_encode($additionalData);
                        $chat->updateThis(['update' => ['additional_data']]);
                    }

                } elseif (isset($params['definition']['scope']) && $params['definition']['scope'] == 'chat') {
                    $chatAttributes[] = $fieldName;
                    $chatFormElement = true;
                    // Save file as association with a chat instead of a form
                    if ($params['definition']['type'] == 'file') {

                        $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
                        $data = (array)$fileData->data;
                        $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/' . $chat->id . '/';

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_path', array('path' => & $path, 'storage_id' => $chat->id));

                        $clamav = false;

                        if (isset($data['clamav_enabled']) && $data['clamav_enabled'] == true) {

                            $opts = array();

                            if (isset($data['clamd_sock']) && !empty($data['clamd_sock'])) {
                                $opts['clamd_sock'] = $data['clamd_sock'];
                            }

                            if (isset($data['clamd_sock_len']) && !empty($data['clamd_sock_len'])) {
                                $opts['clamd_sock_len'] = $data['clamd_sock_len'];
                            }

                            $clamav = new Clamav($opts);
                        }

                        $upload_handler = new erLhcoreClassFileUpload(array(
                            'antivirus' => $clamav,
                            'file_preview' => (isset($params['definition']['file_preview']) && $params['definition']['file_preview'] == 'true' ? true : false),
                            'param_name' => $params['definition']['name'],
                            'max_res' => ($data['max_res'] ?? 0),
                            'user_id' => -1,    // Save as system message
                            'as_form' => true,    // Indicate it's a form upload
                            'max_file_size' => $data['fs_max'] * 1024,
                            'accept_file_types_lhc' => '/\.(' . $data['ft_us'] . ')$/i',
                            'chat' => $chat,
                            'download_via_php' => true,
                            'upload_dir' => $path));

                        if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {

                            if (isset($params['definition']['chat_file_attr']) && !empty($params['definition']['chat_file_attr']) ) {
                                $chatVariablesArray = $chat->chat_variables_array;
                                $chatVariablesArray[$params['definition']['chat_file_attr']] = '[file='.$upload_handler->uploadedFile->id.'_'.$upload_handler->uploadedFile->security_hash.']';
                                $chat->chat_variables_array = $chatVariablesArray;
                                $chat->chat_variables = json_encode($chatVariablesArray);
                                $chat->updateThis(['update' => ['chat_variables']]);
                            }

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.uploadfile.file_store', array('chat_file' => $upload_handler->uploadedFile));
                            $chat->user_typing_txt = '100%';
                        }

                        $chat->user_typing = time();
                        $chat->updateThis(array('update' => array('user_typing_txt','user_typing')));
                    }
                } else {
                    $formAttributes[] = $fieldName;
                }

                // If at-least one attribute is not chat save as a form
                if ($chatForm == true && $chatFormElement == false) {
                    $chatForm = false;
                }

             }
        } else {
            $chatForm = false;
        }

        if ($chatForm === false) {
            if (self::$collectedObject instanceof erLhAbstractModelFormCollected && self::$collectedObject->form_id == $form->id) {
                $formCollected = self::$collectedObject;
                $formCollected->ip = erLhcoreClassIPDetect::getIP();
                $formCollected->ctime = time();
                $formCollected->identifier = substr(isset($_POST['identifier']) ? $_POST['identifier'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),0,250);
            } else {
                $formCollected = new erLhAbstractModelFormCollected();
                $formCollected->ip = erLhcoreClassIPDetect::getIP();
                $formCollected->ctime = time();
                $formCollected->form_id = $form->id;
                $formCollected->identifier = substr(isset($_POST['identifier']) ? $_POST['identifier'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),0,250);
                if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_INTERNAL) {
                    $formCollected->user_id = erLhcoreClassUser::instance()->getUserID();
                }
                $formCollected->saveThis();
            }
        }

        if (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && ((isset($_POST['hash']) && $chat->hash == $_POST['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) || ($form->form_type == erLhAbstractModelForm::FORM_TYPE_INTERNAL && erLhcoreClassUser::instance()->hasAccessTo('lhform', 'fill_private')))) {

            if ($chatForm === false) {
                $formCollected->chat_id = $chat->id;
            }

            if ($form->form_type == erLhAbstractModelForm::FORM_TYPE_PUBLIC) {
                // Store as message to visitor
                $msg = new erLhcoreClassModelmsg();

                if ($chatForm === false) {
                    $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Information collected. [baseurl]form/viewcollected/' . $formCollected->id . '[/baseurl]');
                } else {
                    $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Information collected. Only chat form');
                }

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
        }

        if ($chatForm === true) {
            return;
        }

    	// Finish collect information
    	foreach ($collectedInformation as $fieldName => & $params) {
    	    // Do not save file again if it was chat file

    		if (!in_array($fieldName, $chatAttributes) && isset($params['definition']['type']) && $params['definition']['type'] == 'file' && !(isset($params['definition']['scope']) && $params['definition']['scope'] == 'chat')) {
    						
    			$dir = 'var/storageform/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$formCollected->id.'/';
    			
    			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.fill.file_path',array('path' => & $dir, 'storage_id' => $formCollected->id));
    			
    			erLhcoreClassFileUpload::mkdirRecursive( $dir );
    			    			
    			$file = erLhcoreClassSearchHandler::moveUploadedFile($params['definition']['name'],$dir);
    			
	    		$params['filename'] = $file;
	    		$params['filepath'] = $dir;
	    		
	    		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.fill.store_file',array('file_params' => & $params));
    		}
    	}
    	unset($params);
      
        // Handle form_attr mapping to attr_int_1/2/3
        foreach ($collectedInformation as $fieldName => $params) {
            if (isset($params['definition']['form_attr']) && in_array($params['definition']['form_attr'], ['attr_int_1', 'attr_int_2', 'attr_int_3'])) {
                $formCollected->{$params['definition']['form_attr']} = (int)$params['value'];
            }
        }
      

        if (isset($form->configuration_array['track_field_changes']) && $form->configuration_array['track_field_changes'] == true && $form->form_type == erLhAbstractModelForm::FORM_TYPE_INTERNAL) {
            if ($formCollected->user_id != erLhcoreClassUser::instance()->getUserID()) {
                $currentUserId = erLhcoreClassUser::instance()->getUserID();
                $currentTime = time();

                // Load previous content to compare against
                $previousContent = $formCollected->content_array;
                if (!is_array($previousContent)) {
                    $previousContent = [];
                }

                // Preserve existing field changes history from previous saves
                $fieldChanges = isset($previousContent['lhc_field_changes']) ? $previousContent['lhc_field_changes'] : ['modified_operators' => [], 'field_history' => []];
                $modifiedFields = [];

                foreach ($collectedInformation as $fieldName => $params) {
                    if ($fieldName === 'lhc_field_changes') continue;
                    if (!isset($params['definition']['log_changes']) || $params['definition']['log_changes'] != 'true') continue;

                    $newValue = isset($params['value']) ? $params['value'] : null;
                    $oldValue = isset($previousContent[$fieldName]['value']) ? $previousContent[$fieldName]['value'] : null;

                    // Compare values; skip comparison for file fields (track by filename instead)
                    if ($params['definition']['type'] == 'file') {
                        $newValue = isset($params['filename']) ? $params['filename'] : null;
                        $oldValue = isset($previousContent[$fieldName]['filename']) ? $previousContent[$fieldName]['filename'] : null;
                    }

                    if ($newValue !== $oldValue) {
                        $modifiedFields[] = $fieldName;

                        $fieldChanges['field_history'][$fieldName] = [
                            'user_id' => $currentUserId,
                            'modified_at' => $currentTime,
                            'old_value' => $oldValue,
                            'new_value' => $newValue
                        ];
                    }
                }

                if (!empty($modifiedFields)) {
                    $existingFields = isset($fieldChanges['modified_operators'][$currentUserId]['fields']) ? $fieldChanges['modified_operators'][$currentUserId]['fields'] : [];
                    $fieldChanges['modified_operators'][$currentUserId] = [
                        'fields' => array_unique(array_merge($existingFields, $modifiedFields)),
                        'modified_at' => $currentTime
                    ];
                }

                $collectedInformation['lhc_field_changes'] = $fieldChanges;
            }
        }


    	$formCollected->content = json_encode($collectedInformation);
        $formCollected->custom_fields = json_encode($customFields);
    	$formCollected->saveThis();

    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.filled',array('form' => & $formCollected));
    	
    	// Inform user about filled form
    	erLhcoreClassChatMail::informFormFilled($formCollected,array('email' => self::$mainEmail));

        $translationArgs = array('form' => $form);
        $translationArgs['form_collected'] = $formCollected;

        $form->post_content = erLhcoreClassGenericBotWorkflow::translateMessage($form->post_content, array('args' => $translationArgs));
    }
    
}

?>