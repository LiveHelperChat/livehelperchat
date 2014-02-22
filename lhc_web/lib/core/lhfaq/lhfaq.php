<?php

class erLhcoreClassFaq {

   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhfaq' )
            );
        }
        return self::$persistentSession;
   }

   public static function validateFaq(& $faq) {
	   	$definition = array(
	   			'answer' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'question' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'URL' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'Email' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
	   			),
	   			'Identifier' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'ActiveFAQ' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			)
	   	);
	   	$form = new ezcInputForm( INPUT_POST, $definition );
	   	$Errors = array();

	   	if ( !$form->hasValidData( 'answer' ) || $form->answer == '')
	   	{
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','Please enter answer!');
	   	} else {
	   		$faq->answer = $form->answer;
	   	}

	   	if ( $form->hasValidData( 'Identifier' ) && $form->Identifier != '' )
	   	{
	   		if (mb_strlen($form->Identifier) <= 10) {
	   			$faq->identifier = $form->Identifier;
	   		} else {
	   			$faq->identifier = $form->Identifier;
	   			$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','Identifier has to be shorter than 10 characters!');
	   		}
	   		
	   	} else {
	   		$faq->identifier = '';
	   	}

	   	if ( $form->hasValidData( 'Email' ) )
	   	{
	   		$faq->email = $form->Email;
	   	} else {
	   		$faq->email = '';
	   	}

	   	if ( !$form->hasValidData( 'question' ) || $form->question == '')
	   	{
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','Please enter question!');
	   	} else {
	   		$faq->question = $form->question;
	   	}

	   	if ( $form->hasValidData( 'URL' ) )
	   	{
	   		$faq->url = $form->URL;
	   	} else {
	   		$faq->url = '';
	   	}

	   	if ( $form->hasValidData( 'ActiveFAQ' ) && $form->ActiveFAQ == true ) {
	   		$faq->active = 1;
	   	} else {
	   		$faq->active = 0;
	   	}

	   	return $Errors;
   }

   private static $persistentSession;
}

?>