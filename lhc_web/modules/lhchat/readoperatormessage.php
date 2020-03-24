<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/readoperatormessage.tpl.php');
$tpl->set('referer','');
$tpl->set('referer_site','');
$tpl->set('theme',false);

$checkMessage = true;
if (is_numeric($Params['user_parameters_unordered']['inv'])){
    $checkMessage = false;
}

$userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'check_message_operator' => $checkMessage, 'vid' => (string)$Params['user_parameters_unordered']['vid']));

if (is_numeric($Params['user_parameters_unordered']['inv'])) {
    erLhAbstractModelProactiveChatInvitation::setInvitation($userInstance, (int)$Params['user_parameters_unordered']['inv']);    
}

$tpl->set('visitor',$userInstance);

$inputData = new stdClass();
$inputData->question = '';
$inputData->email = isset($_GET['prefill']['email']) ? (string)$_GET['prefill']['email'] : '';
$inputData->phone = isset($_GET['prefill']['phone']) ? (string)$_GET['prefill']['phone'] : '';
$inputData->username = isset($_GET['prefill']['username']) ? (string)$_GET['prefill']['username'] : '';

if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) == 1){
	erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
	$inputData->departament_id = array_shift($Params['user_parameters_unordered']['department']);
} else {
	$inputData->departament_id = 0;
}

if (is_array($Params['user_parameters_unordered']['department'])){
	erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
	$inputData->departament_id_array = $Params['user_parameters_unordered']['department'];
}

$inputData->validate_start_chat = false;
$inputData->operator = (int)$Params['user_parameters_unordered']['operator'];
$inputData->name_items = array();
$inputData->value_items = array();
$inputData->value_types = array();
$inputData->value_sizes = array();
$inputData->jsvar = array();
$inputData->ua = $Params['user_parameters_unordered']['ua'];
$inputData->value_items_admin = array(); // These variables get's filled from start chat form settings
$inputData->via_hidden = array(); // These variables get's filled from start chat form settings
$inputData->hattr = array();
$inputData->encattr = array();
$inputData->via_encrypted = array();
$inputData->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;
$inputData->tag = isset($_GET['tag']) ? (string)$_GET['tag'] : (isset($Params['user_parameters_unordered']['tag']) ? $Params['user_parameters_unordered']['tag'] : '');

// If chat was started based on key up, we do not need to store a message
//  because user is still typing it. We start chat in the background just.
$inputData->key_up_started = (isset($_POST['keyUpStarted']) && $_POST['keyUpStarted'] == 1);

if ((string)$Params['user_parameters_unordered']['vid'] != '') {
    $inputData->vid = (string)$Params['user_parameters_unordered']['vid'];
}

$chat = new erLhcoreClassModelChat();

// Assign department instantly
if ($inputData->departament_id > 0) {
    $chat->dep_id = $inputData->departament_id;
	$tpl->set('department',$inputData->departament_id);
} else {
	$tpl->set('department',false);
}

// Set time zone if script detected it
if ($userInstance->visitor_tz != '') {
	erLhcoreClassModule::$defaultTimeZone = $userInstance->visitor_tz;
	date_default_timezone_set(erLhcoreClassModule::$defaultTimeZone);
}

$tpl->set('playsound',(string)$Params['user_parameters_unordered']['playsound'] == 'true' && !isset($_POST['askQuestion']) && erLhcoreClassModelChatConfig::fetch('sound_invitation')->current_value == 1);

$fullHeight = (isset($Params['user_parameters_unordered']['fullheight']) && $Params['user_parameters_unordered']['fullheight'] == 'true') ? true : false;

if (is_numeric($inputData->departament_id) && $inputData->departament_id > 0 && ($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $inputData->departament_id)))) !== false) {
	$startDataFields = $startDataDepartment->data_array;
} else {
	// Start chat field options
	$startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
	$startDataFields = (array)$startData->data;
}

if (isset($startDataFields['name_hidden']) && $startDataFields['name_hidden'] == 1) {
    $inputData->hattr[] = 'username';
    $userInstance->requires_username = true;
}

if (isset($startDataFields['email_hidden']) && $startDataFields['email_hidden'] == 1) {
    $inputData->hattr[] = 'email';
    $userInstance->requires_email = true;
}

if (isset($startDataFields['phone_hidden']) && $startDataFields['phone_hidden'] == 1) {
    $inputData->hattr[] = 'phone';
    $userInstance->requires_phone = true;
}

// Allow extension override start chat fields
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.readoperatormessage_data_field',array('data_fields' => & $startDataFields, 'params' => $Params));

$modeAppendTheme = '';
if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$modeAppendTheme = '/(theme)/'.$theme->id;
		$tpl->set('theme',$Result['theme']);
	} catch (Exception $e) {

	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
			$Result['theme'] = $theme;
			$modeAppendTheme = '/(theme)/'.$theme->id;
			$tpl->set('theme',$Result['theme']);
		} catch (Exception $e) {
		
		}
	}
}

$modeAppendTheme .= '/(fullheight)/';
$modeAppendTheme .= ($fullHeight) ? 'true' : 'false';

if (isset($_POST['askQuestion']))
{
    $validationFields = array();
    $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['DepartamentID'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => -1) );
    $validationFields['DepartmentIDDefined'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY);
    $validationFields['operator'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1) );
    $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
    $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
    $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
    $validationFields['tag'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
    $validationFields['user_timezone'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int');
    
    // Additional attributes
    $validationFields['name_items'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY );
	$validationFields['values_req'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY );
	$validationFields['value_items'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY );
	$validationFields['value_types'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY );
	$validationFields['value_sizes'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY );
	$validationFields['value_show'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY );
	$validationFields['hattr'] = new ezcInputFormDefinitionElement ( ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY );
	$validationFields['encattr'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'string',	null,  FILTER_REQUIRE_ARRAY	);
	
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

	$validationFields['jsvar'] = new ezcInputFormDefinitionElement(
	    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
	    null,
	    FILTER_REQUIRE_ARRAY
	);

    if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
    	// Start session if required only
    	$currentUser = erLhcoreClassUser::instance();
    	$hashCaptcha = isset($_SESSION[$_SERVER['REMOTE_ADDR']]['form']) ? $_SESSION[$_SERVER['REMOTE_ADDR']]['form'] : null;
    	$nameField = 'captcha_'.$hashCaptcha;
    } else {
    	// Captcha stuff
    	$nameField = 'captcha_'.sha1(erLhcoreClassIPDetect::getIP().$_POST['tscaptcha'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
    }
    
    $validationFields[$nameField] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );
        
    $form = new ezcInputForm( INPUT_POST, $validationFields );
    $Errors = array();

    if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => erLhcoreClassIPDetect::getIP()))) > 0) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You do not have permission to chat! Please contact site owner.');
    }

    if ( $form->hasValidData( 'hattr' ) && !empty($form->hattr))
    {
    	$inputData->hattr = $form->hattr;
    }
    
    if ($form->hasValidData( 'DepartmentIDDefined' )) {
    	$inputData->departament_id_array = $form->DepartmentIDDefined;
    }
    
    if ( $inputData->key_up_started == false && (!$form->hasValidData( 'Question' ) || trim($form->Question) == '') ) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
    } elseif ($form->hasValidData( 'Question' )) {
        $inputData->question = $form->Question;
    }
    
    if ( (!$form->hasValidData( 'Username' ) || trim($form->Username) == '') && $userInstance->requires_username == 1) {
    	if (!in_array('username', $inputData->hattr)) {
        	$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
    	}
    } elseif ( $form->hasValidData( 'Username' ) ) {
        $inputData->username = $chat->nick = $form->Username;
    }

    if ( (!$form->hasValidData( 'Phone' ) || ($form->Phone == '' || mb_strlen($form->Phone) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value)) && ($userInstance->requires_phone == 1)) {
    	if (!in_array('phone', $inputData->hattr)) {
    		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone');
    	}
    } elseif ($form->hasValidData( 'Phone' )) {
    	$chat->phone = $inputData->phone = $form->Phone;
    }
    
    if ($form->hasValidData( 'Question' ) && $form->Question != '' && mb_strlen($form->Question) > (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
    {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum').' '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','characters for a message');
    }

    if ($userInstance->requires_email == 1) {
    	if ( !$form->hasValidData( 'Email' ) ) {
    		if (!in_array('email', $inputData->hattr)) {
    			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a valid email address');
    		}
    	} else {
    		$inputData->email = $chat->email = $form->Email;
    	}
    }
    
    $stringParts = array();
    
    // validate and insert additional_data code in proactive chat
	if ($form->hasValidData ( 'name_items' ) && ! empty ( $form->name_items )) {
		$valuesArray = array ();
		if ($form->hasValidData ( 'value_items' ) && ! empty ( $form->value_items )) {
			$inputData->value_items = $valuesArray = $form->value_items;
		}
		
		if ($form->hasValidData ( 'values_req' ) && ! empty ( $form->values_req )) {
			$inputData->values_req = $form->values_req;
		}
		
		if ($form->hasValidData ( 'value_types' ) && ! empty ( $form->value_types )) {
			$inputData->value_types = $form->value_types;
		}
		
		if ($form->hasValidData ( 'value_sizes' ) && ! empty ( $form->value_sizes )) {
			$inputData->value_sizes = $form->value_sizes;
		}
		
		if ($form->hasValidData ( 'value_show' ) && ! empty ( $form->value_show )) {
			$inputData->value_show = $form->value_show;
		}
		
		if ($form->hasValidData ( 'hattr' ) && ! empty ( $form->hattr )) {
			$inputData->hattr = $form->hattr;
		}
		
		if ( $form->hasValidData( 'encattr' ) && !empty($form->encattr))
		{
		    $inputData->encattr = $form->encattr;
		}
		
		$inputData->name_items = $form->name_items;
		
		$stringParts = array ();
		foreach ( $form->name_items as $key => $name_item ) {
			if (isset ( $inputData->values_req [$key] ) && $inputData->values_req [$key] == 't' && ($inputData->value_show [$key] == 'b' || $inputData->value_show [$key] == (isset ( $additionalParams ['offline'] ) ? 'off' : 'on')) && (! isset ( $valuesArray [$key] ) || trim ( $valuesArray [$key] ) == '')) {
				$Errors [] = trim ( $name_item ) . ' : ' . erTranslationClassLhTranslation::getInstance ()->getTranslation ( 'chat/startchat', 'is required' );
			}
			
			$valueStore = isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '';
			
			if (isset($inputData->encattr[$key]) && $inputData->encattr[$key] == 't' && $valueStore != '') {
			    try {
			        $valueStore = erLhcoreClassChatValidator::decryptAdditionalField($valueStore, $chat);
			    } catch (Exception $e) {
			        $valueStore = $e->getMessage();
			    }
			}
			
			$stringParts [] = array (
					'key' => $name_item,
					'value' => $valueStore,
			        'h' => ($inputData->value_types[$key] && $inputData->value_types[$key] == 'hidden' ? true : false),
			);
		}
	}

    if ( $form->hasValidData( 'tag' ) && !empty($form->tag))
    {
        $stringParts[] = array('h' => false, 'identifier' => 'tag', 'key' => 'Tags', 'value' => $form->tag);
        $inputData->tag = $form->tag;
    }

		
	// Admin custom fields
	if (isset($startDataFields['custom_fields']) && $startDataFields['custom_fields'] != '') {
	    $customAdminfields = json_decode($startDataFields['custom_fields'],true);
	
	    $valuesArray = array();
	
	    // Fill values if exists
	    if ($form->hasValidData( 'value_items_admin' )){
	        $inputData->value_items_admin = $valuesArray = $form->value_items_admin;
	    }
	    
	    if ($form->hasValidData( 'via_hidden' )){
	        $inputData->via_hidden = $form->via_hidden;
	    }
	    
	    if ($form->hasValidData( 'via_encrypted' )) {
            $inputData->via_encrypted = $form->via_encrypted;
	    }
	    
	    if (is_array($customAdminfields)){
	        foreach ($customAdminfields as $key => $adminField) {
	
	            if (isset($inputData->value_items_admin[$key]) && isset($adminField['isrequired']) && $adminField['isrequired'] == 'true' && ($adminField['visibility'] == 'all' || $adminField['visibility'] == 'on') && (!isset($valuesArray[$key]) || trim($valuesArray[$key]) == '')) {
	                $Errors[] = trim($adminField['fieldname']).': '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required');
	            }
	
	            if (isset($valuesArray[$key]) && $valuesArray[$key] != '') {
	                
	                $valueStore = (isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '');
	                 
	                if (isset($inputData->via_encrypted[$key]) && $inputData->via_encrypted[$key] == 't' && $valueStore != '') {
	                    try {
	                        $valueStore = erLhcoreClassChatValidator::decryptAdditionalField($valueStore, $chat);
	                    } catch (Exception $e) {
	                        $valueStore = $e->getMessage();
	                    }
	                }
	                	                
	                $stringParts[] = array('h' => (isset($inputData->via_hidden[$key]) || $adminField['fieldtype'] == 'hidden'), 'identifier' => (isset($adminField['fieldidentifier'])) ? $adminField['fieldidentifier'] : null, 'key' => $adminField['fieldname'], 'value' => $valueStore);
	            }
	        }
	    }
	}

    // Javascript variables
    if ( $form->hasValidData( 'jsvar' ) && !empty($form->jsvar))
    {
        foreach (erLhAbstractModelChatVariable::getList(array('customfilter' => array('dep_id = 0 OR dep_id = ' . (int)$chat->dep_id))) as $jsVar) {
            if (isset($form->jsvar[$jsVar->id]) && !empty($form->jsvar[$jsVar->id])) {
                if ($jsVar->var_identifier == 'lhc.nick') {
                    $chat->nick = $form->jsvar[$jsVar->id];
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
	   $chat->additional_data = json_encode ( $stringParts );
	}
		
    if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
    	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 || $hashCaptcha != sha1($_SERVER['REMOTE_ADDR'].$form->$nameField.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ))){
    		$Errors['captcha'] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
    	}
    } else {
    	// Captcha validation
    	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600)
    	{
    		$Errors['captcha'] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
    	}
    }
      
    if ($form->hasValidData( 'operator' ) && erLhcoreClassModelUser::getUserCount(array('filter' => array('id' => $form->operator, 'disabled' => 0))) > 0) {
    	$inputData->operator = $chat->user_id = $form->operator;
    }
    
    if ($form->hasValidData( 'user_timezone' )) {
    	$timezone_name = timezone_name_from_abbr(null, $form->user_timezone*3600, true);
    	if ($timezone_name !== false){
    		$chat->user_tz_identifier = $timezone_name;
    	} else {
    		$chat->user_tz_identifier = '';
    	}
    }

    $chat->dep_id = $inputData->departament_id;

    // Assign default department
    if ($form->hasValidData( 'DepartamentID' ) && erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $form->DepartamentID,'disabled' => 0))) > 0) {
    	$inputData->departament_id = $chat->dep_id = $form->DepartamentID;
	} elseif ($form->hasValidData( 'DepartamentID' ) && $form->DepartamentID == -1) {

	    $chat->dep_id == 0;

	    if (isset($Result['theme']) && $Result['theme'] !== false && $Result['theme']->department_title != '') {
	        $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose').' '.htmlspecialchars($Result['theme']->department_title).'!';
	    } else {
	        $Errors['department'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose department!');
	    }

    } elseif ($chat->dep_id == 0 || erLhcoreClassModelDepartament::getCount(array('filter' => array('id' => $chat->dep_id,'disabled' => 0))) == 0) {
        
        // Perhaps extension overrides default department?
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_department', array('input_form' => $inputData));
        
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

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_read_operator_message',array('errors' => & $Errors, 'input_form' => & $inputData, 'chat' => & $chat));
    
    if (count($Errors) == 0)
    {

       $chat->time = $chat->pnd_time = time();
       $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
       $chat->setIP();
       $chat->hash = erLhcoreClassChat::generateHash();
       $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
       $chat->session_referrer = isset($_POST['r']) ? $_POST['r'] : '';

       if ($chat->nick == '') {
       		$chat->nick = 'Visitor';
       }
       
       erLhcoreClassModelChat::detectLocation($chat, (string)$Params['user_parameters_unordered']['vid']);
     
       $chat->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : $chat->department->priority;
       $chat->chat_initiator = erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE;

       // Set invitation if any
       if ($userInstance->invitation_id > 0) {
            $chat->invitation_id = $userInstance->invitation_id;
       }

       $onlineAttrSystem = $userInstance->online_attr_system_array;

       $ignoreResponder = isset($onlineAttrSystem['lhc_ignore_autoresponder']) && $onlineAttrSystem['lhc_ignore_autoresponder'] == 1;

       if (isset($onlineAttrSystem['lhc_assign_to_me']) && $onlineAttrSystem['lhc_assign_to_me'] == 1 && $userInstance->operator_user_id > 0) {
           $chat->user_id = $userInstance->operator_user_id;
           $chat->tslasign = time();
       }

       // Store chat
       erLhcoreClassChat::getSession()->save($chat);

       $conversionUser = erLhAbstractModelProactiveChatCampaignConversion::fetch($userInstance->conversion_id);
       if ($conversionUser instanceof erLhAbstractModelProactiveChatCampaignConversion) {
           $conversionUser->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_CHAT_STARTED;
           $conversionUser->chat_id = $chat->id;
           $conversionUser->department_id = $chat->dep_id;
           $conversionUser->con_time = time();
           $conversionUser->saveThis();
       }

       // Mark as user has read message from operator.
       $userInstance->message_seen = 1;
       $userInstance->message_seen_ts = time();
       $userInstance->chat_id = $chat->id;
       $userInstance->conversion_id = 0;

        if ($chat->nick != 'Visitor') {
            $onlineAttr = $userInstance->online_attr_system_array;
            if (!isset($onlineAttr['username'])){
                $onlineAttr['username'] = $chat->nick;
                $userInstance->online_attr_system = json_encode($onlineAttr);
            }
        } elseif ($chat->nick == 'Visitor'){
            if ($userInstance->nick && $userInstance->has_nick) {
                $chat->nick = $userInstance->nick;
            }
        }

       $userInstance->saveThis();

       $chat->online_user_id = $userInstance->id;

       if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
       		erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance, erLhcoreClassModelChatConfig::fetch('footprint_background')->current_value == 1);
       }

       // Store Message from operator
       $msg = new erLhcoreClassModelmsg();
       $msg->msg = trim($userInstance->operator_message);
       $msg->chat_id = $chat->id;
       $msg->name_support = $userInstance->operator_user !== false ? trim($userInstance->operator_user->name_support) : (!empty($userInstance->operator_user_proactive) ? $userInstance->operator_user_proactive : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
       $msg->user_id = $userInstance->operator_user_id > 0 ? $userInstance->operator_user_id : -2;
       $msg->time = time()-7; // Deduct 7 seconds so for user all looks more natural

       erLhcoreClassChat::getSession()->save($msg);

       // Chat was based on key up, there is no message object
       $messageInitial = false;
                     
       // Do not store anything, like user just started normal chat
       if ($inputData->key_up_started == false) {
           // Store User Message
           $msg = new erLhcoreClassModelmsg();
           $msg->msg = trim($inputData->question);
           $msg->chat_id = $chat->id;
           $msg->user_id = 0;
           $msg->time = time();
           erLhcoreClassChat::getSession()->save($msg);
           
           $chat->unanswered_chat = 1;
           
           $messageInitial = $msg;
       
           if ($ignoreResponder == false && $userInstance->invitation !== false) {

               $responder = $userInstance->invitation->autoresponder;
               
               if ($responder !== false) {

                   $responder->translateByChat($chat->chat_locale);

                   $beforeAutoResponderErrors = array();
                   erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered', array('chat' => & $chat, 'errors' => & $beforeAutoResponderErrors));

                   if (empty($beforeAutoResponderErrors)) {
                       $responderChat = new erLhAbstractModelAutoResponderChat();
                       $responderChat->auto_responder_id = $responder->id;
                       $responderChat->chat_id = $chat->id;
                       $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                       $responderChat->saveThis();

                       $chat->auto_responder_id = $responderChat->id;

                       if ($responder->wait_message != '') {
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

                       erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered', array('chat' => & $chat));

                       $chat->saveThis();
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

           } elseif ($ignoreResponder == false) {
    
           		// Default auto responder
    	       	$responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);

                if ($responder instanceof erLhAbstractModelAutoResponder) {
                   $beforeAutoResponderErrors = array();
                   erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered', array('chat' => & $chat, 'errors' => & $beforeAutoResponderErrors));

                   if (empty($beforeAutoResponderErrors)) {

                       $responderChat = new erLhAbstractModelAutoResponderChat();
                       $responderChat->auto_responder_id = $responder->id;
                       $responderChat->chat_id = $chat->id;
                       $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                       $responderChat->saveThis();

                       $chat->auto_responder_id = $responderChat->id;

                       if ($responder->wait_message != '') {
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

                       erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered', array('chat' => & $chat));

                       $chat->saveThis();
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
           }
       } else {
	       $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_START_ON_KEY_UP;
	   }
       
       // Set chat attributes for transfer workflow logic
       if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
	       	$chat->transfer_if_na = 1;
	       	$chat->transfer_timeout_ts = time();
	       	$chat->transfer_timeout_ac = $chat->department->transfer_timeout;
       }
       
       // Detect device
       $detect = new Mobile_Detect;
       $chat->uagent = $detect->getUserAgent();
       $chat->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);
       
       $chat->last_msg_id = $msg->id;
       $chat->last_user_msg_time = time();
       $chat->saveThis();

       if ($chat->user_id > 0) {
           erLhcoreClassUserDep::updateLastAcceptedByUser($chat->user_id, time());

           // Update fresh user statistic
           erLhcoreClassChat::updateActiveChats($chat->user_id);
       }

       erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started',array('chat' => & $chat, 'msg' => $messageInitial));
       
       erLhcoreClassChat::updateDepartmentStats($chat->department);

        if ((isset($_GET['ajaxmode']) && $_GET['ajaxmode'] == true) || (isset($_POST['ajaxmode']) && $_POST['ajaxmode'] == true)) {
            header ( 'content-type: application/json; charset=utf-8' );
            echo json_encode(array('location' => erLhcoreClassDesign::baseurl('chat/chatwidgetchat') . '/' . $chat->id . '/' . $chat->hash . $modeAppendTheme .  '/(cstarted)/chat_started_by_invitation_cb'));
            exit;
        } else {
           $Result = erLhcoreClassModule::reRun(erLhcoreClassDesign::baseurlRerun('chat/chatwidgetchat') . '/' . $chat->id . '/' . $chat->hash . $modeAppendTheme .  '/(cstarted)/chat_started_by_invitation_cb');
           return true;
        }

    } else {
        $tpl->set('errors',$Errors);
    }
} elseif ($userInstance->conversion_id > 0) {
    $conversionUser = erLhAbstractModelProactiveChatCampaignConversion::fetch($userInstance->conversion_id);
    if ($conversionUser instanceof erLhAbstractModelProactiveChatCampaignConversion && $conversionUser->invitation_status != erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN) {
        $conversionUser->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN;
        $conversionUser->con_time = time();
        $conversionUser->saveThis();
    }
}

$tpl->set('start_data_fields',$startDataFields);

// User this only if not post
if (!ezcInputForm::hasPostData()) {
	$definition = array(
			'name'  => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
					null,
					FILTER_REQUIRE_ARRAY
			),
            'jsvar'  => new ezcInputFormDefinitionElement(
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
			'hattr' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string',
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
            'encattr' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                    null,
                    FILTER_REQUIRE_ARRAY
            ),
            'via_encrypted' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
                    null,
                    FILTER_REQUIRE_ARRAY
        )
	);
	
	$form = new ezcInputForm( INPUT_GET, $definition );
	
	if ( $form->hasValidData( 'name' ) && !empty($form->name))
	{
		$inputData->name_items = $form->name;
	}
	
	if ( $form->hasValidData( 'sh' ) && !empty($form->sh))
	{
		$inputData->value_show = $form->sh;
	}
	
	if ( $form->hasValidData( 'req' ) && !empty($form->req))
	{
		$inputData->values_req = $form->req;
	}
	
	if ( $form->hasValidData( 'value' ) && !empty($form->value))
	{
		$inputData->value_items = $form->value;
	}

    if ( $form->hasValidData( 'jsvar' ) && !empty($form->jsvar))
    {
        $inputData->jsvar = $form->jsvar;
    }

	if ( $form->hasValidData( 'hattr' ) && !empty($form->hattr))
	{
		$inputData->hattr = $form->hattr;
	}
	
	if ( $form->hasValidData( 'type' ) && !empty($form->type))
	{
		$inputData->value_types = $form->type;
	}
	
	if ( $form->hasValidData( 'size' ) && !empty($form->size))
	{
		$inputData->value_sizes = $form->size;
	}
	
	if ( $form->hasValidData( 'encattr' ) && !empty($form->encattr))
	{
	    $inputData->encattr = $form->encattr;
	}
	
	if ( $form->hasValidData( 'via_encrypted' ) && !empty($form->via_encrypted))
	{
	    $inputData->via_encrypted = $form->via_encrypted;
	}
	
	// Fill back office values ir prefilled
	if ($form->hasValidData( 'value_items_admin' ))
	{
	    $inputData->value_items_admin = $form->value_items_admin;
	}
	
	
	if ($form->hasValidData( 'via_hidden' ))
	{
	    $inputData->via_hidden = $form->via_hidden;
	}
}

$tpl->set('input_data',$inputData);
$tpl->set('fullheight',$fullHeight);

if (isset($_GET['URLReferer']))
{
    $tpl->set('referer',$_GET['URLReferer']);
}

if (isset($_POST['URLRefer']))
{
    $tpl->set('referer',$_POST['URLRefer']);
}

if (isset($_GET['r']))
{
	$tpl->set('referer_site',$_GET['r']);
}

if (isset($_POST['r']))
{
	$tpl->set('referer_site',$_POST['r']);
}

// Auto start chat
$autoStartResult = erLhcoreClassChatValidator::validateAutoStart(array(
    'params' => $Params,
    'inputData' => $inputData,
    'chat' => $chat,
    'startDataFields' => $startDataFields,
    'modeAppendTheme' => $modeAppendTheme,
    'invitation_mode' => true,
    'userInstance' => $userInstance
));

if ($autoStartResult !== false) {
    $Result = $autoStartResult;
    return;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.readoperatormessage',array('tpl' => $tpl, 'params' => & $Params));

$Result['content'] = $tpl->fetch();
$Result['fullheight'] = $fullHeight;
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_chat';
$Result['pagelayout_css_append'] = 'widget-chat';
