<?php

/**
 * Class used for validator
 * */

class erLhcoreClassAdminChatValidatorHelper {

	public static function validateStartChatForm(& $data)
	{
	    $definition = array(
	        // Name options
	        'NameVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'NameVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'NameHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'NameRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	         
	        // Name options offline
	        'OfflineNameVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineNameVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineNameHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineNameRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	    
	        // E-mail options
	        'EmailVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'EmailVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'EmailHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineEmailHidden' => new ezcInputFormDefinitionElement(
	        				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'EmailRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	    
	        // Message options
	        'MessageVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'MessageVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'MessageHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'MessageRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	    
	        // Message options offline
	        'OfflineMessageVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineMessageVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineMessageHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineFileVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineFileVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineMessageRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	    
	        // Phone options
	        'PhoneVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'PhoneVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'PhoneHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'PhoneRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	    
	        // Phone options offline
	        'OfflinePhoneVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflinePhoneVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflinePhoneHidden' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflinePhoneRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
	        'UserMessageHeight' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'int'
	        ),
	    
	        // Force leave a message
	        'ForceLeaveMessage' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	    
	        // TOS
	        'OfflineTOSVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineTOSVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'TOSVisibleInPopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'TOSVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	    
	        'ShowOperatorProfile' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'RemoveOperatorSpace' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'HideMessageLabel' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	    
	        // Custom fields from back office
	        'customFieldLabel' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldType' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldSize' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldVisibility' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldIsrequired' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldDefaultValue' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	    );
	    
	    $form = new ezcInputForm( INPUT_POST, $definition );
	    $Errors = array();
	    $hasValidPopupData = false;
	    $hasWidgetData = false;
	    
	    // Force leave a message
	    if ( $form->hasValidData( 'ForceLeaveMessage' ) && $form->ForceLeaveMessage == true ) {
	        $data['force_leave_a_message'] = true;
	    } else {
	        $data['force_leave_a_message'] = false;
	    }
	    
	    // TOS
	    if ( $form->hasValidData( 'TOSVisibleInPopup' ) && $form->TOSVisibleInPopup == true ) {
	        $data['tos_visible_in_popup'] = true;
	    } else {
	        $data['tos_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'TOSVisibleInPageWidget' ) && $form->TOSVisibleInPageWidget == true ) {
	        $data['tos_visible_in_page_widget'] = true;
	    } else {
	        $data['tos_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineTOSVisibleInPopup' ) && $form->OfflineTOSVisibleInPopup == true ) {
	        $data['offline_tos_visible_in_popup'] = true;
	    } else {
	        $data['offline_tos_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineTOSVisibleInPageWidget' ) && $form->OfflineTOSVisibleInPageWidget == true ) {
	        $data['offline_tos_visible_in_page_widget'] = true;
	    } else {
	        $data['offline_tos_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineFileVisibleInPageWidget' ) && $form->OfflineFileVisibleInPageWidget == true ) {
	        $data['offline_file_visible_in_page_widget'] = true;
	    } else {
	        $data['offline_file_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineFileVisibleInPopup' ) && $form->OfflineFileVisibleInPopup == true ) {
	        $data['offline_file_visible_in_popup'] = true;
	    } else {
	        $data['offline_file_visible_in_popup'] = false;
	    }
	    
	    // Name
	    if ( $form->hasValidData( 'NameVisibleInPopup' ) && $form->NameVisibleInPopup == true ) {
	        $data['name_visible_in_popup'] = true;
	    } else {
	        $data['name_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'NameHidden' ) && $form->NameHidden == true ) {
	        $data['name_hidden'] = true;
	    } else {
	        $data['name_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'NameVisibleInPageWidget' ) && $form->NameVisibleInPageWidget == true ) {
	        $data['name_visible_in_page_widget'] = true;
	    } else {
	        $data['name_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'NameRequireOption' ) && $form->NameRequireOption != '' ) {
	        $data['name_require_option'] = $form->NameRequireOption;
	    } else {
	        $data['name_require_option'] = 'required';
	    }
	    
	    // Name offline
	    if ( $form->hasValidData( 'OfflineNameVisibleInPopup' ) && $form->OfflineNameVisibleInPopup == true ) {
	        $data['offline_name_visible_in_popup'] = true;
	    } else {
	        $data['offline_name_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineNameHidden' ) && $form->OfflineNameHidden == true ) {
	        $data['offline_name_hidden'] = true;
	    } else {
	        $data['offline_name_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineNameVisibleInPageWidget' ) && $form->OfflineNameVisibleInPageWidget == true ) {
	        $data['offline_name_visible_in_page_widget'] = true;
	    } else {
	        $data['offline_name_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineNameRequireOption' ) && $form->OfflineNameRequireOption != '' ) {
	        $data['offline_name_require_option'] = $form->OfflineNameRequireOption;
	    } else {
	        $data['offline_name_require_option'] = 'required';
	    }
	    
	    if ($data['name_visible_in_popup'] == true && $data['name_require_option'] == 'required') {
	        $hasValidPopupData = true;
	    }
	    
	    if ($data['name_visible_in_page_widget'] == true && $data['name_require_option'] == 'required') {
	        $hasWidgetData = true;
	    }
	    
	    // E-mail
	    if ( $form->hasValidData( 'EmailVisibleInPopup' ) && $form->EmailVisibleInPopup == true ) {
	        $data['email_visible_in_popup'] = true;
	    } else {
	        $data['email_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'EmailHidden' ) && $form->EmailHidden == true ) {
	        $data['email_hidden'] = true;
	    } else {
	        $data['email_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineEmailHidden' ) && $form->OfflineEmailHidden == true ) {
	        $data['offline_email_hidden'] = true;
	    } else {
	        $data['offline_email_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'EmailVisibleInPageWidget' ) && $form->EmailVisibleInPageWidget == true ) {
	        $data['email_visible_in_page_widget'] = true;
	    } else {
	        $data['email_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'EmailRequireOption' ) && $form->EmailRequireOption != '' ) {
	        $data['email_require_option'] = $form->EmailRequireOption;
	    } else {
	        $data['email_require_option'] = 'required';
	    }
	    
	    if ( $form->hasValidData( 'UserMessageHeight' ) ) {
	        $data['user_msg_height'] = $form->UserMessageHeight;
	    } else {
	        $data['user_msg_height'] = '';
	    }
	    
	    if ($data['email_visible_in_popup'] == true && $data['email_require_option'] == 'required') {
	        $hasValidPopupData = true;
	    }
	    
	    if ($data['email_visible_in_page_widget'] == true && $data['email_require_option'] == 'required') {
	        $hasWidgetData = true;
	    }
	    
	    // Phone
	    if ( $form->hasValidData( 'PhoneVisibleInPopup' ) && $form->PhoneVisibleInPopup == true ) {
	        $data['phone_visible_in_popup'] = true;
	    } else {
	        $data['phone_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'PhoneHidden' ) && $form->PhoneHidden == true ) {
	        $data['phone_hidden'] = true;
	    } else {
	        $data['phone_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'PhoneVisibleInPageWidget' ) && $form->PhoneVisibleInPageWidget == true ) {
	        $data['phone_visible_in_page_widget'] = true;
	    } else {
	        $data['phone_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'PhoneRequireOption' ) && $form->PhoneRequireOption != '' ) {
	        $data['phone_require_option'] = $form->PhoneRequireOption;
	    } else {
	        $data['phone_require_option'] = 'required';
	    }
	    
	    // Phone offline
	    if ( $form->hasValidData( 'OfflinePhoneVisibleInPopup' ) && $form->OfflinePhoneVisibleInPopup == true ) {
	        $data['offline_phone_visible_in_popup'] = true;
	    } else {
	        $data['offline_phone_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflinePhoneHidden' ) && $form->OfflinePhoneHidden == true ) {
	        $data['offline_phone_hidden'] = true;
	    } else {
	        $data['offline_phone_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflinePhoneVisibleInPageWidget' ) && $form->OfflinePhoneVisibleInPageWidget == true ) {
	        $data['offline_phone_visible_in_page_widget'] = true;
	    } else {
	        $data['offline_phone_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflinePhoneRequireOption' ) && $form->OfflinePhoneRequireOption != '' ) {
	        $data['offline_phone_require_option'] = $form->OfflinePhoneRequireOption;
	    } else {
	        $data['offline_phone_require_option'] = 'required';
	    }
	    
	    if ($data['phone_visible_in_popup'] == true && $data['phone_require_option'] == 'required') {
	        $hasValidPopupData = true;
	    }
	    
	    if ($data['phone_visible_in_page_widget'] == true && $data['phone_require_option'] == 'required') {
	        $hasWidgetData = true;
	    }
	    
	    // Message
	    if ( $form->hasValidData( 'MessageVisibleInPopup' ) && $form->MessageVisibleInPopup == true ) {
	        $data['message_visible_in_popup'] = true;
	    } else {
	        $data['message_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'MessageHidden' ) && $form->MessageHidden == true ) {
	        $data['message_hidden'] = true;
	    } else {
	        $data['message_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'MessageVisibleInPageWidget' ) && $form->MessageVisibleInPageWidget == true ) {
	        $data['message_visible_in_page_widget'] = true;
	    } else {
	        $data['message_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'MessageRequireOption' ) && $form->MessageRequireOption != '' ) {
	        $data['message_require_option'] = $form->MessageRequireOption;
	    } else {
	        $data['message_require_option'] = 'required';
	    }
	    
	    // Message offline
	    if ( $form->hasValidData( 'OfflineMessageVisibleInPopup' ) && $form->OfflineMessageVisibleInPopup == true ) {
	        $data['offline_message_visible_in_popup'] = true;
	    } else {
	        $data['offline_message_visible_in_popup'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineMessageHidden' ) && $form->OfflineMessageHidden == true ) {
	        $data['offline_message_hidden'] = true;
	    } else {
	        $data['offline_message_hidden'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineMessageVisibleInPageWidget' ) && $form->OfflineMessageVisibleInPageWidget == true ) {
	        $data['offline_message_visible_in_page_widget'] = true;
	    } else {
	        $data['offline_message_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'ShowOperatorProfile' ) && $form->ShowOperatorProfile == true ) {
	        $data['show_operator_profile'] = true;
	    } else {
	        $data['show_operator_profile'] = false;
	    }
	    
	    if ( $form->hasValidData( 'RemoveOperatorSpace' ) && $form->RemoveOperatorSpace == true ) {
	        $data['remove_operator_space'] = true;
	    } else {
	        $data['remove_operator_space'] = false;
	    }
	    
	    if ( $form->hasValidData( 'HideMessageLabel' ) && $form->HideMessageLabel == true ) {
	        $data['hide_message_label'] = true;
	    } else {
	        $data['hide_message_label'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineMessageRequireOption' ) && $form->OfflineMessageRequireOption != '' ) {
	        $data['offline_message_require_option'] = $form->OfflineMessageRequireOption;
	    } else {
	        $data['offline_message_require_option'] = 'required';
	    }
	    
	    if ( $form->hasValidData( 'customFieldType' ) && !empty($form->customFieldType)) {
	        $customFields = array();
	        foreach ($form->customFieldType as $key => $customFieldType) {
	            $customFields[] = array(
	                'fieldname' => $form->customFieldLabel[$key],
	                'defaultvalue' => $form->customFieldDefaultValue[$key],
	                'fieldtype' => $customFieldType,
	                'size' => $form->customFieldSize[$key],
	                'visibility' => $form->customFieldVisibility[$key],
	                'isrequired' => $form->customFieldIsrequired[$key],
	            );
	        }
	        $data['custom_fields'] = json_encode($customFields,JSON_HEX_APOS);
	    } else {
	        $data['custom_fields'] = '';
	    }
	    
	    if ($data['message_visible_in_popup'] == true && $data['message_require_option'] == 'required') {
	        $hasValidPopupData = true;
	    }
	    
	    if ($data['message_visible_in_page_widget'] == true && $data['message_require_option'] == 'required') {
	        $hasWidgetData = true;
	    }
	    
	    if ($hasValidPopupData == false){
	        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Please choose at least one field for a popup');
	    }
	    
	    if ($hasWidgetData == false){
	        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Please choose at least one field for a page widget');
	    }
	    
	    return $Errors;
    }
}

?>