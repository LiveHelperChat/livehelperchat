<?php

/**
 * Class used for validator
 * */

class erLhcoreClassChatValidator {

    /**
     * Custom form fields validation
     */
    public static function validateStartChat(& $inputForm, & $start_data_fields, & $chat, $additionalParams = array())
    {
        $validationFields = array();

        // Dynamic form field
        if ($inputForm->validate_start_chat == true) {
            if ( (isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) || isset($additionalParams['offline']) ) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if ((isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if (isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

        } else {
            if ((isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if ((isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if ( (isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true) || isset($additionalParams['offline'])) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if (isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }
        }

        $validationFields['DepartamentID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1));

        $validationFields['name_items'] = new ezcInputFormDefinitionElement(
        		ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',
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

        // Captcha stuff
        /* $hashCaptcha = $_SESSION[$_SERVER['REMOTE_ADDR']]['form'];
        $nameField = 'captcha_'.$_SESSION[$_SERVER['REMOTE_ADDR']]['form']; */

        $nameField = 'captcha_'.sha1($_SERVER['REMOTE_ADDR'].$_POST['tscaptcha'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
        $validationFields[$nameField] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );


        $form = new ezcInputForm( INPUT_POST, $validationFields );
        $Errors = array();

        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $_SERVER['REMOTE_ADDR']))) > 0) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You do not have permission to chat! Please contact site owner.');
        }

        // Captcha validation
        if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 )
        {
        	$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invalid captcha code, please enable Javascript!');
        }


        if (
        ($inputForm->validate_start_chat == true && isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) ||
        ($inputForm->validate_start_chat == false && isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true) ||
        isset($additionalParams['offline'])
        ) {

            if ( !$form->hasValidData( 'Username' ) || ($form->Username == '' && ($start_data_fields['name_require_option'] == 'required' || isset($additionalParams['offline'])))  )
            {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your name');
            } elseif ($form->hasValidData( 'Username' )) {
                $chat->nick = $inputForm->username = $form->Username;
            }

            if ($form->hasValidData( 'Username' ) && $form->Username != '' && strlen($form->Username) > 50)
            {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 50 characters');
            }
        }

        if (($inputForm->validate_start_chat == true && isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) ||
        ($inputForm->validate_start_chat == false && isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) || isset($additionalParams['offline']) ) {

            if ( (!$form->hasValidData( 'Email' ) && $start_data_fields['email_require_option'] == 'required') || (!$form->hasValidData( 'Email' ) && isset($additionalParams['offline'])) ) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Wrong email');
            } elseif ( $form->hasValidData( 'Email' ) ) {
                $chat->email = $inputForm->email = $form->Email;
            } else {
                $chat->email = $inputForm->email = $_POST['Email'];
            }
        }

        // Validate question
        if (($inputForm->validate_start_chat == true && isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) ||
        ($inputForm->validate_start_chat == false && isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true) || isset($additionalParams['offline'])) {

            if ( !$form->hasValidData( 'Question' ) || ($form->Question == '' && ($start_data_fields['message_require_option'] == 'required' || isset($additionalParams['offline'])))) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your message');
            } elseif ($form->hasValidData( 'Question' )) {
                $inputForm->question = $form->Question;
            }

            if ($form->hasValidData( 'Question' ) && $form->Question != '' && strlen($form->Question) > 500)
            {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 500 characters for message');
            }
        }

        // Validate phone
        if (($inputForm->validate_start_chat == true && isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true) ||
        ($inputForm->validate_start_chat == false && isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true)) {

            if ( !$form->hasValidData( 'Phone' ) || ($form->Phone == '' && $start_data_fields['phone_require_option'] == 'required')) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone');
            } elseif ($form->hasValidData( 'Phone' )) {
                $chat->phone = $inputForm->phone = $form->Phone;
            }

            if ($form->hasValidData( 'Phone' ) && $form->Phone != '' && strlen($form->Phone) > 100)
            {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters for phone');
            }
        }

        $departments = erLhcoreClassModelDepartament::getList();
        $ids = array_keys($departments);
        if ($form->hasValidData( 'DepartamentID' ) && in_array($form->DepartamentID,$ids)) {
            $chat->dep_id = $form->DepartamentID;
        } elseif ($chat->dep_id == 0 || !in_array($chat->dep_id,$ids)) {
            $id = array_shift($ids);
            $chat->dep_id = $id;
        }

        // Set chat attributes for transfer workflow logic
        if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
        	$chat->transfer_if_na = 1;
        	$chat->transfer_timeout_ts = time();
        	$chat->transfer_timeout_ac = $chat->department->transfer_timeout;
        }

        $inputForm->departament_id = $chat->dep_id;

        if ( $inputForm->priority !== false && is_numeric($inputForm->priority) ) {
        	$chat->priority = (int)$inputForm->priority;
        } else {
        	$chat->priority = $departments[$chat->dep_id]->priority;
        }

        if ( $form->hasValidData( 'name_items' ) && !empty($form->name_items))
        {
        	$valuesArray = array();
        	if ( $form->hasValidData( 'value_items' ) && !empty($form->value_items))
        	{
        		$inputForm->value_items = $valuesArray = $form->value_items;
        	}

        	if ( $form->hasValidData( 'value_types' ) && !empty($form->value_types))
        	{
        		$inputForm->value_types = $form->value_types;
        	}

        	if ( $form->hasValidData( 'value_sizes' ) && !empty($form->value_sizes))
        	{
        		$inputForm->value_sizes = $form->value_sizes;
        	}

        	$inputForm->name_items = $form->name_items;

        	$stringParts = array();
        	foreach ($form->name_items as $key => $name_item) {
        		$stringParts[] = trim($name_item).' - '.(isset($valuesArray[$key]) ? trim($valuesArray[$key]) : '-');
        	}

        	$chat->additional_data = implode(', ', $stringParts);
        }

        return $Errors;
    }

}

?>