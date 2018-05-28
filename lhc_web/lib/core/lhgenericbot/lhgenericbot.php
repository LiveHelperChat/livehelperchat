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

        return $Errors;
    }

    private static $persistentSession;
    private static $instance = null;
}

?>