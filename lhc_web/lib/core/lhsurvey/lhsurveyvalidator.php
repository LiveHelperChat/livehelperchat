<?php

/**
 * Class used for survey fill validation.
 * 
 * */
class erLhcoreClassSurveyValidator {

	public static function validateSurvey(erLhAbstractModelSurveyItem & $surveyItem, erLhAbstractModelSurvey $survey)
	{
		$definition = array(				
			'StarsValue' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1, 'max_range' => $survey->max_stars)
			)
		);
		
		$form = new ezcInputForm( INPUT_POST, $definition );
		$Errors = array();
			
		if ( !$form->hasValidData( 'StarsValue' ) ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please choose a star');
		} else {
			$surveyItem->stars = $form->StarsValue;
		}
				
		return $Errors;		
	}	
}

?>