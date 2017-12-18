<?php

/**
 * Class used for validator
 * */

class erLhcoreClassChatValidator {

	public static function validateChatModify(& $chat)
	{
		$definition = array(				
				'Email' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
				),
				'UserNick' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
				),				
				'UserPhone' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
				)				
		);
		
		$form = new ezcInputForm( INPUT_POST, $definition );
		$Errors = array();
		
		$currentUser = erLhcoreClassUser::instance();
			
		if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invalid CSRF token!');
		}
	
		if ( !$form->hasValidData( 'Email' ) && $_POST['Email'] != '' ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a valid email address');
		} elseif ($form->hasValidData( 'Email' )) {
			$chat->email = $form->Email;
		}
		
		if ($form->hasValidData( 'UserNick' ) && $form->UserNick != '' && strlen($form->UserNick) > 50)
		{
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 50 characters');
		}
		
		if ($form->hasValidData( 'UserPhone' )) {
			$chat->phone = $form->UserPhone;
		}
		
		if ($form->hasValidData( 'UserNick' ))
		{
			$chat->nick = $form->UserNick;
		}
		
		return $Errors;		
	}
	
    /**
     * Custom form fields validation
     */
    public static function validateStartChat(& $inputForm, & $start_data_fields, & $chat, $additionalParams = array())
    {
        $validationFields = array();

        // Dynamic form field
        if ($inputForm->validate_start_chat == true) {
            if ( (isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_name_visible_in_popup']) && $start_data_fields['offline_name_visible_in_popup'] == true) ) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if ((isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_message_visible_in_popup']) && $start_data_fields['offline_message_visible_in_popup'] == true)) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_phone_visible_in_popup']) && $start_data_fields['offline_phone_visible_in_popup'] == true)) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['tos_visible_in_popup']) && $start_data_fields['tos_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_tos_visible_in_popup']) && $start_data_fields['offline_tos_visible_in_popup'] == true)) {
                $validationFields['AcceptTOS'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'boolean' );
            }
            
        } else {
            if ((isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_name_visible_in_page_widget']) && $start_data_fields['offline_name_visible_in_page_widget'] == true)) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if ( (isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_message_visible_in_page_widget']) && $start_data_fields['offline_message_visible_in_page_widget'] == true)) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_phone_visible_in_page_widget']) && $start_data_fields['offline_phone_visible_in_page_widget'] == true)) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }     

            if ((isset($start_data_fields['tos_visible_in_page_widget']) && $start_data_fields['tos_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_tos_visible_in_page_widget']) && $start_data_fields['offline_tos_visible_in_page_widget'] == true)) {
            	$validationFields['AcceptTOS'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'boolean' );
            }            
            
        }

        $validationFields['ProductID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1));
        $validationFields['DepartamentID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => -1));
        $validationFields['DepartmentIDDefined'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY);
        $validationFields['ProductIDDefined'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY);
        $validationFields['operator'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1));
        $validationFields['user_timezone'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int');
        $validationFields['HasProductID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean');
        $validationFields['keyUpStarted'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1));
        
        $validationFields['name_items'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
        		null,
        		FILTER_REQUIRE_ARRAY
        );

        $validationFields['values_req'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'string',
        		null,
        		FILTER_REQUIRE_ARRAY
        );

        $validationFields['value_items'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
        		null,
        		FILTER_REQUIRE_ARRAY
        );

        $validationFields['value_types'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
        		null,
        		FILTER_REQUIRE_ARRAY
        );

        $validationFields['value_sizes'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
        		null,
        		FILTER_REQUIRE_ARRAY
        );

        $validationFields['value_show'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'string',
        		null,
        		FILTER_REQUIRE_ARRAY
        );

        $validationFields['hattr'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'string',
        		null,
        		FILTER_REQUIRE_ARRAY
        );
        
        $validationFields['encattr'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string',
            null,
            FILTER_REQUIRE_ARRAY
        );
        
        if (!isset($additionalParams['ignore_captcha']) || $additionalParams['ignore_captcha'] == false)
        {
            // Captcha stuff        
            if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
            	// Start session if required only
            	$currentUser = erLhcoreClassUser::instance();
            	$hashCaptcha = isset($_SESSION[$_SERVER['REMOTE_ADDR']]['form']) ? $_SESSION[$_SERVER['REMOTE_ADDR']]['form'] : null;
        		$nameField = 'captcha_'.$hashCaptcha;    	
            	$validationFields[$nameField] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
            } else {        
    	        $nameField = 'captcha_'.sha1(erLhcoreClassIPDetect::getIP().$_POST['tscaptcha'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
    	        $validationFields[$nameField] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
            }
        }
        
        // Custom start chat fields
        $validationFields['value_items_admin'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
            null,
            FILTER_REQUIRE_ARRAY
        );
        
        $validationFields['via_hidden'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
            null,
            FILTER_REQUIRE_ARRAY
        );
        
        $validationFields['via_encrypted'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
            null,
            FILTER_REQUIRE_ARRAY
        );
        
        $form = new ezcInputForm( INPUT_POST, $validationFields );
        $Errors = array();

        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => erLhcoreClassIPDetect::getIP()))) > 0) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You do not have permission to chat! Please contact site owner.');
        }
        
        /**
         * IP Ranges block
         * */
        $ignorable_ip = erLhcoreClassModelChatConfig::fetch('banned_ip_range')->current_value;
        
        if ( $ignorable_ip != '' && erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
        	$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You do not have permission to chat! Please contact site owner.');
        }
       
        if (!isset($additionalParams['ignore_captcha']) || $additionalParams['ignore_captcha'] == false)
        {
            if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
            	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 || $hashCaptcha != sha1($_SERVER['REMOTE_ADDR'].$form->$nameField.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ))){
            		$Errors['captcha'] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
            	}
            } else {
            	// Captcha validation
            	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 )
            	{
            		$Errors['captcha'] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
            	}
            }
        }
        
        if (isset($validationFields['Username'])) {

            if ( !$form->hasValidData( 'Username' ) || ($form->Username == '' && (($start_data_fields['name_require_option'] == 'required' && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required' )))  )
            {
                $Errors['nick'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
            } elseif ($form->hasValidData( 'Username' )) {
                $chat->nick = $inputForm->username = $form->Username;
            }

            if ($form->hasValidData( 'Username' ) && $form->Username != '' && strlen($form->Username) > 50)
            {
                $Errors['nick'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 50 characters');
            }
        }

        if ( isset($validationFields['Email']) ) {
            if ( (!$form->hasValidData( 'Email' ) && $start_data_fields['email_require_option'] == 'required') || (!$form->hasValidData( 'Email' ) && isset($additionalParams['offline'])) ) {
                $Errors['email'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a valid email address');
            } elseif ( $form->hasValidData( 'Email' ) ) {
                $chat->email = $inputForm->email = $form->Email;
            } else {
                $chat->email = $inputForm->email = $_POST['Email'];
            }
        }
        
        // Validate question
        if (isset($validationFields['Question'])) {

            if ( !$form->hasValidData('keyUpStarted') && (!$form->hasValidData( 'Question' ) || (trim($form->Question) == '' && (($start_data_fields['message_require_option'] == 'required' && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_message_require_option']) && $start_data_fields['offline_message_require_option'] == 'required'))))) {
                $Errors['question'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
            } elseif ($form->hasValidData( 'Question' )) {
                $inputForm->question = trim($form->Question);
            }

            if ($form->hasValidData( 'Question' ) && trim($form->Question) != '' && strlen($form->Question) > (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
            {
                $Errors['question'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum').' '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters for a message');
            }
        }
       
        if (isset($validationFields['AcceptTOS'])) {
        	if ( !$form->hasValidData( 'AcceptTOS' ) || $form->AcceptTOS == false) {
        		$Errors['accept_tos'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You have to accept our Terms Of Service');
        	} else {
        		$inputForm->accept_tos = true;
        	}
        }

        
        // Validate phone
        if (isset($validationFields['Phone'])) {

            if ( !$form->hasValidData( 'Phone' ) || (($form->Phone == '' || mb_strlen($form->Phone) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value) && ( ($start_data_fields['phone_require_option'] == 'required' && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required')  ))) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone');
            } elseif ($form->hasValidData( 'Phone' )) {
                $chat->phone = $inputForm->phone = $form->Phone;
            }

            if ($form->hasValidData( 'Phone' ) && $form->Phone != '' && strlen($form->Phone) > 100)
            {
                $Errors['phone'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters for phone');
            }
        }
        
        if ($form->hasValidData( 'operator' ) && erLhcoreClassModelUser::getUserCount(array('filter' => array('id' => $form->operator, 'disabled' => 0))) > 0) {
        	$inputForm->operator = $chat->user_id = $form->operator;
        }
        
        /**
         * File for offline form
         * */
        $inputForm->has_file = false;
              
        if (isset($additionalParams['offline']) && (
         				($inputForm->validate_start_chat == true && isset($start_data_fields['offline_file_visible_in_popup']) && $start_data_fields['offline_file_visible_in_popup'] == true) || 
         				($inputForm->validate_start_chat == false && isset($start_data_fields['offline_file_visible_in_page_widget']) && $start_data_fields['offline_file_visible_in_page_widget'] == true)
         		)) {
         		
         	$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
         	$data = (array)$fileData->data;
         	
         	if ($_FILES['File']['error'] != 4) { // No file was provided
	         	if (isset($_FILES['File']) && erLhcoreClassSearchHandler::isFile('File','/\.('.$data['ft_us'].')$/i',$data['fs_max']*1024)){
	         		$inputForm->has_file = true;
	
	         		// Just extract file extension
	         		$fileNameAray = explode('.',$_FILES['File']['name']);
	         		end($fileNameAray);
	         		
	         		// Set attribute for futher
	         		$inputForm->file_extension = strtolower(current($fileNameAray));
	         		$inputForm->file_location = $_FILES['File']['tmp_name'];
	         		         		
	         	} elseif (isset($_FILES['File'])) {
	         		$Errors[] = erLhcoreClassSearchHandler::$lastError != '' ? erLhcoreClassSearchHandler::$lastError : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invalid file');
	         	}
         	}
         }
        
        if ($form->hasValidData( 'user_timezone' )) {        	
        	$timezone_name = timezone_name_from_abbr(null, $form->user_timezone*3600, true);        	
        	if ($timezone_name !== false) {
        		$chat->user_tz_identifier = $timezone_name;
        	} else {
        		$chat->user_tz_identifier = '';
        	}
        }

        if ($form->hasValidData( 'DepartmentIDDefined' )) {
        	$inputForm->departament_id_array = $form->DepartmentIDDefined;
        }

        if ($form->hasValidData( 'ProductIDDefined' )) {
        	$inputForm->product_id_array = $form->ProductIDDefined;
        }

        if ($form->hasValidData( 'HasProductID' ) && $form->HasProductID == true) {

            if ($form->hasValidData( 'ProductID' )) {

                $inputForm->product_id = $chat->product_id = $form->ProductID;

                try {
                    $product = erLhAbstractModelProduct::fetch($chat->product_id);
                    
                    if (erLhcoreClassModelChatConfig::fetch('product_show_departament')->current_value == 0) {
                        $chat->dep_id = $product->departament_id;   
                    } else {
                        if ($form->hasValidData( 'DepartamentID' ) && erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $form->DepartamentID, 'disabled' => 0))) > 0) {
                            $chat->dep_id = $form->DepartamentID;
                        } else {
                            $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose department!');
                        }
                    }
                    
                } catch (Exception $e) {
                    $Errors['ProductID'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Could not find a product!');
                }

            } else {
                
                if (erLhcoreClassModelChatConfig::fetch('product_show_departament')->current_value == 1) {
                    
                    if ($form->hasValidData( 'DepartamentID' ) && erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $form->DepartamentID, 'disabled' => 0))) > 0) {
                        $chat->dep_id = $form->DepartamentID;
                    } elseif ($form->hasValidData( 'DepartamentID' ) && $form->DepartamentID == -1) {
                        $chat->dep_id == 0;
                    
                        if (isset($additionalParams['theme']) && $additionalParams['theme'] !== false && $additionalParams['theme']->department_title != '') {
                            $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose').' '.htmlspecialchars($additionalParams['theme']->department_title).'!';
                        } else {
                            $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose department!');
                        }
                    
                    } elseif ($chat->dep_id == 0 || erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $chat->dep_id,'disabled' => 0))) == 0) {
                    
                        // Perhaps extension overrides default department?
                        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_department', array('input_form' => $inputForm));
                    
                        // There was no callbacks or file not found etc, we try to download from standard location
                        if ($response === false) {
                            $departments = erLhcoreClassModelDepartament::getList(array('limit' => 1,'filter' => array('disabled' => 0)));
                            if (!empty($departments) ) {
                                $department = array_shift($departments);
                                $chat->dep_id = $department->id;
                            } else {
                                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Could not determine a default department!');
                            }
                        } else {
                            $chat->dep_id = $response['department_id'];
                        }
                    }
                    
                    // Is product required or not?
                    if ( $chat->dep_id > 0 ) {
                        try {
                            $dep = erLhcoreClassModelDepartament::fetch($chat->dep_id);
                            if (isset($dep->product_configuration_array['products_enabled']) && $dep->product_configuration_array['products_enabled'] == 1 && isset($dep->product_configuration_array['products_required']) && $dep->product_configuration_array['products_required'] == 1) {
                                $Errors['ProductID'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose a product!');
                            }
                        } catch (Exception $e) {
                            
                        }
                    }

                } else {
                    $Errors['ProductID'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose a product!');
                }
            }

        } else {
            
            if ($form->hasValidData( 'DepartamentID' ) && erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $form->DepartamentID, 'disabled' => 0))) > 0) {
            	$chat->dep_id = $form->DepartamentID;        	
            } elseif ($form->hasValidData( 'DepartamentID' ) && $form->DepartamentID == -1) {            
                $chat->dep_id == 0;
                
                if (isset($additionalParams['theme']) && $additionalParams['theme'] !== false && $additionalParams['theme']->department_title != '') {
                    $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose').' '.htmlspecialchars($additionalParams['theme']->department_title).'!';
                } else {
                    $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose department!');
                }
                
            } elseif ($chat->dep_id == 0 || erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $chat->dep_id,'disabled' => 0))) == 0) {
                
                // Perhaps extension overrides default department?
                $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_department', array('input_form' => $inputForm));
                
                // There was no callbacks or file not found etc, we try to download from standard location
                if ($response === false) {
                	$departments = erLhcoreClassModelDepartament::getList(array('limit' => 1,'filter' => array('disabled' => 0)));
                	if (!empty($departments) ) {
        	        	$department = array_shift($departments);
        	        	$chat->dep_id = $department->id;
                	} else {
                		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Could not determine a default department!');
                	}
                } else {
                    $chat->dep_id = $response['department_id'];
                }
            }
        }

        $department = false;
        
        if ($chat->product_id > 0) {
           $department = $chat->product->departament;
        } elseif ($chat->department !== false) {
           $department = $chat->department;
        }
                        
        if ($department !== false && $department->department_transfer_id > 0) {
        	$chat->transfer_if_na = 1;
        	$chat->transfer_timeout_ts = time();
        	$chat->transfer_timeout_ac = $department->transfer_timeout;
        }
        
        if ($department !== false && $department->inform_unread == 1) {
        	$chat->reinform_timeout = $department->inform_unread_delay;        	
        }
        
        // Allow offline request, but do not allow online request if department is overloaded
        if (!(isset($additionalParams['offline']) && $additionalParams['offline'] == true) && $department !== false && $department->is_overloaded == true) {
            $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','At the moment department is overloaded, please choose a different department or try again later!');
        }
        
        $inputForm->departament_id = $chat->dep_id;

        if ( $inputForm->priority !== false && is_numeric($inputForm->priority) ) {
        	$chat->priority = (int)$inputForm->priority;
        } else {
        	if ($department !== false) {
        		$chat->priority = $department->priority;
        	}
        }

        $stringParts = array();
        
        if ( $form->hasValidData( 'name_items' ) && !empty($form->name_items))
        {
        	$valuesArray = array();
        	if ( $form->hasValidData( 'value_items' ) && !empty($form->value_items))
        	{
        		$inputForm->value_items = $valuesArray = $form->value_items;
        	}
        	
        	if ( $form->hasValidData( 'values_req' ) && !empty($form->values_req))
        	{
        		$inputForm->values_req = $form->values_req;
        	}

        	if ( $form->hasValidData( 'value_types' ) && !empty($form->value_types))
        	{
        		$inputForm->value_types = $form->value_types;
        	}

        	if ( $form->hasValidData( 'value_sizes' ) && !empty($form->value_sizes))
        	{
        		$inputForm->value_sizes = $form->value_sizes;
        	}

        	if ( $form->hasValidData( 'value_show' ) && !empty($form->value_show))
        	{
        		$inputForm->value_show = $form->value_show;
        	}

        	if ( $form->hasValidData( 'hattr' ) && !empty($form->hattr))
        	{
        		$inputForm->hattr = $form->hattr;
        	}

        	if ( $form->hasValidData( 'encattr' ) && !empty($form->encattr))
        	{
        		$inputForm->encattr = $form->encattr;
        	}
        	
        	$inputForm->name_items = $form->name_items;
        	
        	foreach ($form->name_items as $key => $name_item) {    
        	    
        		if (isset($inputForm->values_req[$key]) && $inputForm->values_req[$key] == 't' && ($inputForm->value_show[$key] == 'b' || $inputForm->value_show[$key] == (isset($additionalParams['offline']) ? 'off' : 'on')) && (!isset($valuesArray[$key]) || trim($valuesArray[$key]) == '')) {
        			$Errors['additional_'.$key] = trim($name_item).' : '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
        		}
        		
        		$valueStore = isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '';
        		
        		if (isset($inputForm->encattr[$key]) && $inputForm->encattr[$key] == 't' && $valueStore != '') {
        		    try {
        		        $valueStore = self::decryptAdditionalField($valueStore, $chat);
        		    } catch (Exception $e) {
        		        $Errors[] = $e->getMessage();
        		    }
        		}
        		
        		$stringParts[] = array('h' => ($inputForm->value_types[$key] && $inputForm->value_types[$key] == 'hidden' ? true : false), 'key' => $name_item, 'value' => $valueStore);
        	}
        }
        

        if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != '') {
            $customAdminfields = json_decode($start_data_fields['custom_fields'],true);
            
            $valuesArray = array();
            
            // Fill values if exists
            if ($form->hasValidData( 'value_items_admin' )){
                $inputForm->value_items_admin = $valuesArray = $form->value_items_admin;
            }
            
            if ($form->hasValidData( 'via_hidden' )){
                $inputForm->via_hidden = $form->via_hidden;
            }
            
            if ($form->hasValidData( 'via_encrypted' )) {
                $inputForm->via_encrypted = $form->via_encrypted;
            }

            if (is_array($customAdminfields)) {
                foreach ($customAdminfields as $key => $adminField) {

                    if (isset($inputForm->value_items_admin[$key]) && isset($adminField['isrequired']) && $adminField['isrequired'] == 'true' && ($adminField['visibility'] == 'all' || $adminField['visibility'] == (isset($additionalParams['offline']) ? 'off' : 'on')) && (!isset($valuesArray[$key]) || trim($valuesArray[$key]) == '')) {
            			$Errors['additional_admin_'.$key] = trim($adminField['fieldname']).': '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
            		}

            		if (isset($valuesArray[$key]) && $valuesArray[$key] != '') {

            		    $valueStore = (isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '');

            		    if (isset($inputForm->via_encrypted[$key]) && $inputForm->via_encrypted[$key] == 't' && $valueStore != '') {
            		        try {
            		            $valueStore = self::decryptAdditionalField($valueStore, $chat);
            		        } catch (Exception $e) {
            		            $valueStore = $e->getMessage();
            		        }
            		    }

            		    $stringParts[] = array('h' => (isset($inputForm->via_hidden[$key]) || $adminField['fieldtype'] == 'hidden'), 'identifier' => (isset($adminField['fieldidentifier'])) ? $adminField['fieldidentifier'] : null, 'key' => $adminField['fieldname'], 'value' => $valueStore);
            		}
                }
            }
        }

        // Detect user locale
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $parts = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $languages = explode(',',$parts[0]);
            if (isset($languages[0])) {
                $chat->chat_locale = $languages[0];
            }
        }

        if (!empty($stringParts)) {
            $chat->additional_data = json_encode($stringParts);
        }

        // Detect device
        $detect = new Mobile_Detect;
        $chat->uagent = $detect->getUserAgent();
        $chat->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_start_chat',array('errors' => & $Errors, 'input_form' => & $inputForm, 'start_data_fields' => & $start_data_fields, 'chat' => & $chat,'additional_params' => & $additionalParams));
        
        return $Errors;
    }
    
    
    /**
     * Validates custom fields
     */
    public static function validateCustomFieldsRefresh(& $chat)
    {
        $definition = array (
            'name'  => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'value' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'size' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'req' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'sh' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
                null,
                FILTER_REQUIRE_ARRAY
            ),            
            'encattr' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            // At the moment not used                        
            'via_encrypted' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'value_items_admin' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
            ),            
            'via_hidden' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                null,
                FILTER_REQUIRE_ARRAY
            )            
        );
            
        
        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();
        $inputForm = new stdClass();
        
        $stringParts = array();
        
        if ( $form->hasValidData( 'name' ) && !empty($form->name))
        {
            $valuesArray = array();
            if ( $form->hasValidData( 'value' ) && !empty($form->value))
            {
                $inputForm->value = $valuesArray = $form->value;
            }
             
            if ( $form->hasValidData( 'req' ) && !empty($form->req))
            {
                $inputForm->req = $form->req;
            }
        
            if ( $form->hasValidData( 'type' ) && !empty($form->type))
            {
                $inputForm->type = $form->type;
            }
        
            if ( $form->hasValidData( 'size' ) && !empty($form->size))
            {
                $inputForm->size = $form->size;
            }
        
            if ( $form->hasValidData( 'encattr' ) && !empty($form->encattr))
            {
                $inputForm->encattr = $form->encattr;
            }
        
            $inputForm->name = $form->name;
            
            $currentChatData = json_decode($chat->additional_data,true);
            
            if (is_array($currentChatData)) {
                foreach ($currentChatData as $key => $data) {
                    if (isset($data['h']) && $data['h'] == true) {
                        unset($currentChatData[$key]);
                    }
                }
            } else {
                $currentChatData = array();
            }
            
            foreach ($form->name as $key => $name_item) {
                if (isset($inputForm->type[$key]) && $inputForm->type[$key] == 'hidden') {

                    $valueStore = isset($inputForm->value[$key]) ? trim($inputForm->value[$key]) : '';

                    if (isset($inputForm->encattr[$key]) && $inputForm->encattr[$key] == 't' && $valueStore != '') {
                        try {
                            $valueStore = self::decryptAdditionalField($valueStore, $chat);
                        } catch (Exception $e) {
                            $valueStore = $e->getMessage();
                        }
                    }

                    $currentChatData[] = array('h' => true, 'key' => $name_item,'value' => $valueStore);
                }
            }
        }
        
        /**
         * Admin custom fields
         * */
        $startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $chat->dep_id)));
        
        if ($startDataDepartment instanceof erLhcoreClassModelChatStartSettings) {
            $start_data_fields = $startDataDepartment->data_array;
        } else {
            $start_data_fields = (array)erLhcoreClassModelChatConfig::fetch('start_chat_data')->data;
        }
        
        /**
         * Admin custom fields support
         * */
        if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != '') {
            
            $customAdminfields = json_decode($start_data_fields['custom_fields'],true);

            if (!isset($currentChatData)) {
                $currentChatData = json_decode($chat->additional_data,true);
                
                if (is_array($currentChatData)) {
                    $currentChatData = array();
                }
            }

            $fieldsTitles = array();
            $hiddenFields = array();
            $valuesArray = array();

            // Fill values if exists
            if ($form->hasValidData( 'value_items_admin' )) {
                $inputForm->value_items_admin = $valuesArray = $form->value_items_admin;
            }
            
            if ($form->hasValidData( 'via_hidden' )) {
                $inputForm->via_hidden = $form->via_hidden;
            }
                    
            if ($form->hasValidData( 'via_encrypted' )) {
                $inputForm->via_encrypted = $form->via_encrypted;
            }
            
            // Detect hidden fields which have to be resaved
            if (is_array($customAdminfields)) {
                foreach ($customAdminfields as $key => $adminField) {
                    $fieldsTitles[] = $adminField['fieldidentifier'];
            
                    if ($inputForm->via_hidden[$key] || $adminField['fieldtype'] == 'hidden') {
                        $hiddenFields[] = $adminField['fieldidentifier'];
                    }
                }
            }

            // Removed hidden admin fields
            foreach ($currentChatData as $key => $data) {
                /**
                 * Clean only if
                 * 1. Field is hidden
                 * 2. Field is hidden manually
                 * 3. Field is back office custom field
                 * */
                if (in_array($data['key'], $hiddenFields) && in_array($data['key'], $fieldsTitles)) {
                    unset($currentChatData[$key]);
                }
            }
            
            // Custom fields
            if (is_array($customAdminfields)) {
                foreach ($customAdminfields as $key => $adminField) {
        
                    if ( (isset($inputForm->via_hidden[$key]) && $inputForm->via_hidden[$key] == 't') || $adminField['fieldtype'] == 'hidden' )
                    {                           
                        if (isset($valuesArray[$key]) && $valuesArray[$key] != '') {
            
                            $valueStore = (isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '');
            
                            if (isset($inputForm->via_encrypted[$key]) && $inputForm->via_encrypted[$key] == 't' && $valueStore != '') {
                                try {
                                    $valueStore = self::decryptAdditionalField($valueStore, $chat);
                                } catch (Exception $e) {
                                    $valueStore = $e->getMessage();
                                }
                            }

                            $currentChatData[] = array('h' => true, 'identifier' => $adminField['fieldidentifier'], 'key' => $adminField['fieldname'], 'value' => $valueStore);
                        }
                    }
                }
            }
        }
        
        // To reset index
        $currentChatData = array_values($currentChatData);
        
        if (!empty($currentChatData)) {
            $chat->additional_data = json_encode($currentChatData);
        }
    }
    
    public static function decryptAdditionalField($valueStore, $chat)
    {
        if ($valueStore != '') {
                   
            $startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $chat->dep_id)));
            
            if ($startDataDepartment instanceof erLhcoreClassModelChatStartSettings) {
                $startData = $startDataDepartment->data_array;
            } else {
                $startData = (array)erLhcoreClassModelChatConfig::fetch('start_chat_data')->data;
            }
            
            $valueStore = lhSecurity::decrypt(base64_decode($valueStore),
                (isset($startData['custom_fields_encryption']) && !empty($startData['custom_fields_encryption']) ? $startData['custom_fields_encryption'] : null),
                (isset($startData['custom_fields_encryption_hmac']) && !empty($startData['custom_fields_encryption_hmac']) ? $startData['custom_fields_encryption_hmac'] : null)
            );
        
            if ($valueStore === false) {
               throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Could not decrypt data!'));
            }
        }
        
        return $valueStore;
    }
    
    /**
     * If user was redirected to contact form and he changed some default attributes we change then in intial chat
     * */
    public static function updateInitialChatAttributes(erLhcoreClassModelChat & $prefillChat, erLhcoreClassModelChat $currentChat) {
    	$attributesPrefill = array(
    		'nick',
    		'email',
    		'phone'
    	);
    	
    	$attrChanged = false;
    	foreach ($attributesPrefill as $attr) {
    		if ($prefillChat->$attr == '' && $currentChat->$attr != '') {
    			$prefillChat->$attr = $currentChat->$attr;
    			$attrChanged = true;
    		}
    	}
    	
    	if ($attrChanged) {
    		$prefillChat->saveThis();
    	}
    }
    
    public static function validateNickChange(& $chat)
    {
        $definition = array(
            'Email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'UserNick' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'UserPhone' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );
    
        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();
     
        if ( !$form->hasValidData( 'Email' ) && $_POST['Email'] != '' ) {
            $Errors['email'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a valid email address');
        } elseif ($form->hasValidData( 'Email' )) {
            $chat->email = $form->Email;
        }
    
        if ($form->hasValidData( 'UserNick' ) && $form->UserNick != '' && strlen($form->UserNick) > 50)
        {
            $Errors['nick'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 50 characters');
        }
    
        if ($form->hasValidData( 'UserPhone' )) {
            $chat->phone = $form->UserPhone;
        }
    
        if ($form->hasValidData( 'UserNick' ) && $form->UserNick != '')
        {
            $chat->nick = $form->UserNick;
        } else {
            $Errors['nick'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
        }
    
        return $Errors;
    }
    
    public static function validateUpdateAttribute($chat, $dataUpdate)
    {
    	if (is_array($dataUpdate)) {
    		$currentChatData = json_decode($chat->additional_data, true);
    		
    		if (!is_array($currentChatData)) {
    			$currentChatData = array();
    		}

    		$fieldsToUpdate = array_keys($dataUpdate);
	
    		// Removed hidden admin fields
    		foreach ($currentChatData as $key => $data) {
    			/**
    			 * Clean only if
    			 * 1. Field is hidden
    			 * 2. Field is hidden manually
    			 * 3. Field is back office custom field
    			 * */
    			if (in_array($data['key'], $fieldsToUpdate)) {
    				unset($currentChatData[$key]);
    			}
    		}

    		foreach ($dataUpdate as $field => $attrValue) {
    			
    			$valueStore = $attrValue['val'];
    			$hiddenField = false;
    			
    			if (isset($attrValue['enc']) && $attrValue['enc'] == true) {
    				$hiddenField = true;
	    			try {
	                     $valueStore = self::decryptAdditionalField($valueStore, $chat);
	                } catch (Exception $e) {
	                     $valueStore = $e->getMessage();
	                }
    			}
    			
    			$currentChatData[] = array('h' => $hiddenField, 'key' => $field, 'value' => $valueStore);
    		}
    		
    		// To reset index
    		$currentChatData = array_values($currentChatData);
    		
    		if (!empty($currentChatData)) {
    			$chat->additional_data = json_encode($currentChatData);
    		}
    	}
    }
}

?>