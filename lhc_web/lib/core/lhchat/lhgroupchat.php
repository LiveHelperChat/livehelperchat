<?php

class erLhcoreClassGroupChat {

    public static function validateGroupChat( & $item)
    {
        $definition = array(
            'Name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );

        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a name');
        } else {
            $item->name = $form->Name;
        }

        return $Errors;
    }
}

?>