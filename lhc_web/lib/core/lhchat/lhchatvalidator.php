<?php

/**
 * Class used for validator
 * */

class erLhcoreClassChatValidator {

    /**
     * Custom form fields validation
     */
    public static function validateStartChat(& $inputForm, & $start_data_fields, & $chat)
    {
        $validationFields = array();

        // Dynamic form field
        if ($inputForm->validate_start_chat == true) {
            if (isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if (isset($start_data_fields['email_visible_in_popup']) && $start_data_fields['email_visible_in_popup'] == true) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if (isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if (isset($start_data_fields['phone_visible_in_popup']) && $start_data_fields['phone_visible_in_popup'] == true) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

        } else {
            if (isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true) {
                $validationFields['Username'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if (isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true) {
                $validationFields['Email'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'validate_email' );
            }

            if (isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true) {
                $validationFields['Question'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }

            if (isset($start_data_fields['phone_visible_in_page_widget']) && $start_data_fields['phone_visible_in_page_widget'] == true) {
                $validationFields['Phone'] =  new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' );
            }
        }

        $validationFields['DepartamentID'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1));

        $form = new ezcInputForm( INPUT_POST, $validationFields );
        $Errors = array();

        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $_SERVER['REMOTE_ADDR']))) > 0) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You do not have permission to chat! Please contact site owner.');
        }

        if (($inputForm->validate_start_chat == true && isset($start_data_fields['name_visible_in_popup']) && $start_data_fields['name_visible_in_popup'] == true) ||
        ($inputForm->validate_start_chat == false && isset($start_data_fields['name_visible_in_page_widget']) && $start_data_fields['name_visible_in_page_widget'] == true)) {

            if ( !$form->hasValidData( 'Username' ) || ($form->Username == '' && $start_data_fields['name_require_option'] == 'required') )
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
        ($inputForm->validate_start_chat == false && isset($start_data_fields['email_visible_in_page_widget']) && $start_data_fields['email_visible_in_page_widget'] == true)) {

            if ( !$form->hasValidData( 'Email' ) && $start_data_fields['email_require_option'] == 'required' ) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Wrong email');
            } elseif ( $form->hasValidData( 'Email' ) ) {
                $chat->email = $inputForm->email = $form->Email;
            } else {
                $chat->email = $inputForm->email = $_POST['Email'];
            }
        }

        // Validate question
        if (($inputForm->validate_start_chat == true && isset($start_data_fields['message_visible_in_popup']) && $start_data_fields['message_visible_in_popup'] == true) ||
        ($inputForm->validate_start_chat == false && isset($start_data_fields['message_visible_in_page_widget']) && $start_data_fields['message_visible_in_page_widget'] == true)) {

            if ( !$form->hasValidData( 'Question' ) || ($form->Question == '' && $start_data_fields['message_require_option'] == 'required')) {
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
        } else {
            $id = array_shift($ids);
            $chat->dep_id = $id;
        }

        $inputForm->departament_id = $chat->dep_id;

        return $Errors;

    }

}

?>