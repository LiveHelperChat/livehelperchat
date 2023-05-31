<?php

namespace LiveHelperChat\Helpers\Bot;

class ConditionValidator
{
    public static function validate(& $condition)
    {
        $definition = array(
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'identifier' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'configuration' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL,'unsafe_raw'
            )
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a name!');
        } else {
            $condition->name = $form->name;
        }

        if ( !$form->hasValidData( 'identifier' ) || $form->identifier == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter an identifier!');
        } else {
            $condition->identifier = $form->identifier;
        }

        if ( $form->hasValidData( 'configuration' ) ) {
            $condition->configuration = $form->configuration;
        } else {
            $condition->configuration = '';
        }

        return $Errors;
    }
}