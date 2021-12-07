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

    public static function validateCampaignRecipient($item) {
        $definition = array(
            'email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'email' )) {
            $item->email = $form->email;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter an e-mail!');
        }

        if (erLhcoreClassModelMailconvMailingCampaignRecipient::getCount(['filter' => ['campaign_id' => $item->campaign_id, 'email' => $item->email]]) == 1) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','This recipient already exists in this campaign!');
        }

        return $Errors;
    }

    public static function validateMailingRecipient($item) {
        $definition = array(
            'email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'disabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'ml' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData( 'email' )) {
            $item->email = $form->email;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Please enter a name!');
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