<?php

class erLhcoreClassMailconvMailingValidator {

    public static function validateMailingList($item) {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
        }

        return $Errors;
    }

    public static function validateCampaign($item) {

        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'starts_at' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'enabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'as_active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'activate_again' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'mailbox_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'body' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'body_alt' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'subject' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'reply_email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'reply_name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'owner_logic' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                    'min_range' => erLhcoreClassModelMailconvMailingCampaign::OWNER_CREATOR,
                    'max_range' => erLhcoreClassModelMailconvMailingCampaign::OWNER_USER
                )
            ),
            'owner_user_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                    'min_range' => 0
                )
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
        }

        if ($form->hasValidData( 'subject' )) {
            $item->subject = $form->subject;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a subject!');
        }

        if ($form->hasValidData( 'owner_user_id' )) {
            $item->owner_user_id = $form->owner_user_id;
        }

        if ($form->hasValidData( 'owner_logic' )) {
            $item->owner_logic = $form->owner_logic;
        }

        if ($form->hasValidData( 'reply_email' )) {
            $item->reply_email = $form->reply_email;
        } else {
            $item->reply_email = '';
        }

        if ($form->hasValidData( 'reply_name' )) {
            $item->reply_name = $form->reply_name;
        } else {
            $item->reply_name = '';
        }

        if ($form->hasValidData( 'starts_at' )) {
            $item->starts_at = strtotime($form->starts_at);
        } else {
            $item->starts_at = 0;
        }

        if ($form->hasValidData( 'enabled' ) && $form->enabled == true) {
            $item->enabled = 1;
        } else {
            $item->enabled = 0;
        }

        if ($form->hasValidData( 'as_active' ) && $form->as_active == true) {
            $item->as_active = 1;
        } else {
            $item->as_active = 0;
        }

        if ($form->hasValidData( 'activate_again' ) && $form->activate_again == true) {
            $item->status = erLhcoreClassModelMailconvMailingCampaign::STATUS_PENDING;
        }

        if ($form->hasValidData( 'body_alt' )) {
            $item->body_alt = $form->body_alt;
        } else {
            $item->body_alt = '';
        }

        if ($form->hasValidData( 'body' )) {
            $item->body = $form->body;
        } else {
            $item->body = '';
        }

        if ($form->hasValidData( 'mailbox_id' ) && $form->mailbox_id != '') {
            $mailbox = erLhcoreClassModelMailconvMailbox::findOne(['filter' => ['mail' => $form->mailbox_id]]);
            if ($mailbox instanceof erLhcoreClassModelMailconvMailbox) {
                $item->mailbox_id = $mailbox->id;
            }
        }

        if ($item->mailbox_id == 0) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please choose a mailbox!');
        }

        return $Errors;
    }

    public static function validateCampaignRecipient($item) {
        $definition = array(
            'email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'mailbox' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_1' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_2' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_3' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_4' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_5' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_6' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'email' )) {
            $item->email = $form->email;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter an e-mail!');
        }

        if ($form->hasValidData( 'mailbox' )) {
            $item->mailbox = $form->mailbox;
        } else {
            $item->mailbox = '';
        }

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        }

        if ($form->hasValidData( 'attr_str_1' )) {
            $item->attr_str_1 = $form->attr_str_1;
        }

        if ($form->hasValidData( 'attr_str_2' )) {
            $item->attr_str_2 = $form->attr_str_2;
        }

        if ($form->hasValidData( 'attr_str_3' )) {
            $item->attr_str_3 = $form->attr_str_3;
        }

        if ($form->hasValidData( 'attr_str_4' )) {
            $item->attr_str_4 = $form->attr_str_4;
        }

        if ($form->hasValidData( 'attr_str_5' )) {
            $item->attr_str_5 = $form->attr_str_5;
        }

        if ($form->hasValidData( 'attr_str_6' )) {
            $item->attr_str_6 = $form->attr_str_6;
        }

        if ($item->id == null && erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['campaign_id' => $item->campaign_id, 'email' => $item->email]]) == 1) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','This recipient already exists in this campaign!');
        }

        return $Errors;
    }

    public static function validateMailingRecipient($item) {
        $definition = array(
            'email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'mailbox' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'disabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'ml' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_1' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_2' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_3' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_4' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_5' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_6' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'email' )) {
            $item->email = $form->email;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter an e-mail!');
        }

        if ($form->hasValidData( 'mailbox' )) {
            $item->mailbox = $form->mailbox;
        }

        if ($form->hasValidData( 'name' )) {
            $item->name = $form->name;
        }

        if ($form->hasValidData( 'attr_str_1' )) {
            $item->attr_str_1 = $form->attr_str_1;
        }

        if ($form->hasValidData( 'attr_str_2' )) {
            $item->attr_str_2 = $form->attr_str_2;
        }

        if ($form->hasValidData( 'attr_str_3' )) {
            $item->attr_str_3 = $form->attr_str_3;
        }

        if ($form->hasValidData( 'attr_str_4' )) {
            $item->attr_str_4 = $form->attr_str_4;
        }

        if ($form->hasValidData( 'attr_str_5' )) {
            $item->attr_str_5 = $form->attr_str_5;
        }

        if ($form->hasValidData( 'attr_str_6' )) {
            $item->attr_str_6 = $form->attr_str_6;
        }

        if ($form->hasValidData( 'ml' ) && !empty($form->ml)) {
            $item->ml_ids = $item->ml_ids_front = $form->ml;
        } else {
            $item->ml_ids = [];
        }

        if ($form->hasValidData( 'disabled' ) && $form->disabled == true) {
            $item->disabled = 1;
        } else {
            $item->disabled = 0;
        }

        return $Errors;
    }
}

?>