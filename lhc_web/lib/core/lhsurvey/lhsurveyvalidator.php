<?php

/**
 * Class used for survey fill validation.
 * 
 * */
class erLhcoreClassSurveyValidator {

	public static function validateSurvey(erLhAbstractModelSurveyItem & $surveyItem, erLhAbstractModelSurvey $survey)
	{
		include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));

		$definition = array();

		for ($i = 0; $i < 16; $i++) 
		{
			foreach ($sortOptions as $keyOption => $sortOption) 
			{
	    		if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'}) {
	    			if ($sortOption['type'] == 'stars') {
		    			$definition[$sortOption['field'] . 'Evaluate'] = new ezcInputFormDefinitionElement(
							ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1, 'max_range' => $survey->{$sortOption['field']})
						);
	    			} elseif ($sortOption['type'] == 'question') {
	    				$definition[$sortOption['field'] . 'Question'] = new ezcInputFormDefinitionElement(
    						ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
    					);
	    			} elseif ($sortOption['type'] == 'question_options') {
	    				$definition[$sortOption['field'] . 'EvaluateOption'] = new ezcInputFormDefinitionElement(
    						ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
    					);
	    			}
	    		}
			}
		}
		
		$form = new ezcInputForm( INPUT_POST, $definition );
		$Errors = array();
		
		for ($i = 0; $i < 16; $i++)
		{
			foreach ($sortOptions as $keyOption => $sortOption)
			{
				if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'}) {
					
					if ($sortOption['type'] == 'stars') {
						if (!$form->hasValidData( $sortOption['field'] . 'Evaluate' )) {
						    if ($survey->{$keyOption.'_req'} == 1) {
							     $Errors[] = '"'.htmlspecialchars(trim($survey->{$keyOption . '_title'})).'" : '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required'); 
						    }
						} else {
							$surveyItem->{$sortOption['field']} = $form->{$sortOption['field'] . 'Evaluate'};
						}
					} elseif ($sortOption['type'] == 'question') {						
						if (!$form->hasValidData( $sortOption['field'] . 'Question' ) || ($form->{$sortOption['field'] . 'Question'} == '' && $survey->{$keyOption.'_req'} == 1)) { // @todo Make possible to choose field type in the future
							$Errors[] = '"'.htmlspecialchars(trim($survey->{$keyOption})).'" : '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required'); 
						} else {
							$surveyItem->{$sortOption['field']} = $form->{$sortOption['field'] . 'Question'};
						}
					} elseif ($sortOption['type'] == 'question_options') {						
						if (!$form->hasValidData( $sortOption['field'] . 'EvaluateOption' ) ) {
						    if ($survey->{$keyOption.'_req'} == 1) {
							     $Errors[] = '"'.htmlspecialchars(trim($survey->{$sortOption['field']})).'" : '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','is required'); 
						    }
						} else {
							$surveyItem->{$sortOption['field']} = $form->{$sortOption['field'] . 'EvaluateOption'};
						}
					}
				}
			}
		}

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('survey.validate', array('survey' => & $survey, 'survey_item' => & $surveyItem, 'errors' => & $Errors));

		return $Errors;
	}

	public static function parseAnswer($answer, $options = array())
    {
        $answer = trim($answer);
        $answer = preg_replace('/\[value=(.*?)\]/','',$answer);

        return erLhcoreClassBBCode::make_clickable(htmlspecialchars($answer));
    }

    public static function parseAnswerPlain($answer)
    {
        $answer = trim($answer);
        $answer = preg_replace('/\[value=(.*?)\]/','',$answer);

        return htmlspecialchars(erLhcoreClassBBCode::make_plain($answer));
    }
}

?>