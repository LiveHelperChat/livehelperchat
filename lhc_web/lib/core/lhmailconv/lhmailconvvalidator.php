<?php

class erLhcoreClassMailconvValidator {

    public static function validateMatchRule($item) {

        $definition = array(
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'conditions' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'mailbox_ids' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
            ),
            'priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'priority_rule' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'from_mail' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'from_name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'subject_contains' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'dep_id' )) {
            $item->dep_id = $form->dep_id;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Please choose a department!');
        }

        if ( $form->hasValidData( 'from_mail' )) {
            $item->from_mail = $form->from_mail;
        } else {
            $item->from_mail = '';
        }

        if ( $form->hasValidData( 'from_name' )) {
            $item->from_name = $form->from_name;
        } else {
            $item->from_name = '';
        }

        if ( $form->hasValidData( 'priority' )) {
            $item->priority = $form->priority;
        } else {
            $item->priority = 0;
        }

        if ( $form->hasValidData( 'priority_rule' )) {
            $item->priority_rule = $form->priority_rule;
        } else {
            $item->priority_rule = 0;
        }

        if ( $form->hasValidData( 'subject_contains' )) {
            $item->subject_contains = $form->subject_contains;
        } else {
            $item->subject_contains = '';
        }

        if ( $form->hasValidData( 'mailbox_ids' )) {
            $item->mailbox_ids = $form->mailbox_ids;
        } else {
            $item->mailbox_ids = [];
        }

        $item->mailbox_id = json_encode($item->mailbox_ids);

        if ( $form->hasValidData( 'active' ) && $form->active == true) {
            $item->active = 1;
        } else {
            $item->active = 0;
        }

        return $Errors;
    }

    public static function validateMailbox($item) {
        $definition = array(
            'mail' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'username' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'password' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'host' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'imap' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'signature' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'port' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'sync_interval' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'mail' ))
        {
            $item->mail = $form->mail;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Please enter a e-mail!');
        }

        if ( $form->hasValidData( 'username' )) {
            $item->username = $form->username;
        } else {
            $item->username = '';
        }

        if ( $form->hasValidData( 'imap' )) {
            $item->imap = $form->imap;
        } else {
            $item->imap = '';
        }

        if ( $form->hasValidData( 'signature' )) {
            $item->signature = $form->signature;
        } else {
            $item->signature = '';
        }

        if ( $form->hasValidData( 'sync_interval' )) {
            $item->sync_interval = $form->sync_interval;
        } else {
            $item->sync_interval = 60;
        }

        if ( $form->hasValidData( 'password' )) {
            $item->password = $form->password;
        } else {
            $item->password = '';
        }

        if ( $form->hasValidData( 'host' )) {
            $item->host = $form->host;
        } else {
            $item->host = '';
        }

        if ( $form->hasValidData( 'port' )) {
            $item->port = $form->port;
        } else {
            $item->port = '';
        }

        if ( $form->hasValidData( 'active' ) && $form->active == true) {
            $item->active = 1;
        } else {
            $item->active = 0;
        }

        return $Errors;
    }
    
    public static function validateResponseTemplate($item) {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'template' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( $form->hasValidData( 'name' ) && $form->name != '')
        {
            $item->name = $form->name;
        } else {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Please enter a name!');
        }

        if ( $form->hasValidData( 'template' )) {
            $item->template = $form->template;
        } else {
            $item->template = '';
        }

        if ( $form->hasValidData( 'dep_id' )) {
            $item->dep_id = $form->dep_id;
        } else {
            $item->dep_id = 0;
        }

        return $Errors;
    }

}

?>