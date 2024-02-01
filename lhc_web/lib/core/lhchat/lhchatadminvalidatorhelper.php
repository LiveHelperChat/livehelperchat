<?php

/**
 * Class used for validator
 * */

class erLhcoreClassAdminChatValidatorHelper {

    public static function validateSavedSearch(erLhAbstractModelSavedSearch & $search, $params, $scope = 'chat') {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'description' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'days' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 30)
            ),
            'position' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'passive' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a name');
        } else {
            $search->name = $form->name;
        }
        
        if ( $form->hasValidData( 'description' ) ) {
            $search->description = $form->description;
        }

        if ($form->hasValidData( 'position' )) {
            $search->position = $form->position;
        } else {
            $search->position = 0;
        }

        if ($form->hasValidData( 'days' )) {
            $search->days = $form->days;
        } else {
            $search->days = 30;
        }

        if ($form->hasValidData( 'passive' ) && $form->passive == true) {
            $search->passive = 1;
        } else {
            $search->passive = 0;
        }

        $search->params = json_encode($params);
        $search->scope = $scope;

        return $Errors;
    }

    public static function validateReplaceVariable(erLhcoreClassModelCannedMsgReplace & $replace) {
        $definition = array(
            'identifier' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'default' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'conditions' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'repetitiveness' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0,'max_range' => 3)
            ),
            'active_from' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'active_to' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'time_zone' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) {
            $definition[$dayShort] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            );

            $key = $dayShort.'StartTime';
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );

            $key = $dayShort.'EndTime';
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );
        }

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'time_zone' ) && $form->time_zone != '') {
            $replace->time_zone = $form->time_zone;
        } else {
            $replace->time_zone = '';
        }

        if ( !$form->hasValidData( 'identifier' ) || $form->identifier == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a identifier');
        } else {
            $replace->identifier = $form->identifier;
        }

        if ( !$form->hasValidData( 'default' ) || $form->identifier == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a default value');
        } else {
            $replace->default = $form->default;
        }

        if ($form->hasValidData( 'conditions' )) {
            $replace->conditions = $form->conditions;
        } else {
            $replace->conditions = '';
        }

        if ( $form->hasValidData( 'repetitiveness' ) ) {
            $replace->repetitiveness = $form->repetitiveness;
        } else {
            $replace->repetitiveness = 0;
        }

        if ($replace->repetitiveness == erLhcoreClassModelCannedMsg::REP_DAILY) {
            $activeDays = [];
            foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) {
                if ($form->hasValidData( $dayShort ) && $form->{$dayShort} == true) {

                    if ($form->hasValidData( $dayShort . 'StartTime' ) && $form->{$dayShort . 'StartTime'} != '') {
                        $activeDays[$dayShort]['start'] = (int)str_replace(':','',$form->{$dayShort . 'StartTime'});
                    }

                    if ($form->hasValidData( $dayShort . 'EndTime' ) && $form->{$dayShort . 'EndTime'} != '') {
                        $activeDays[$dayShort]['end'] = (int)str_replace(':','',$form->{$dayShort . 'EndTime'});
                    }

                    if (
                        !isset($activeDays[$dayShort]['start']) ||
                        !isset($activeDays[$dayShort]['end']) ||
                        !is_numeric($activeDays[$dayShort]['start']) ||
                        !is_numeric($activeDays[$dayShort]['end']) ||
                        $activeDays[$dayShort]['end'] <= $activeDays[$dayShort]['start']
                    ) {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter from and to time. To has to be greater than from.');
                    }
                }
            }
            $replace->days_activity = json_encode($activeDays, JSON_FORCE_OBJECT);
            $replace->days_activity_array = $activeDays;
        }

        if (
            $replace->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD ||
            $replace->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD_REP
        ) {

            if ( $form->hasValidData( 'active_from' ) && !empty($form->active_from) )
            {
                $d = new DateTime($form->active_from,$replace->time_zone != '' ? new DateTimeZone($replace->time_zone) : null);
                $replace->active_from = $d->getTimestamp();
            }

            if ( $form->hasValidData( 'active_to' ) && !empty($form->active_to) )
            {
                $d = new DateTime($form->active_to,$replace->time_zone != '' ? new DateTimeZone($replace->time_zone) : null);
                $replace->active_to = $d->getTimestamp();
                //$replace->active_to = strtotime($form->active_to);
            }

            if (!is_numeric($replace->active_to)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter activity to period');
            }

            if (!is_numeric($replace->active_from)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter activity from period');
            }

            if (empty($Errors) && $replace->active_from > $replace->active_to) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Activity to period has to be bigger than activity from');
            }
        }

        return $Errors;
    }

    public static function validateCannedMessage(erLhcoreClassModelCannedMsg & $cannedMessage, $userDepartments) {
        $definition = array(
            'Message' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'FallbackMessage' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Title' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'ExplainHover' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'HTMLSnippet' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'active_from' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'active_to' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Position' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array()
            ),
            'repetitiveness' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0,'max_range' => 3)
            ),
            'Delay' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0)
            ),
            'DepartmentID' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'AutoSend' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'Disabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'delete_on_exp' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'Tags' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'languages' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
            'message_lang' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
            'fallback_message_lang' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
        );

        foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) {
            $definition[$dayShort] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            );

            $key = $dayShort.'StartTime';
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );

            $key = $dayShort.'EndTime';
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );
        }


        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();
        
        if ( !$form->hasValidData( 'Message' ) || $form->Message == '' )
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a canned message');
        } else {
            $cannedMessage->msg = $form->Message;
        }
        
        if ( $form->hasValidData( 'FallbackMessage' ) )
        {
            $cannedMessage->fallback_msg = $form->FallbackMessage;
        }

        if ( $form->hasValidData( 'repetitiveness' ) ) {
            $cannedMessage->repetitiveness = $form->repetitiveness;
        } else {
            $cannedMessage->repetitiveness = 0;
        }

        if ( $form->hasValidData( 'delete_on_exp' ) ) {
            $cannedMessage->delete_on_exp = 1;
        } else {
            $cannedMessage->delete_on_exp = 0;
        }

        if ($cannedMessage->repetitiveness == erLhcoreClassModelCannedMsg::REP_DAILY) {
            $activeDays = [];
            foreach (erLhcoreClassDepartament::getWeekDays() as $dayShort => $dayLong) {
                if ($form->hasValidData( $dayShort ) && $form->{$dayShort} == true) {

                    if ($form->hasValidData( $dayShort . 'StartTime' ) && $form->{$dayShort . 'StartTime'} != '') {
                        $activeDays[$dayShort]['start'] = (int)str_replace(':','',$form->{$dayShort . 'StartTime'});
                    }

                    if ($form->hasValidData( $dayShort . 'EndTime' ) && $form->{$dayShort . 'EndTime'} != '') {
                        $activeDays[$dayShort]['end'] = (int)str_replace(':','',$form->{$dayShort . 'EndTime'});
                    }

                    if (
                        !isset($activeDays[$dayShort]['start']) ||
                        !isset($activeDays[$dayShort]['end']) ||
                        !is_numeric($activeDays[$dayShort]['start']) ||
                        !is_numeric($activeDays[$dayShort]['end']) ||
                        $activeDays[$dayShort]['end'] <= $activeDays[$dayShort]['start']
                    ) {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter from and to time. To has to be greater than from.');
                    }
                }
            }
            $cannedMessage->days_activity = json_encode($activeDays, JSON_FORCE_OBJECT);
            $cannedMessage->days_activity_array = $activeDays;
        }

        if (
            $cannedMessage->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD ||
            $cannedMessage->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD_REP
        ) {

            if ( $form->hasValidData( 'active_from' ) && !empty($form->active_from) )
            {
                $cannedMessage->active_from = strtotime($form->active_from);
            }

            if ( $form->hasValidData( 'active_to' ) && !empty($form->active_to) )
            {
                $cannedMessage->active_to = strtotime($form->active_to);
            } else {
                $cannedMessage->active_to = 0;
            }

            if ((!is_numeric($cannedMessage->active_to) || $cannedMessage->active_to == 0) && $cannedMessage->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD_REP && $cannedMessage->repetitiveness == erLhcoreClassModelCannedMsg::REP_PERIOD) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter activity to period');
            }

            if (!is_numeric($cannedMessage->active_from)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter activity from period');
            }

            if (empty($Errors) && is_numeric($cannedMessage->active_to) && $cannedMessage->active_to > 0 &&  $cannedMessage->active_from > $cannedMessage->active_to) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Activity to period has to be bigger than activity from');
            }
        }

        $languagesData = array();
        if ( $form->hasValidData( 'languages' ) && !empty($form->languages) )
        {
            foreach ($form->languages as $index => $languages) {
                $languagesData[] = array(
                    'message' => $form->message_lang[$index],
                    'fallback_message' => $form->fallback_message_lang[$index],
                    'languages' => $form->languages[$index],
                );
            }
        }

        $cannedMessage->languages = json_encode($languagesData, JSON_HEX_APOS);
        $cannedMessage->languages_array = $languagesData;

        if ( $form->hasValidData( 'Title' ) )
        {
            $cannedMessage->title = $form->Title;
        }

        if ( $form->hasValidData( 'HTMLSnippet' ) )
        {
            $cannedMessage->html_snippet = $form->HTMLSnippet;
        } else {
            $cannedMessage->html_snippet = '';
        }
        
        if ( $form->hasValidData( 'ExplainHover' ) )
        {
            $cannedMessage->explain = $form->ExplainHover;
        }
        
        if ( $form->hasValidData( 'AutoSend' ) && $form->AutoSend == true )
        {
            $cannedMessage->auto_send = 1;
        } else {
            $cannedMessage->auto_send = 0;
        }

        if ( $form->hasValidData( 'Disabled' ) && $form->Disabled == true )
        {
            $cannedMessage->disabled = 1;
        } else {
            $cannedMessage->disabled = 0;
        }
        
        if ( $form->hasValidData( 'Position' )  )
        {
            $cannedMessage->position = $form->Position;
        }
        
        if ( $form->hasValidData( 'Delay' )  )
        {
            $cannedMessage->delay = $form->Delay;
        }
        
        if ( $form->hasValidData( 'Tags' )  )
        {
            $cannedMessage->tags_plain = $form->Tags;
        }

        if (strpos($cannedMessage->tags_plain,'#') !== false)
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned message tags should not contain # character');
        }


        if ( !$form->hasValidData( 'DepartmentID' )  ) {

            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validate_canned_msg_user_departments',array('canned_msg' => & $cannedMessage, 'errors' => & $Errors));
            
            $cannedMessage->department_ids = $cannedMessage->department_ids_front = [];

            // Perhaps extension did some internal validation and we don't need anymore validate internaly
            if ($response === false) {
                $cannedMessage->department_id = 0;
            }

            if ($userDepartments !== true) {
                if ($cannedMessage->department_id == 0 && !erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','see_global')) {
                    $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please choose a department!');
                }
            }

        } else {
            $cannedMessage->department_ids_front = $cannedMessage->department_ids = $form->DepartmentID;
            // -1 means, individual per department
            $cannedMessage->department_id = -1;

            if ($userDepartments !== true) {
                if (
                    ($cannedMessage->department_id == 0 && !erLhcoreClassUser::instance()->hasAccessTo('lhcannedmsg','see_global'))
                ) {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please choose a department!');
                }

                if (!empty(array_diff($cannedMessage->department_ids, $userDepartments))) {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','You cannot modify canned messages for the departments you are not assigned to!');
                }
            }
        }

        return $Errors;
    }
    
    /**
     * 
     * @param array $data
     * 
     * @return array
     */
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
            'NameHiddenPrefilled' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'NameHiddenBot' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'RequiresPrefilledDepartment' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'RequireLockForPassedDepartment' => new ezcInputFormDefinitionElement(
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
            'OfflineNameHiddenPrefilled' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineNameRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),
            'OfflineEmailRequireOption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'string'
	        ),

            'pre_chat_html' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	        ),
            'pre_offline_chat_html' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
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
            'EmailHiddenPrefilled' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'EmailHiddenBot' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'OfflineEmailHidden' => new ezcInputFormDefinitionElement(
	        				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'OfflineEmailVisibleInPopup' => new ezcInputFormDefinitionElement(
	        				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'OfflineEmailVisibleInPageWidget' => new ezcInputFormDefinitionElement(
	        				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'OfflineEmailHiddenPrefilled' => new ezcInputFormDefinitionElement(
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
            'MessageHiddenPrefilled' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'MessageHiddenBot' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'MessageAutoStart' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'MessageAutoStartOnKeyPress' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'NoProfileBorder' => new ezcInputFormDefinitionElement(
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
            'OfflineMessageHiddenPrefilled' => new ezcInputFormDefinitionElement(
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
            'PhoneHiddenPrefilled' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'PhoneHiddenBot' => new ezcInputFormDefinitionElement(
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
            'OfflinePhoneHiddenPrefilled' => new ezcInputFormDefinitionElement(
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
            'AutoStartChat' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'MobilePopup' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'DontAutoProcess' => new ezcInputFormDefinitionElement(
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
	        'TOSCheckByDefaultOffline' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'TOSCheckByDefaultOnline' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        
	        // Extra
	        'ShowOperatorProfile' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'RemoveOperatorSpace' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'HideMessageLabel' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'ShowMessagesBox' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'HideStartButton' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'LazyLoad' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'DisableStartChat' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),

            'OfflineNameWidth' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflineEmailWidth' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflinePhoneWidth' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OnlineNameWidth' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OnlineEmailWidth' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'PhoneWidth' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),

            'OnlineNamePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OnlineEmailPriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'MessagePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'PhonePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflineNamePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflineEmailPriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflineMessagePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflinePhonePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflineFilePriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'OfflineTOSPriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'TOSPriority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
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
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null, FILTER_REQUIRE_ARRAY
	        ),
            'customFieldHidePrefilled' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldDefaultValue' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
            'customFieldOptions' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'customFieldIdentifier' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
            'customFieldCondition' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
	        ),
	        'CustomFieldsEncryption' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	        ),
            'pre_conditions' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	        ),
            'customFieldURLIdentifier' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'customFieldURLName' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'customFieldPriority' => new ezcInputFormDefinitionElement(
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

        // Force leave a message
        if ( $form->hasValidData( 'DisableStartChat' ) && $form->DisableStartChat == true ) {
            $data['disable_start_chat'] = true;
        } else {
            $data['disable_start_chat'] = false;
        }

        if ( $form->hasValidData( 'NoProfileBorder' ) && $form->NoProfileBorder == true ) {
            $data['np_border'] = true;
        } else {
            $data['np_border'] = false;
        }

        if ( $form->hasValidData( 'pre_conditions' )) {
            $data['pre_conditions'] = $form->pre_conditions;
        }

        if ( $form->hasValidData( 'OnlineNamePriority' )) {
            $data['name_priority'] = $form->OnlineNamePriority;
        } else {
            $data['name_priority'] = 0;
        }

        if ( $form->hasValidData( 'OnlineEmailPriority' )) {
            $data['email_priority'] = $form->OnlineEmailPriority;
        } else {
            $data['email_priority'] = 0;
        }

        if ( $form->hasValidData( 'MessagePriority' )) {
            $data['message_priority'] = $form->MessagePriority;
        } else {
            $data['message_priority'] = 0;
        }

        if ( $form->hasValidData( 'PhonePriority' )) {
            $data['phone_priority'] = $form->PhonePriority;
        } else {
            $data['phone_priority'] = 0;
        }

        if ( $form->hasValidData( 'OfflineNamePriority' )) {
            $data['offline_name_priority'] = $form->OfflineNamePriority;
        } else {
            $data['offline_name_priority'] = 0;
        }

        if ( $form->hasValidData( 'OfflineEmailPriority' )) {
            $data['offline_email_priority'] = $form->OfflineEmailPriority;
        } else {
            $data['offline_email_priority'] = 0;
        }

        if ( $form->hasValidData( 'OfflineMessagePriority' )) {
            $data['offline_message_priority'] = $form->OfflineMessagePriority;
        } else {
            $data['offline_message_priority'] = 0;
        }

        if ( $form->hasValidData( 'OfflinePhonePriority' )) {
            $data['offline_phone_priority'] = $form->OfflinePhonePriority;
        } else {
            $data['offline_phone_priority'] = 0;
        }

        if ( $form->hasValidData( 'OfflineFilePriority' )) {
            $data['offline_file_priority'] = $form->OfflineFilePriority;
        } else {
            $data['offline_file_priority'] = 0;
        }

        if ( $form->hasValidData( 'OfflineTOSPriority' )) {
            $data['offline_tos_priority'] = $form->OfflineTOSPriority;
        } else {
            $data['offline_tos_priority'] = 0;
        }

        if ( $form->hasValidData( 'TOSPriority' )) {
            $data['tos_priority'] = $form->TOSPriority;
        } else {
            $data['tos_priority'] = 0;
        }

        // Width options

        if ( $form->hasValidData( 'OfflineNameWidth' )) {
            $data['offline_name_width'] = $form->OfflineNameWidth;
        } else {
            $data['offline_name_width'] = 0;
        }

        if ( $form->hasValidData( 'OfflineEmailWidth' )) {
            $data['offline_email_width'] = $form->OfflineEmailWidth;
        } else {
            $data['offline_email_width'] = 0;
        }

        if ( $form->hasValidData( 'OfflinePhoneWidth' )) {
            $data['offline_phone_width'] = $form->OfflinePhoneWidth;
        } else {
            $data['offline_phone_width'] = 0;
        }

        if ( $form->hasValidData( 'OnlineNameWidth' )) {
            $data['name_width'] = $form->OnlineNameWidth;
        } else {
            $data['name_width'] = 0;
        }

        if ( $form->hasValidData( 'OnlineEmailWidth' )) {
            $data['email_width'] = $form->OnlineEmailWidth;
        } else {
            $data['email_width'] = 0;
        }

        if ( $form->hasValidData( 'PhoneWidth' )) {
            $data['phone_width'] = $form->PhoneWidth;
        } else {
            $data['phone_width'] = 0;
        }

	    if ( $form->hasValidData( 'AutoStartChat' ) && $form->AutoStartChat == true ) {
	        $data['auto_start_chat'] = true;
	    } else {
	        $data['auto_start_chat'] = false;
	    }

	    if ( $form->hasValidData( 'HideStartButton' ) && $form->HideStartButton == true ) {
	        $data['hide_start_button'] = true;
	    } else {
	        $data['hide_start_button'] = false;
	    }

	    if ( $form->hasValidData( 'LazyLoad' ) && $form->LazyLoad == true ) {
	        $data['lazy_load'] = true;
	    } else {
	        $data['lazy_load'] = false;
	    }

	    if ( $form->hasValidData( 'MobilePopup' ) && $form->MobilePopup == true ) {
	        $data['mobile_popup'] = true;
	    } else {
	        $data['mobile_popup'] = false;
	    }

	    if ( $form->hasValidData( 'DontAutoProcess' ) && $form->DontAutoProcess == true ) {
	        $data['dont_auto_process'] = true;
	    } else {
	        $data['dont_auto_process'] = false;
	    }
	    
	    // TOS
	    if ( $form->hasValidData( 'TOSVisibleInPopup' ) && $form->TOSVisibleInPopup == true ) {
	        $data['tos_visible_in_popup'] = true;
	    } else {
	        $data['tos_visible_in_popup'] = false;
	    }

	    if ( $form->hasValidData( 'RequiresPrefilledDepartment' ) && $form->RequiresPrefilledDepartment == true ) {
	        $data['requires_dep'] = true;
	    } else {
	        $data['requires_dep'] = false;
	    }

	    if ( $form->hasValidData( 'RequireLockForPassedDepartment' ) && $form->RequireLockForPassedDepartment == true ) {
	        $data['requires_dep_lock'] = true;
	    } else {
	        $data['requires_dep_lock'] = false;
	    }

	    if ( $form->hasValidData( 'ShowMessagesBox' ) && $form->ShowMessagesBox == true ) {
	        $data['show_messages_box'] = true;
	    } else {
	        $data['show_messages_box'] = false;
	    }
	    
	    if ( $form->hasValidData( 'TOSVisibleInPageWidget' ) && $form->TOSVisibleInPageWidget == true ) {
	        $data['tos_visible_in_page_widget'] = true;
	    } else {
	        $data['tos_visible_in_page_widget'] = false;
	    }
	    
	    if ( $form->hasValidData( 'TOSCheckByDefaultOffline' ) && $form->TOSCheckByDefaultOffline == true ) {
	        $data['tos_checked_offline'] = true;
	    } else {
	        $data['tos_checked_offline'] = false;
	    }
	    
	    if ( $form->hasValidData( 'TOSCheckByDefaultOnline' ) && $form->TOSCheckByDefaultOnline == true ) {
	        $data['tos_checked_online'] = true;
	    } else {
	        $data['tos_checked_online'] = false;
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

	    if ( $form->hasValidData( 'NameHiddenBot' ) && $form->NameHiddenBot == true ) {
	        $data['name_hidden_bot'] = true;
	    } else {
	        $data['name_hidden_bot'] = false;
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

	    if ( $form->hasValidData( 'CustomFieldsEncryption' ) && $form->CustomFieldsEncryption != '' ) {
	        if (strlen($form->CustomFieldsEncryption) < 40) {
	            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Minimum 40 characters for encryption key!');
	        } else {
	           $data['custom_fields_encryption'] = $form->CustomFieldsEncryption;
	        }
	    } else {
	        $data['custom_fields_encryption'] = '';
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

	    if ( $form->hasValidData( 'OfflineEmailRequireOption' ) && $form->OfflineEmailRequireOption != '' ) {
	        $data['offline_email_require_option'] = $form->OfflineEmailRequireOption;
	    } else {
	        $data['offline_email_require_option'] = 'required';
	    }

	    if ( $form->hasValidData( 'pre_chat_html' ) && $form->pre_chat_html != '' ) {
	        $data['pre_chat_html'] = $form->pre_chat_html;
	    } else {
	        $data['pre_chat_html'] = '';
	    }

	    if ( $form->hasValidData( 'pre_offline_chat_html' ) && $form->pre_offline_chat_html != '' ) {
	        $data['pre_offline_chat_html'] = $form->pre_offline_chat_html;
	    } else {
	        $data['pre_offline_chat_html'] = '';
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

	    if ( $form->hasValidData( 'EmailHiddenBot' ) && $form->EmailHiddenBot == true ) {
	        $data['email_hidden_bot'] = true;
	    } else {
	        $data['email_hidden_bot'] = false;
	    }
	    
	    if ( $form->hasValidData( 'OfflineEmailHidden' ) && $form->OfflineEmailHidden == true ) {
	        $data['offline_email_hidden'] = true;
	    } else {
	        $data['offline_email_hidden'] = false;
	    }

	    if ( $form->hasValidData( 'OfflineEmailHiddenPrefilled' ) && $form->OfflineEmailHiddenPrefilled == true ) {
	        $data['offline_email_hidden_prefilled'] = true;
	    } else {
	        $data['offline_email_hidden_prefilled'] = false;
	    }

	    if ( $form->hasValidData( 'OfflineEmailVisibleInPageWidget' ) && $form->OfflineEmailVisibleInPageWidget == true ) {
	        $data['offline_email_visible_in_page_widget'] = true;
	    } else {
	        $data['offline_email_visible_in_page_widget'] = false;
	    }

	    if ( $form->hasValidData( 'OfflineEmailVisibleInPopup' ) && $form->OfflineEmailVisibleInPopup == true ) {
	        $data['offline_email_visible_in_popup'] = true;
	    } else {
	        $data['offline_email_visible_in_popup'] = false;
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

	    if ( $form->hasValidData( 'PhoneHiddenBot' ) && $form->PhoneHiddenBot == true ) {
	        $data['phone_hidden_bot'] = true;
	    } else {
	        $data['phone_hidden_bot'] = false;
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

	    if ( $form->hasValidData( 'MessageHiddenBot' ) && $form->MessageHiddenBot == true ) {
	        $data['message_hidden_bot'] = true;
	    } else {
	        $data['message_hidden_bot'] = false;
	    }

	    if ( $form->hasValidData( 'MessageAutoStart' ) && $form->MessageAutoStart == true ) {
	        $data['message_auto_start'] = true;
	    } else {
	        $data['message_auto_start'] = false;
	    }
	    
	    if ( $form->hasValidData( 'MessageAutoStartOnKeyPress' ) && $form->MessageAutoStartOnKeyPress == true ) {
	        $data['message_auto_start_key_press'] = true;
	    } else {
	        $data['message_auto_start_key_press'] = false;
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

	    if ( $form->hasValidData( 'customFieldURLIdentifier' ) && !empty($form->customFieldURLIdentifier)) {
            $customFieldsURL = array();
            foreach ($form->customFieldURLIdentifier as $key => $customFieldIdentifier) {
                $customFieldsURL[] = array(
                    'fieldidentifier' => $customFieldIdentifier,
                    'fieldname' => $form->customFieldURLName[$key]
                );
            }
            $data['custom_fields_url'] = json_encode($customFieldsURL,JSON_HEX_APOS);
        } else {
            $data['custom_fields_url'] = '';
        }

	    if ( $form->hasValidData( 'customFieldType' ) && !empty($form->customFieldType)) {
	        $customFields = array();
	        foreach ($form->customFieldType as $key => $customFieldType) {
	            $customFields[] = array(
	                'fieldname' => $form->customFieldLabel[$key],
	                'defaultvalue' => $form->customFieldDefaultValue[$key],
	                'options' => $form->customFieldOptions[$key],
	                'fieldtype' => $customFieldType,
	                'size' => $form->customFieldSize[$key],
	                'visibility' => $form->customFieldVisibility[$key],
	                'isrequired' => ($form->hasValidData('customFieldIsrequired') && isset($form->customFieldIsrequired[$key]) && $form->customFieldIsrequired[$key] == true),
	                'hide_prefilled' => ($form->hasValidData('customFieldHidePrefilled') && isset($form->customFieldHidePrefilled[$key]) && $form->customFieldHidePrefilled[$key] == true),
	                'fieldidentifier' => $form->customFieldIdentifier[$key],
	                'showcondition' => $form->customFieldCondition[$key],
	                'priority' => (($form->hasValidData('customFieldPriority') && isset($form->customFieldPriority[$key])) ? (int)$form->customFieldPriority[$key] : 0)
	            );
	        }
	        $data['custom_fields'] = json_encode($customFields,JSON_HEX_APOS);
	    } else {
	        $data['custom_fields'] = '';
	    }

        if ( $form->hasValidData( 'NameHiddenPrefilled' ) && $form->NameHiddenPrefilled == true ) {
            $data['name_hidden_prefilled'] = true;
        } else {
            $data['name_hidden_prefilled'] = false;
        }

        if ( $form->hasValidData( 'EmailHiddenPrefilled' ) && $form->EmailHiddenPrefilled == true ) {
            $data['email_hidden_prefilled'] = true;
        } else {
            $data['email_hidden_prefilled'] = false;
        }

        if ( $form->hasValidData( 'MessageHiddenPrefilled' ) && $form->MessageHiddenPrefilled == true ) {
            $data['message_hidden_prefilled'] = true;
        } else {
            $data['message_hidden_prefilled'] = false;
        }

        if ( $form->hasValidData( 'PhoneHiddenPrefilled' ) && $form->PhoneHiddenPrefilled == true ) {
            $data['phone_hidden_prefilled'] = true;
        } else {
            $data['phone_hidden_prefilled'] = false;
        }

        if ( $form->hasValidData( 'OfflineNameHiddenPrefilled' ) && $form->OfflineNameHiddenPrefilled == true ) {
            $data['offline_name_hidden_prefilled'] = true;
        } else {
            $data['offline_name_hidden_prefilled'] = false;
        }

        if ( $form->hasValidData( 'OfflineMessageHiddenPrefilled' ) && $form->OfflineMessageHiddenPrefilled == true ) {
            $data['offline_message_hidden_prefilled'] = true;
        } else {
            $data['offline_message_hidden_prefilled'] = false;
        }
        
        if ( $form->hasValidData( 'OfflinePhoneHiddenPrefilled' ) && $form->OfflinePhoneHiddenPrefilled == true ) {
            $data['offline_phone_hidden_prefilled'] = true;
        } else {
            $data['offline_phone_hidden_prefilled'] = false;
        }

	    return $Errors;
    }

    public static function validateWebhook(& $webhook )
    {
        $definition = array(
            'event' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'configuration' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'bot_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
            ),
            'AbstractInput_trigger_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
            ),
            'bot_id_alt' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
            ),
            'AbstractInput_trigger_id_alt' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
            ),
            'type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'max_range' => 1)
            ),
            'disabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'event' )) {
            $webhook->event = $form->event;
        }

        if ( $form->hasValidData( 'configuration' )) {
            $webhook->configuration = $form->configuration;
        } else {
            $webhook->configuration = '';
        }

        if ( $form->hasValidData( 'type' )) {
            $webhook->type = $form->type;
        } else {
            $webhook->type = 0;
        }

        if ( $form->hasValidData( 'bot_id' )) {
            $webhook->bot_id = $form->bot_id;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Please choose a bot');
        }

        if ( $form->hasValidData( 'bot_id_alt' )) {
            $webhook->bot_id_alt = $form->bot_id_alt;
        } else {
            $webhook->bot_id_alt = 0;
        }

        if ( $form->hasValidData( 'AbstractInput_trigger_id' )) {
            $webhook->trigger_id = $form->AbstractInput_trigger_id;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Please choose a trigger');
        }

        if ( $form->hasValidData( 'AbstractInput_trigger_id_alt' )) {
            $webhook->trigger_id_alt = $form->AbstractInput_trigger_id_alt;
        } else {
            $webhook->trigger_id_alt = 0;
        }

        if ( $form->hasValidData( 'disabled' ) && $form->disabled == true ) {
            $webhook->disabled = 1;
        } else {
            $webhook->disabled = 0;
        }

        return $Errors;
    }

    public static function clearUsersCache()
    {
        ezcDbInstance::get()->query("UPDATE lh_users SET cache_version = cache_version + 1");
    }

    public static function validateIncomingWebhook(& $webhook )
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'identifier' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'configuration' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'scope' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'disabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'log_incoming' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'log_failed_parse' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'icon' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'icon_color' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData('name'))
        {
            $webhook->name = $form->name;
        }

        if ( $form->hasValidData( 'log_incoming' ) && $form->log_incoming == true ) {
            $webhook->log_incoming = 1;
        } else {
            $webhook->log_incoming = 0;
        }
        
        if ( $form->hasValidData( 'log_failed_parse' ) && $form->log_failed_parse == true ) {
            $webhook->log_failed_parse = 1;
        } else {
            $webhook->log_failed_parse = 0;
        }

        if ($form->hasValidData('configuration'))
        {
            $webhook->configuration = $form->configuration;
        } else {
            $webhook->configuration = '';
        }

        if ( $form->hasValidData( 'identifier' ))
        {
            $webhook->identifier = $form->identifier;
        } else {
            $webhook->identifier = '';
        }

        if ( $form->hasValidData( 'scope' ))
        {
            $webhook->scope = $form->scope;
        } else {
            $webhook->scope = '';
        }

        if ( $form->hasValidData( 'icon' )) {
            $webhook->icon = $form->icon;
        } else {
            $webhook->icon = '';
        }

        if ( $form->hasValidData( 'icon_color' )) {
            $webhook->icon_color = $form->icon_color;
        } else {
            $webhook->icon_color = '';
        }

        if ($form->hasValidData('disabled') && $form->disabled == true)
        {
            $webhook->disabled = 1;
        } else {
            $webhook->disabled = 0;
        }

        if ($form->hasValidData('dep_id'))
        {
            $webhook->dep_id = $form->dep_id;
        } else {
            $webhook->dep_id = 0;
        }

        return $Errors;
    }

    public static function validateTrackEvent(& $data) {

        $definition = array(
            'ga_js' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'ga_js' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'js_static' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'ga_dep' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY)
        );

        $optionsEvents = array(
            'showWidget',
            'closeWidget',
            'openPopup',
            'endChat',
            'chatStarted',
            'offlineMessage',
            'showInvitation',
            'hideInvitation',
            'nhClicked',
            'nhClosed',
            'nhShow',
            'nhHide',
            'cancelInvitation',
            'fullInvitation',
            'readInvitation',
            'clickAction',
            'botTrigger',
        );

        foreach ($optionsEvents as $event){
            $definition[$event . '_category'] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );
            $definition[$event . '_action'] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );
            $definition[$event . '_label'] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            );
            $definition[$event . '_on'] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            );
        }

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'ga_js' )) {
            $data['ga_js'] = $form->ga_js;
        } else {
            $data['ga_js'] = '';
        }

        if ( $form->hasValidData( 'js_static' )) {
            $data['js_static'] = $form->js_static;
        } else {
            $data['js_static'] = '';
        }

        if ( $form->hasValidData( 'ga_dep' )) {
            $data['ga_dep'] = $form->ga_dep;
        } else {
            $data['ga_dep'] = [];
        }

        foreach ($optionsEvents as $event) {

            if ($form->hasValidData( $event . '_category' )) {
                $data[$event . '_category'] = $form->{$event . '_category'};
            }

            if ($form->hasValidData( $event . '_action' )) {
                $data[$event . '_action'] = $form->{$event . '_action'};
            }

            if ($form->hasValidData( $event . '_label' )) {
                $data[$event . '_label'] = $form->{$event . '_label'};
            }

            if ($form->hasValidData( $event . '_on' ) && $form->hasValidData( $event . '_on' ) == true) {
                $data[$event . '_on'] = 1;
            } else {
                $data[$event . '_on'] = 0;
            }
        }

        return array();
    }

}

?>