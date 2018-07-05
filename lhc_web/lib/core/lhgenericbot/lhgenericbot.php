<?php

class erLhcoreClassGenericBot {

    public static function getSession()
    {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhgenericbot' )
            );
        }
        return self::$persistentSession;
    }

    public static function validateBot(& $bot) {

        $definition = array(
            'Name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Nick' => new ezcInputFormDefinitionElement(
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
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot name!');
        } else {
            $bot->name = $form->Name;
        }

        if ( !$form->hasValidData( 'Nick' ) || $form->Nick == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot nick!');
        } else {
            $bot->nick = $form->Nick;
        }

        if ( $form->hasValidData( 'attr_str_1' ) ) {
            $bot->attr_str_1 = $form->attr_str_1;
        } else {
            $bot->attr_str_1 = '';
        }

        if ( $form->hasValidData( 'attr_str_2' ) ) {
            $bot->attr_str_2 = $form->attr_str_2;
        } else {
            $bot->attr_str_2 = '';
        }

        if ( $form->hasValidData( 'attr_str_3' ) ) {
            $bot->attr_str_3 = $form->attr_str_3;
        } else {
            $bot->attr_str_3 = '';
        }

        return $Errors;
    }

    private static $persistentSession;
    private static $instance = null;
}

?>