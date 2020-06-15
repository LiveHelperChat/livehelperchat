<?php

/**
 * Class used for validator
 * */

class erLhcoreClassChatValidator {

    public static function validateChatModifyCore(& $chat)
    {
        $definition = array(
            'DepartmentID' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'unanswered_chat' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        $currentUser = erLhcoreClassUser::instance();

        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invalid CSRF token!');
        }

        if ($form->hasValidData( 'DepartmentID' ))
        {
            $chat->dep_id = $form->DepartmentID;
        }

        if ($form->hasValidData( 'unanswered_chat' ) && $form->unanswered_chat == 1) {
            $chat->unanswered_chat = 1;
        } else {
            $chat->unanswered_chat = 0;
        }

        return $Errors;
    }

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
		
		if ($form->hasValidData( 'UserNick' ) && $form->UserNick != '' && mb_strlen($form->UserNick) > 100)
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

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_name_visible_in_popup']) && $start_data_fields['offline_name_visible_in_popup'] == true))) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) || isset($additionalParams['offline']))) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_message_visible_in_popup']) && $start_data_fields['offline_message_visible_in_popup'] == true))) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_phone_visible_in_popup']) && $start_data_fields['offline_phone_visible_in_popup'] == true))) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['tos_visible_in_popup']) && $start_data_fields['tos_visible_in_popup'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_tos_visible_in_popup']) && $start_data_fields['offline_tos_visible_in_popup'] == true))) {
                $validationFields['AcceptTOS'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'boolean' );
            }
            
        } else {
            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_name_visible_in_page_widget']) && $start_data_fields['offline_name_visible_in_page_widget'] == true))) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) || isset($additionalParams['offline']))) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_message_visible_in_page_widget']) && $start_data_fields['offline_message_visible_in_page_widget'] == true))) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_phone_visible_in_page_widget']) && $start_data_fields['offline_phone_visible_in_page_widget'] == true))) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }     

            if ((isset($additionalParams['collect_all']) && $additionalParams['collect_all'] == true) || ((isset($start_data_fields['tos_visible_in_page_widget']) && $start_data_fields['tos_visible_in_page_widget'] == true && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_tos_visible_in_page_widget']) && $start_data_fields['offline_tos_visible_in_page_widget'] == true))) {
            	$validationFields['AcceptTOS'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'boolean' );
            }
        }

        $validationFields['ProductID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1));
        $validationFields['DepartamentID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => -1));
        $validationFields['DepartmentIDDefined'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY);
        $validationFields['ProductIDDefined'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY);
        $validationFields['operator'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1));
        $validationFields['user_timezone'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
        $validationFields['HasProductID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean');
        $validationFields['keyUpStarted'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1));
        $validationFields['bot_id'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1));
        $validationFields['tag'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string');

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

        $validationFields['jsvar'] = new ezcInputFormDefinitionElement(
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

                $captchaString = '';
                if (isset($_POST['tscaptcha'])) {
                    $captchaString = $_POST['tscaptcha'];
                } elseif (isset($additionalParams['payload_data']['tscaptcha'])) {
                    $captchaString = $additionalParams['payload_data']['tscaptcha'];
                }

    	        $nameField = 'captcha_'.sha1(erLhcoreClassIPDetect::getIP().$captchaString.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));

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

        if (isset($additionalParams['payload_data'])) {
            $form = new erLhcoreClassInputForm(INPUT_GET, $validationFields, null, $additionalParams['payload_data']);
        } else {
            $form = new ezcInputForm( INPUT_POST, $validationFields );
        }

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
            	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time() - 1800 || $hashCaptcha != sha1($_SERVER['REMOTE_ADDR'].$form->$nameField.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ))){
            		$Errors['captcha'] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
            	}
            } else {
            	// Captcha validation
            	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time() - 1800 )
            	{
            		$Errors['captcha'] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
            	}
            }
        }

        if (isset($validationFields['Username'])) {
            if (
                ((!$form->hasValidData( 'Username' ) || $form->Username == '') && ($start_data_fields['name_require_option'] == 'required' && !isset($additionalParams['offline']))) ||
                ((!$form->hasValidData( 'Username' ) || $form->Username == '') && isset($additionalParams['offline']) && isset($start_data_fields['offline_name_require_option']) && $start_data_fields['offline_name_require_option'] == 'required' )
            )
            {
                if (!($inputForm->only_bot_online == 1 && isset($start_data_fields['name_hidden_bot']) && $start_data_fields['name_hidden_bot'] == true) && !isset($additionalParams['ignore_required'])) {
                    $Errors['nick'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
                }

            } elseif ($form->hasValidData( 'Username' )) {
                $chat->nick = $inputForm->username = $form->Username;
            }

            if ($form->hasValidData( 'Username' ) && $form->Username != '' && mb_strlen($form->Username) > 100 && !isset($additionalParams['ignore_required']))
            {
                $Errors['nick'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters');
            }
        }

        if ( isset($validationFields['Email']) ) {
            if ( (!$form->hasValidData( 'Email' ) && $start_data_fields['email_require_option'] == 'required') || (!$form->hasValidData( 'Email' ) && isset($additionalParams['offline'])) ) {

                if (!($inputForm->only_bot_online == 1 && isset($start_data_fields['email_hidden_bot']) && $start_data_fields['email_hidden_bot'] == true) && !isset($additionalParams['ignore_required'])) {
                    $Errors['email'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a valid email address');
                }

            } elseif ( $form->hasValidData( 'Email' ) ) {
                $chat->email = $inputForm->email = $form->Email;
            } else {
                $chat->email = $inputForm->email = isset($_POST['Email']) ? $_POST['Email'] : '';
            }
        }
        
        // Validate question
        if (isset($validationFields['Question'])) {

            if ( !$form->hasValidData('keyUpStarted') && (!$form->hasValidData( 'Question' ) || (trim($form->Question) == '' && (($start_data_fields['message_require_option'] == 'required' && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_message_require_option']) && $start_data_fields['offline_message_require_option'] == 'required'))))) {
                if (!($inputForm->only_bot_online == 1 && isset($start_data_fields['message_hidden_bot']) && $start_data_fields['message_hidden_bot'] == true) && !isset($additionalParams['ignore_required'])) {
                    $Errors['question'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
                }
            } elseif ($form->hasValidData( 'Question' )) {
                $inputForm->question = trim($form->Question);
            }

            if ($form->hasValidData( 'Question' ) && trim($form->Question) != '' && mb_strlen($form->Question) > (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value && !isset($additionalParams['ignore_required']))
            {
                $Errors['question'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum').' '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters for a message');
            }
        }
       
        if (isset($validationFields['AcceptTOS'])) {
        	if ( (!$form->hasValidData( 'AcceptTOS' ) || $form->AcceptTOS == false) && !isset($additionalParams['ignore_required'])) {
        		$Errors['accept_tos'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You have to accept our Terms Of Service');
        	} else {
        		$inputForm->accept_tos = true;
        	}
        }

        
        // Validate phone
        if (isset($validationFields['Phone'])) {
            if (((!$form->hasValidData( 'Phone' ) || $form->Phone == '' || mb_strlen($form->Phone) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value) && ( ($start_data_fields['phone_require_option'] == 'required' && !isset($additionalParams['offline'])) || (isset($additionalParams['offline']) && isset($start_data_fields['offline_phone_require_option']) && $start_data_fields['offline_phone_require_option'] == 'required')))) {
                if (!($inputForm->only_bot_online == 1 && isset($start_data_fields['phone_hidden_bot']) && $start_data_fields['phone_hidden_bot'] == true) && !isset($additionalParams['ignore_required'])) {
                    $Errors['phone'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone');
                }
            } elseif ($form->hasValidData( 'Phone' )) {
                $chat->phone = $inputForm->phone = $form->Phone;
            }

            if ($form->hasValidData( 'Phone' ) && $form->Phone != '' && mb_strlen($form->Phone) > 100 && !isset($additionalParams['ignore_required']))
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
         	
         	if (isset($_FILES['File']) && $_FILES['File']['error'] != 4) { // No file was provided
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
        
        if ($form->hasValidData( 'user_timezone' ) && is_numeric($form->user_timezone)) {
        	$timezone_name = timezone_name_from_abbr(null, (int)$form->user_timezone*3600, true);
        	if ($timezone_name !== false) {
        		$chat->user_tz_identifier = $timezone_name;
        	} else {
        		$chat->user_tz_identifier = '';
        	}
        } else if ($form->hasValidData( 'user_timezone' ) && self::isValidTimezoneId2($form->user_timezone)) {
            $chat->user_tz_identifier = $form->user_timezone;
        }

        if ($form->hasValidData( 'DepartmentIDDefined' )) {
        	$inputForm->departament_id_array = $form->DepartmentIDDefined;
        }

        if ($form->hasValidData( 'bot_id' )) {
        	$inputForm->bot_id = $form->bot_id;
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
        		    if (!isset($additionalParams['ignore_required'])){
                        $Errors['additional_'.$key] = trim($name_item).' : '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
                    }
        		}
        		
        		$valueStore = isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '';
        		
        		if (isset($inputForm->encattr[$key]) && $inputForm->encattr[$key] == 't' && $valueStore != '') {
        		    try {
        		        $valueStore = self::decryptAdditionalField($valueStore, $chat);
        		    } catch (Exception $e) {
        		        $Errors[] = $e->getMessage();
        		    }
        		}
        		
        		$stringParts[] = array('h' => (isset($inputForm->value_types[$key]) && $inputForm->value_types[$key] == 'hidden' ? true : false), 'key' => $name_item, 'value' => $valueStore);
        	}
        }

        if (isset($start_data_fields['custom_fields']) && $start_data_fields['custom_fields'] != '') {
            $customAdminfields = json_decode($start_data_fields['custom_fields'],true);
            
            $valuesArray = array();
            
            // Fill values if exists
            if ($form->hasValidData( 'value_items_admin' )){
                $inputForm->value_items_admin = $valuesArray = $form->value_items_admin;
            }

            // If data comes from payload we process it a different way
            if (isset($additionalParams['payload_data'])) {
                foreach ($customAdminfields as $key => $adminField) {
                    if (isset($additionalParams['payload_data']['value_items_admin_' . $key])) {
                        $inputForm->value_items_admin[$key] = $valuesArray[$key] = $additionalParams['payload_data']['value_items_admin_' . $key];
                    }
                }
            }

            if ($form->hasValidData( 'via_hidden' )){
                $inputForm->via_hidden = $form->via_hidden;
            }
            
            if ($form->hasValidData( 'via_encrypted' )) {
                $inputForm->via_encrypted = $form->via_encrypted;
            }

            if (is_array($customAdminfields)) {
                foreach ($customAdminfields as $key => $adminField) {

                    if ((isset($adminField['isrequired']) && ($adminField['isrequired'] == 'true' || $adminField['isrequired'] == 1) && !isset($inputForm->value_items_admin[$key])) || (isset($inputForm->value_items_admin[$key]) && isset($adminField['isrequired']) && ($adminField['isrequired'] == 'true' || $adminField['isrequired'] == 1) && ($adminField['visibility'] == 'all' || $adminField['visibility'] == (isset($additionalParams['offline']) ? 'off' : 'on')) && (!isset($valuesArray[$key]) || trim($valuesArray[$key]) == ''))) {
                        $Errors[(isset($additionalParams['payload_data']) ? 'value_items_admin_' . $key : 'additional_admin_' . $key)] = trim($adminField['fieldname']).': '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
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

        $refererALL = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';

        if ($refererALL != '' && isset($start_data_fields['custom_fields_url']) && $start_data_fields['custom_fields_url'] != '') {
            $queryURL = array();
            preg_match('/(\?|\:\:)(.*?)$/',$refererALL,$queryURL);

            if (isset($queryURL[2]))
            {
                $referer = $queryURL[2];

                $matchesArray = array();
                preg_match_all('/(.*?)\=(.*?)(\&|\;|$)/',$referer,$matchesArray);

                $argumentsFormatted = array();
                foreach ($matchesArray[1] as $index => $value) {
                    $argumentsFormatted[$value] = $matchesArray[2][$index];
                }

                if ($referer != '') {
                    $customURLfields = json_decode($start_data_fields['custom_fields_url'],true);
                    if (is_array($customURLfields)) {
                        foreach ($customURLfields as $key => $adminField) {
                            if (isset($argumentsFormatted[$adminField['fieldidentifier']])) {
                                $stringParts[] = array('url' => true, 'identifier' => (isset($adminField['fieldidentifier'])) ? $adminField['fieldidentifier'] : null, 'key' => $adminField['fieldname'], 'value' => $argumentsFormatted[$adminField['fieldidentifier']]);
                            }
                        }
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

        // We set custom chat locale only if visitor is not using default siteaccss and default langauge is not english.
        if (erConfigClassLhConfig::getInstance()->getSetting('site','default_site_access') != erLhcoreClassSystem::instance()->SiteAccess) {
            $siteAccessOptions = erConfigClassLhConfig::getInstance()->getSetting('site_access_options', erLhcoreClassSystem::instance()->SiteAccess);
            // Never override to en
            if (isset($siteAccessOptions['content_language']) && $siteAccessOptions['content_language'] != 'en') {
                $chat->chat_locale = $siteAccessOptions['content_language'];
            }
        }

        // Javascript variables
        if ( $form->hasValidData( 'jsvar' ) && !empty($form->jsvar))
        {
            $inputForm->jsvar = $form->jsvar;
            foreach (erLhAbstractModelChatVariable::getList(array('customfilter' => array('dep_id = 0 OR dep_id = ' . (int)$chat->dep_id))) as $jsVar) {
                if (isset($form->jsvar[$jsVar->id]) && !empty($form->jsvar[$jsVar->id])) {

                    if (strpos($jsVar->var_identifier,'lhc.') !== false) {
                        $lhcVar = str_replace('lhc.','',$jsVar->var_identifier);
                        if ($chat->{$lhcVar} != $form->jsvar[$jsVar->id] && $form->jsvar[$jsVar->id] != '') {
                            $chat->{$lhcVar} = $form->jsvar[$jsVar->id];
                        }
                    } else {

                        $val = $form->jsvar[$jsVar->id];
                        if ($jsVar->type == 0) {
                            $val = (string)$val;
                        } elseif ($jsVar->type == 1) {
                            $val = (int)$val;
                        } elseif ($jsVar->type == 2) {
                            $val = (real)$val;
                        } elseif ($jsVar->type == 3) {
                            try {
                                $val = self::decryptAdditionalField($val, $chat);
                            } catch (Exception $e) {
                                $val = $e->getMessage();
                            }
                        }

                        $stringParts[] = array('h' => false, 'identifier' => $jsVar->var_identifier, 'key' => $jsVar->var_name, 'value' => $val);

                    }

                }
            }
        }

        if ( $form->hasValidData( 'tag' ) && !empty($form->tag))
        {
            $stringParts[] = array('h' => false, 'identifier' => 'tag', 'key' => 'Tags', 'value' => $form->tag);
            $inputForm->tag = $form->tag;
        }

        if (!empty($stringParts)) {
            $chat->additional_data = json_encode($stringParts);
            $chat->additional_data_array = $stringParts;
        }

        $chat->setIP();
        $chat->lsync = time();
        erLhcoreClassModelChat::detectLocation($chat, $inputForm->vid);

        // Detect device
        $detect = new Mobile_Detect;
        $chat->uagent = $detect->getUserAgent();
        $chat->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);

        // Set priority by additional variables
        $priority = self::getPriorityByAdditionalData($chat);

        if ($priority !== false && $priority > $chat->priority) {
            $chat->priority = $priority;
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_start_chat',array('errors' => & $Errors, 'input_form' => & $inputForm, 'start_data_fields' => & $start_data_fields, 'chat' => & $chat,'additional_params' => & $additionalParams));
        
        return $Errors;
    }

    public static function validateJSVarsVisitor($visitor, $data) {

        $onlineAttr = $visitor->online_attr_array;

        foreach (erLhAbstractModelChatVariable::getList(array('customfilter' => array('dep_id = 0 OR dep_id = ' . (int)$visitor->dep_id))) as $jsVar) {

            if (isset($onlineAttr[$jsVar->var_identifier]) && $jsVar->persistent == 0) {
                unset($onlineAttr[$jsVar->var_identifier]);
            }

            $val = null;

            if (isset($data[str_replace('lhc_var.','',$jsVar->js_variable)]) && !empty(str_replace('lhc_var.','',$jsVar->js_variable))) {
                $val = $data[str_replace('lhc_var.','',$jsVar->js_variable)];
            } elseif (isset($data[$jsVar->id]) && !empty($data[$jsVar->id])) {
                $val = $data[$jsVar->id];
            }

            if (!empty($val)) {
                if ($jsVar->type == 0) {
                    $val = (string)$val;
                } elseif ($jsVar->type == 1) {
                    $val = (int)$val;
                } elseif ($jsVar->type == 2) {
                    $val = (real)$val;
                } elseif ($jsVar->type == 3) {
                    try {
                        $val = self::decryptAdditionalField($val);
                    } catch (Exception $e) {
                        $val = $e->getMessage();
                    }
                }

                if ($jsVar->var_identifier == 'lhc.nick' && $val != '') {
                    $onlineAttrSystem = $visitor->online_attr_system_array;
                    $onlineAttrSystem['username'] = $val;
                    $visitor->online_attr_system = json_encode($onlineAttrSystem);
                    $visitor->online_attr_system_array =$onlineAttrSystem;
                }

                $onlineAttr[$jsVar->var_identifier] =  array('h' => false, 'identifier' => $jsVar->var_identifier, 'key' => $jsVar->var_name, 'value' => $val);
            }
        }

        $visitor->online_attr = json_encode($onlineAttr);
        $visitor->saveThis();

    }

    public static function isValidTimezoneId2($tzid) {
        $valid = array();
        $tza = timezone_abbreviations_list();
        foreach ($tza as $zone)
            foreach ($zone as $item)
                $valid[$item['timezone_id']] = true;
        unset($valid['']);
        return !!$valid[$tzid];
    }

    public static function validateJSVarsChat($chat, $data) {

        $additionalDataArray = $chat->additional_data_array;

        if ( !empty($data))
        {
            $needUpdate = false;
            $stringParts = array();
            $updateColumns = array(
                'additional_data',
                'priority',
                'operation_admin',
            );

            foreach (erLhAbstractModelChatVariable::getList(array('customfilter' => array('dep_id = 0 OR dep_id = ' . (int)$chat->dep_id))) as $jsVar) {

                if (isset($data[str_replace('lhc_var.','',$jsVar->js_variable)]) && !empty(str_replace('lhc_var.','',$jsVar->js_variable))) {
                    $val = $data[str_replace('lhc_var.','',$jsVar->js_variable)];
                } elseif (isset($data[$jsVar->id]) && !empty($data[$jsVar->id])) {
                    $val = $data[$jsVar->id];
                } else {
                    $val = null;
                }

                if (!empty($val)) {
                    if (strpos($jsVar->var_identifier,'lhc.') !== false) {
                        $lhcVar = str_replace('lhc.','',$jsVar->var_identifier);
                        if ($chat->{$lhcVar} != $val && $val != '') {
                            $chat->{$lhcVar} = $val;
                            $updateColumns[] = $lhcVar;
                            $needUpdate = true;
                        }
                    } else {
                        if ($jsVar->type == 0) {
                            $val = (string)$val;
                        } elseif ($jsVar->type == 1) {
                            $val = (int)$val;
                        } elseif ($jsVar->type == 2) {
                            $val = (real)$val;
                        } elseif ($jsVar->type == 3) {
                            try {
                                $val = self::decryptAdditionalField($val, $chat);
                            } catch (Exception $e) {
                                $val = $e->getMessage();
                            }
                        }
                        $stringParts[] = array('h' => false, 'identifier' => $jsVar->var_identifier, 'key' => $jsVar->var_name, 'value' => $val);
                    }
                }
            }

            $identifiersUpdated = array();
            foreach ($additionalDataArray as  & $item) {
                foreach ($stringParts as $newItem) {
                    if ($item['identifier'] == $newItem['identifier']) {
                        if ( $newItem['value'] != $item['value'] ) {
                            $item['value'] = $newItem['value'];
                            $needUpdate = true;
                        }
                        $identifiersUpdated[] = $newItem['identifier'];
                    }
                }
            }

            foreach ($stringParts as $newItem) {
                if (!in_array($newItem['identifier'],$identifiersUpdated)){
                    $additionalDataArray[] = $newItem;
                    $needUpdate = true;
                }
            }

            if ($needUpdate == true) {
                $chat->additional_data_array = $additionalDataArray;
                $chat->additional_data = json_encode($additionalDataArray);
                $chat->operation_admin = 'lhinst.updateVoteStatus(' . $chat->id . ');';

                // Set priority if we find new after fetching additional data
                $priority = erLhcoreClassChatValidator::getPriorityByAdditionalData($chat);

                if ($priority !== false && $priority > $chat->priority) {
                    $chat->priority = $priority;
                }

                $db = ezcDbInstance::get();
                $db->beginTransaction();

                $chat->updateThis(array('update' => $updateColumns));

                $db->commit();

                // Perhaps someone is listening for chat modifications
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.modified', array('chat' => & $chat, 'params' => array()));
            }
        }
    }

    public static function updateAdditionalVariables($chat) {

        $validationFields = array();

        $validationFields['jsvar'] = new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string',
            null,
            FILTER_REQUIRE_ARRAY
        );

        $form = new ezcInputForm( INPUT_GET, $validationFields );

        $additionalDataArray = $chat->additional_data_array;

        if ( $form->hasValidData( 'jsvar' ) && !empty($form->jsvar))
        {
            $needUpdate = false;
            $stringParts = array();
            $updateColumns = array(
                'additional_data',
                'priority',
                'operation_admin',
            );

            foreach (erLhAbstractModelChatVariable::getList(array('customfilter' => array('dep_id = 0 OR dep_id = ' . (int)$chat->dep_id))) as $jsVar) {
                if (isset($form->jsvar[$jsVar->id]) && !empty($form->jsvar[$jsVar->id])) {
                    if (strpos($jsVar->var_identifier,'lhc.') !== false) {
                        $lhcVar = str_replace('lhc.','',$jsVar->var_identifier);
                        if ($chat->{$lhcVar} != $form->jsvar[$jsVar->id] && $form->jsvar[$jsVar->id] != '') {
                            $chat->{$lhcVar} = $form->jsvar[$jsVar->id];
                            $updateColumns[] = $lhcVar;
                        }
                    } else {
                        $val = $form->jsvar[$jsVar->id];
                        if ($jsVar->type == 0) {
                            $val = (string)$val;
                        } elseif ($jsVar->type == 1) {
                            $val = (int)$val;
                        } elseif ($jsVar->type == 2) {
                            $val = (real)$val;
                        }
                        $stringParts[] = array('h' => false, 'identifier' => $jsVar->var_identifier, 'key' => $jsVar->var_name, 'value' => $val);
                    }
                }
            }

            $identifiersUpdated = array();
            foreach ($additionalDataArray as  & $item) {
                foreach ($stringParts as $newItem) {
                    if (isset($item['identifier']) && $item['identifier'] == $newItem['identifier']) {
                        if ( $newItem['value'] != $item['value'] ) {
                            $item['value'] = $newItem['value'];
                            $needUpdate = true;
                        }
                        $identifiersUpdated[] = $newItem['identifier'];
                    }
                }
            }

            foreach ($stringParts as $newItem) {
                if (!in_array($newItem['identifier'],$identifiersUpdated)){
                    $additionalDataArray[] = $newItem;
                    $needUpdate = true;
                }
            }

            if ($needUpdate == true) {

                $chat->additional_data_array = $additionalDataArray;
                $chat->additional_data = json_encode($additionalDataArray);
                $chat->operation_admin = 'lhinst.updateVoteStatus(' . $chat->id . ');';

                // Set priority if we find new after fetching additional data
                $priority = erLhcoreClassChatValidator::getPriorityByAdditionalData($chat);

                if ($priority !== false && $priority > $chat->priority) {
                    $chat->priority = $priority;
                }

                $db = ezcDbInstance::get();
                $db->beginTransaction();

                $chat->updateThis(array('update' => $updateColumns));

                $db->commit();

                // Perhaps someone is listening for chat modifications
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.modified', array('chat' => & $chat, 'params' => array()));
            }
        }

    }

    public static function getPriorityByAdditionalData($chat)
    {
        $priorityRules = erLhAbstractModelChatPriority::getList(array('sort' => 'dep_id DESC, priority DESC','customfilter' => array('dep_id = 0 OR dep_id = ' .(int)$chat->dep_id)));

        foreach ($priorityRules as $priorityRule) {

            $ruleMatched = true;

            foreach ($priorityRule->value_array as $rule) {
                $valueToCompare = null;
                if (strpos($rule['field'],'additional_data') !== false) {
                    $additionalDataArray = $chat->additional_data_array;

                    if (is_array($additionalDataArray)) {
                        foreach ($additionalDataArray as $additionalItem) {
                            $valueCompare = false;

                            if (isset($additionalItem['identifier'])){
                                $valueCompare = $additionalItem['identifier'];
                            } elseif (isset($additionalItem['key'])){
                                $valueCompare = $additionalItem['key'];
                            }

                            if ($valueCompare !== false && $valueCompare == str_replace('additional_data.','',$rule['field'])) {
                                $valueToCompare = $additionalItem['value'];
                                break;
                            }
                        }
                    }
                } elseif (strpos($rule['field'],'chat_variable') !== false) {
                    $additionalDataArray = $chat->chat_variables_array;
                    if (is_array($additionalDataArray)) {
                        $variableName = str_replace('chat_variable.','', $rule['field']);
                        if (isset($chat->chat_variables_array[$variableName]) && $chat->chat_variables_array[$variableName] != '') {
                            $valueToCompare = $chat->chat_variables_array[$variableName];
                        }
                    }
                } elseif (strpos($rule['field'],'lhc.') !== false) {
                    $variableName = str_replace('lhc.','', $rule['field']);
                    if (isset($chat->{$variableName}) && $chat->{$variableName} != '') {
                        $valueToCompare = $chat->{$variableName};
                    }
                }

                if ($valueToCompare !== null) {
                    if ($rule['comparator'] == '=' && $rule['value'] != $valueToCompare) {
                        $ruleMatched = false;
                        break;
                    } else if ($rule['comparator'] == '>' && ($valueToCompare > $rule['value']) == false) {
                        $ruleMatched = false;
                        break;
                    } else if ($rule['comparator'] == '>=' && ($valueToCompare >= $rule['value']) == false) {
                        $ruleMatched = false;
                        break;
                    } else if ($rule['comparator'] == '<' && ($valueToCompare < $rule['value']) == false) {
                        $ruleMatched = false;
                        break;
                    } else if ($rule['comparator'] == '<=' && ($valueToCompare <= $rule['value']) == false) {
                        $ruleMatched = false;
                        break;
                    }
                } else {
                    $ruleMatched = false;
                    break;
                }
            }

            if ($ruleMatched == true) {
                return $priorityRule->priority;
            }
        }

        return false;
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
            ),
            'jsvar' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
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
                
                if (!is_array($currentChatData)) {
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

        // Update variables from jsvar
        if ( $form->hasValidData( 'jsvar' ) && !empty($form->jsvar))
        {
            $stringParts = array();

            foreach (erLhAbstractModelChatVariable::getList(array('customfilter' => array('dep_id = 0 OR dep_id = ' . (int)$chat->dep_id))) as $jsVar) {
                if (isset($form->jsvar[$jsVar->id]) && !empty($form->jsvar[$jsVar->id])) {

                    if (strpos($jsVar->var_identifier,'lhc.') !== false) {
                        $lhcVar = str_replace('lhc.','', $jsVar->var_identifier);
                        if ($chat->{$lhcVar} != $form->jsvar[$jsVar->id] && $form->jsvar[$jsVar->id] != '') {
                            $chat->{$lhcVar} = $form->jsvar[$jsVar->id];
                        }
                    } else {
                        $val = $form->jsvar[$jsVar->id];
                        if ($jsVar->type == 0) {
                            $val = (string)$val;
                        } elseif ($jsVar->type == 1) {
                            $val = (int)$val;
                        } elseif ($jsVar->type == 2) {
                            $val = (real)$val;
                        }
                        $stringParts[] = array('h' => false, 'identifier' => $jsVar->var_identifier, 'key' => $jsVar->var_name, 'value' => $val);
                    }
                }
            }

            if (!isset($currentChatData)) {
                $currentChatData = json_decode($chat->additional_data,true);
                if (!is_array($currentChatData)) {
                    $currentChatData = array();
                }
            }

            $identifiersUpdated = array();
            foreach ($currentChatData as  & $item) {
                foreach ($stringParts as $newItem) {
                    if ($item['identifier'] == $newItem['identifier']) {
                        if ( $newItem['value'] != $item['value'] ) {
                            $item['value'] = $newItem['value'];
                        }
                        $identifiersUpdated[] = $newItem['identifier'];
                    }
                }
            }

            foreach ($stringParts as $newItem) {
                if (!in_array($newItem['identifier'],$identifiersUpdated)){
                    $currentChatData[] = $newItem;
                }
            }
        }

        // To reset index
        $currentChatData = array_values($currentChatData);
        
        if (!empty($currentChatData)) {
            $chat->additional_data = json_encode($currentChatData);
            $chat->additional_data_array = $currentChatData;
        }
    }
    
    public static function decryptAdditionalField($valueStore, $chat = null)
    {
        if ($valueStore != '') {

            if ($chat !== null) {
                $startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $chat->dep_id)));

                if ($startDataDepartment instanceof erLhcoreClassModelChatStartSettings) {
                    $startData = $startDataDepartment->data_array;
                } else {
                    $startData = (array)erLhcoreClassModelChatConfig::fetch('start_chat_data')->data;
                }

            } else {
                $startData = (array)erLhcoreClassModelChatConfig::fetch('start_chat_data')->data;
            }

            $keyDecrypt = (isset($startData['custom_fields_encryption']) && !empty($startData['custom_fields_encryption']) ? $startData['custom_fields_encryption'] : null);

            if ($keyDecrypt !== null) {
                $valueStore = lhSecurity::decrypt($valueStore,$keyDecrypt);

                if ($valueStore === false) {
                    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Could not decrypt data!'));
                }
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
    
        if ($form->hasValidData( 'UserNick' ) && $form->UserNick != '' && mb_strlen($form->UserNick) > 100)
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

    /**
     *
     * @desc
     *
     * @param $params
     *
     */
    public static function saveOfflineRequest($params) {

        $offlineData = erLhcoreClassModelChatConfig::fetch('offline_settings');
        $data = (array)$offlineData->data;

        if (!isset($data['do_not_save_offline']) || $data['do_not_save_offline'] == 0)
        {
            // Save as offline request
            $params['chat']->time = $params['chat']->pnd_time = time();
            $params['chat']->lsync = time();
            $params['chat']->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
            $params['chat']->status_sub = erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST;
            $params['chat']->hash = erLhcoreClassChat::generateHash();
            $params['chat']->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
            $params['chat']->session_referrer = isset($_POST['r']) ? $_POST['r'] : '';

            if ( empty($params['chat']->nick) ) {
                $params['chat']->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
            }

            $params['chat']->saveThis();

            if ( $params['question'] != '' ) {
                // Store question as message
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = trim($params['question']);
                $msg->chat_id = $params['chat']->id;
                $msg->user_id = 0;
                $msg->time = time();
                erLhcoreClassChat::getSession()->save($msg);

                $params['chat']->unanswered_chat = 0;
                $params['chat']->last_msg_id = $msg->id;
                $params['chat']->saveThis();
            }

            if (isset($data['close_offline']) && $data['close_offline'] == 1) {
                erLhcoreClassChatHelper::closeChat(array(
                    'chat' => & $params['chat'],
                    'user' => false
                ));
            }
        }
    }

    // Set's chat as a bot
    public static function setBot(& $chat, $params = array()) {

        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_set_bot', array(
            'chat' => & $chat,
            'params' => $params
        ));

        if ($handler !== false) {
             return;
        }

        if ((isset($params['bot_id']) && $params['bot_id'] > 0) || (isset($chat->department->bot_configuration_array['bot_id']) && is_numeric($chat->department->bot_configuration_array['bot_id']) && $chat->department->bot_configuration_array['bot_id'] > 0)) {

            if (isset($params['bot_id']) && $params['bot_id'] > 0) {
                $bot = erLhcoreClassModelGenericBotBot::fetch($params['bot_id']);
                if (isset($params['bot_only_offline']) && $params['bot_only_offline'] === true) {
                    $botConfiguration['bot_only_offline'] = true;
                }
            } else {
                $botConfiguration = $chat->department->bot_configuration_array;
                $bot = erLhcoreClassModelGenericBotBot::fetch($botConfiguration['bot_id']);
            }

            if (
                $bot instanceof erLhcoreClassModelGenericBotBot && ((isset($params['bot_id']) && isset($params['trigger_id'])) ||
                    (
                        (!isset($botConfiguration['bot_only_offline']) || $botConfiguration['bot_only_offline'] == false) ||
                        (isset($botConfiguration['bot_only_offline']) && $botConfiguration['bot_only_offline'] == true && erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true)) == false)
                    )
                )) {

                $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
                $chat->gbot_id = $bot->id;

                if (!isset($params['ignore_default']) || $params['ignore_default'] == false)
                {
                    if (isset($params['trigger_id']) && $params['trigger_id'] > 0) {
                        $botTrigger = erLhcoreClassModelGenericBotTrigger::fetch($params['trigger_id']);
                    } else {
                        $botIds = $bot->getBotIds();
                        // Find default messages if there are any
                        $botTrigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filterin' => array('bot_id' => $botIds), 'filter' => array('default' => 1)));
                    }

                    if ($botTrigger instanceof erLhcoreClassModelGenericBotTrigger && (!isset($params['trigger_id_executed']) || $params['trigger_id_executed'] != $botTrigger->id)) {

                        // set flag that we are executing everthing in start chat mode
                        erLhcoreClassGenericBotWorkflow::$startChat = true;

                        if (isset($params['msg']) && $chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                            $chatVariables = $chat->chat_variables_array;
                            if (!isset($chatVariables['msg_v'])) {
                                $chatVariables['msg_v'] = 1;
                            } else {
                                $chatVariables['msg_v']++;
                            }
                            $chat->chat_variables_array = $chatVariables;
                            $chat->chat_variables = json_encode($chatVariables);
                        }

                        $message = erLhcoreClassGenericBotWorkflow::processTrigger($chat, $botTrigger, false, array('args' => $params));

                        if (isset($params['trigger_button_id'])) {
                            $chat->saveThis();
                            erLhcoreClassGenericBotWorkflow::processTriggerClick($chat, $message, $params['trigger_button_id'], array('processed' => (isset($params['processed']) && $params['processed'] == true)));
                        } else if (isset($params['trigger_payload_id'])) {
                            $chat->saveThis();
                            erLhcoreClassGenericBotWorkflow::processButtonClick($chat, $message, $params['trigger_payload_id'], array('processed' => (isset($params['processed']) && $params['processed'] == true)));
                        }

                        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                            $chat->last_msg_id = $message->id;
                        }
                    }
                }

                $chat->saveThis();
            }
        }
    }

    public static function setLanguageByBrowser($return = false) {
        // Detect user locale
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $parts = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $languages = explode(',',$parts[0]);
            if (isset($languages[0])) {
                $locale = $languages[0];

                $db = ezcDbInstance::get();
                $stmt = $db->prepare('SELECT `siteaccess` FROM `lh_speech_language` INNER JOIN `lh_speech_language_dialect` ON `lh_speech_language_dialect`.`language_id` = `lh_speech_language`.`id` WHERE (`lh_speech_language_dialect`.`lang_code` = :lang_code OR `lh_speech_language_dialect`.`short_code` = :short_code)');
                $stmt->bindValue(':lang_code', $locale, PDO::PARAM_STR);
                $stmt->bindValue(':short_code', $locale, PDO::PARAM_STR);
                $stmt->execute();

                $siteAccess = $stmt->fetchColumn();

                if ($siteAccess != '') {
                    if ($return == true) {
                        return $siteAccess;
                    } else {
                        erLhcoreClassSystem::setSiteAccess($siteAccess);
                    }
                }
            }
        }
    }
    
    /*
     * Auto start chat if required
     *
     * */
    public static function validateAutoStart($params)
    {
        $paramsExecution = array();

        $skipOnlyOnlineCheck = false;
        $autoStart = false;

        if (isset($params['invitation_mode']) && $params['invitation_mode'] == 1 && isset($params['userInstance'])) {

            $invitation = erLhAbstractModelProactiveChatInvitation::fetch($params['userInstance']->invitation_id);

            if ($invitation instanceof erLhAbstractModelProactiveChatInvitation && $invitation->bot_id > 0 && $invitation->trigger_id > 0) {
                
                $autoStart = true;

                if ($invitation->bot_offline == false) {
                    $skipOnlyOnlineCheck = true;
                }

                $paramsExecution['bot_id'] = $invitation->bot_id;
                $paramsExecution['trigger_id'] = $invitation->trigger_id;

                $params['chat']->chat_initiator = erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE;
            }
        }

        if (isset($params['bot_id']) && is_numeric($params['bot_id']) && $params['bot_id'] > 0 && isset($params['startDataFields']['auto_start_chat']) && $params['startDataFields']['auto_start_chat'] == true) {
            $paramsExecution['bot_id'] = (int)$params['bot_id'];
            $autoStart = true;
            $skipOnlyOnlineCheck = true;
        }
        
        if (($autoStart == true && $params['chat']->dep_id > 0) || (!isset($params['invitation_mode']) && isset($params['startDataFields']['auto_start_chat']) && $params['startDataFields']['auto_start_chat'] == true && $params['chat']->dep_id > 0)) {

            $chat = $params['chat'];

            if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => erLhcoreClassIPDetect::getIP()))) > 0) {
                return false;
            }

            if ($skipOnlyOnlineCheck == true || erLhcoreClassChat::isOnlyBotOnline($chat->dep_id)) {

                $chat->setIP();
                erLhcoreClassModelChat::detectLocation($chat);

                $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value, $params['inputData']->vid);

                if ($statusGeoAdjustment['status'] == 'hidden') { // This should never happen
                    exit('Chat not available in your country');
                }

                $chat->time = $chat->pnd_time = time();
                $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                $chat->hash = erLhcoreClassChat::generateHash();

                if ( $params['inputData']->priority !== false && is_numeric($params['inputData']->priority) ) {
                    $chat->priority = (int)$params['inputData']->priority;
                } else {
                    if ($chat->department !== false) {
                        $chat->priority = $chat->department->priority;
                    }
                }

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

                if (isset($_GET['URLReferer']))
                {
                    $chat->referrer = $_GET['URLReferer'];
                }

                if (isset($_GET['r']))
                {
                    $chat->session_referrer = $_GET['r'];
                }

                $nick = trim($chat->nick);

                if ( empty($nick) ) {
                    $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
                }

                $db = ezcDbInstance::get();

                try {
                    $db->beginTransaction();

                    // Store chat
                    $chat->saveThis();

                    // Assign chat to user
                    if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
                        // To track online users
                        $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('check_message_operator' => true, 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $params['params']['user_parameters_unordered']['vid']));

                        if ($userInstance !== false) {
                            $userInstance->chat_id = $chat->id;
                            $userInstance->dep_id = $chat->dep_id;
                            $userInstance->message_seen = 1;
                            $userInstance->message_seen_ts = time();
                            $userInstance->saveThis();

                            $chat->online_user_id = $userInstance->id;
                            $chat->saveThis();

                            if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
                                erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance, erLhcoreClassModelChatConfig::fetch('footprint_background')->current_value == 1);
                            }
                        }
                    }

                    if (!empty($stringParts)) {
                        $chat->additional_data = json_encode($stringParts);
                    }

                    // Detect timezone if provided
                    if (isset($_GET['tzuser']) && !empty($_GET['tzuser'])) {
                        $timezone_name = timezone_name_from_abbr(null, $_GET['tzuser']*3600, true);
                        if ($timezone_name !== false) {
                            $chat->user_tz_identifier = $timezone_name;
                        } else {
                            $chat->user_tz_identifier = '';
                        }
                    }

                    $stringParts = array();

                    if (!empty($params['inputData']->name_items))
                    {
                        $valuesArray = $params['inputData']->value_items;

                        foreach ($params['inputData']->name_items as $key => $name_item) {

                            if (isset($params['inputData']->values_req[$key]) && $params['inputData']->values_req[$key] == 't' && ($params['inputData']->value_show[$key] == 'b' || $params['inputData']->value_show[$key] == (isset($additionalParams['offline']) ? 'off' : 'on')) && (!isset($valuesArray[$key]) || trim($valuesArray[$key]) == '')) {
                                $Errors['additional_'.$key] = trim($name_item).' : '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
                            }

                            $valueStore = isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '';

                            if (isset($params['inputData']->encattr[$key]) && $params['inputData']->encattr[$key] == 't' && $valueStore != '') {
                                try {
                                    $valueStore = self::decryptAdditionalField($valueStore, $chat);
                                } catch (Exception $e) {
                                    $Errors[] = $e->getMessage();
                                }
                            }

                            $stringParts[] = array('h' => ($params['inputData']->value_types[$key] && $params['inputData']->value_types[$key] == 'hidden' ? true : false), 'key' => $name_item, 'value' => $valueStore);
                        }
                    }

                    if (!empty($params['inputData']->value_items_admin)) {
                        if (isset($params['startDataFields']['custom_fields']) && $params['startDataFields']['custom_fields'] != '') {

                            $customAdminfields = json_decode($params['startDataFields']['custom_fields'], true);

                            $valuesArray = $params['inputData']->value_items_admin;

                            if (is_array($customAdminfields)) {
                                foreach ($customAdminfields as $key => $adminField) {

                                    if (isset($params['inputData']->value_items_admin[$key]) && isset($adminField['isrequired']) && $adminField['isrequired'] == 'true' && ($adminField['visibility'] == 'all' || $adminField['visibility'] == (isset($additionalParams['offline']) ? 'off' : 'on')) && (!isset($valuesArray[$key]) || trim($valuesArray[$key]) == '')) {
                                        $Errors['additional_admin_'.$key] = trim($adminField['fieldname']).': '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
                                    }

                                    if (isset($valuesArray[$key]) && $valuesArray[$key] != '') {

                                        $valueStore = (isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '');

                                        if (isset($params['inputData']->via_encrypted[$key]) && $params['inputData']->via_encrypted[$key] == 't' && $valueStore != '') {
                                            try {
                                                $valueStore = self::decryptAdditionalField($valueStore, $chat);
                                            } catch (Exception $e) {
                                                $valueStore = $e->getMessage();
                                            }
                                        }

                                        $stringParts[] = array('h' => (isset($params['inputData']->via_hidden[$key]) || $adminField['fieldtype'] == 'hidden'), 'identifier' => (isset($adminField['fieldidentifier'])) ? $adminField['fieldidentifier'] : null, 'key' => $adminField['fieldname'], 'value' => $valueStore);
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($stringParts)) {
                        $chat->additional_data = json_encode($stringParts);
                    }

                    // Detect user locale
                    if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        $parts = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
                        $languages = explode(',',$parts[0]);
                        if (isset($languages[0])) {
                            $chat->chat_locale = $languages[0];
                        }
                    }

                    // Detect device
                    $detect = new Mobile_Detect;
                    $chat->uagent = $detect->getUserAgent();
                    $chat->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);

                    // Set bot workflow if required
                    erLhcoreClassChatValidator::setBot($chat, $paramsExecution);

                    // Auto responder
                    $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);

                    if ($responder instanceof erLhAbstractModelAutoResponder) {
                        $beforeAutoResponderErrors = array();
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $chat, 'errors' => & $beforeAutoResponderErrors));

                        if (empty($beforeAutoResponderErrors)) {

                            $responderChat = new erLhAbstractModelAutoResponderChat();
                            $responderChat->auto_responder_id = $responder->id;
                            $responderChat->chat_id = $chat->id;
                            $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                            $responderChat->saveThis();

                            $chat->auto_responder_id = $responderChat->id;

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_message',array('chat' => & $chat, 'responder' => & $responder));

                            if ($responder->wait_message != '' && $chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = trim($responder->wait_message);
                                $msg->chat_id = $chat->id;
                                $msg->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                                $msg->user_id = -2;
                                $msg->time = time() + 5;
                                erLhcoreClassChat::getSession()->save($msg);

                                if ($chat->last_msg_id < $msg->id) {
                                    $chat->last_msg_id = $msg->id;
                                }
                            }

                            $chat->saveThis();

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered', array('chat' => & $chat));
                        } else {
                            $msg = new erLhcoreClassModelmsg();
                            $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $beforeAutoResponderErrors);
                            $msg->chat_id = $chat->id;
                            $msg->user_id = -1;
                            $msg->time = time();

                            if ($chat->last_msg_id < $msg->id) {
                                $chat->last_msg_id = $msg->id;
                            }

                            erLhcoreClassChat::getSession()->save($msg);
                        }
                    }

                    erLhcoreClassChat::updateDepartmentStats($chat->department);

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started',array('chat' => & $chat));

                    $db->commit();

                } catch (Exception $e) {
                    $db->rollback();
                }

                 if (isset($params['invitation_mode']) && $params['invitation_mode'] == 1) {
                     $callBack = '/(cstarted)/chat_started_by_invitation_cb';
                 } else {
                     $callBack = '/(cstarted)/online_chat_started_cb';
                 }

                 $baseURLBasic = '';
                 if (isset($params['popup']) && $params['popup'] == true) {
                     $baseURL = erLhcoreClassDesign::baseurlRerun('chat/chat');
                     $baseURLBasic = 'chat/chat';
                 } else {
                     $baseURL = erLhcoreClassDesign::baseurlRerun('chat/chatwidgetchat');
                     $baseURLBasic = 'chat/chatwidgetchat';
                 }

                 if (isset($params['startDataFields']['dont_auto_process']) && $params['startDataFields']['dont_auto_process'] == true) {
                     erLhcoreClassModule::redirect($baseURLBasic, '/' . $chat->id . '/' . $chat->hash . (isset($params['modeAppend']) ? $params['modeAppend'] : null) . $params['modeAppendTheme'] . $callBack);
                     exit;
                 } else {
                     // Redirect user
                     $Result = erLhcoreClassModule::reRun($baseURL . '/' . $chat->id . '/' . $chat->hash . (isset($params['modeAppend']) ? $params['modeAppend'] : null) . $params['modeAppendTheme'] . $callBack);
                 }


                return $Result;
            }
        }

        return false;
    }
}

?>