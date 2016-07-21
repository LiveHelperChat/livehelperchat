<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$referer = '';
$tpl = erLhcoreClassTemplate::getInstance( 'lhquestionary/votingwidget.tpl.php');

$tpl->set('referer',$referer);

if (isset($_GET['URLReferer']))
{
	$referer = $_GET['URLReferer'];
    $tpl->set('referer',$referer);
}

if (isset($_POST['URLRefer']))
{
	$referer = $_POST['URLRefer'];
    $tpl->set('referer',$_POST['URLRefer']);
}

$embedMode = false;
$modeAppend = '';
if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
	$embedMode = true;
	$modeAppend = '/(mode)/embed';
}

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$modeAppend .= '/(theme)/'.$theme->id;
	} catch (Exception $e) {

	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
			$Result['theme'] = $theme;
			$modeAppend .= '/(theme)/'.$theme->id;
		} catch (Exception $e) {
			
		}
	}
}


$tpl->set('append_mode',$modeAppend);

$votingRelative = erLhcoreClassQuestionary::getReletiveVoting($referer);

$answer = new erLhcoreClassModelQuestionAnswer();
$votingAnswer = new erLhcoreClassModelQuestionOptionAnswer();

if ($votingRelative !== false) {
	if (isset($_POST['VoteAction'])) {

		$definition = array(
				'Option' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL,'int', array('min_range' => 1)
				),
				'QuestionID' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL,'int', array('min_range' => 1)
				)
		);

		// Captcha stuff
		if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
			// Start session if required only
			$currentUser = erLhcoreClassUser::instance();
			$hashCaptcha = isset($_SESSION[$_SERVER['REMOTE_ADDR']]['form']) ? $_SESSION[$_SERVER['REMOTE_ADDR']]['form'] : null;
    		$nameField = 'captcha_'.$hashCaptcha;
		} else {
			$nameField = 'captcha_'.sha1(erLhcoreClassIPDetect::getIP().$_POST['tscaptcha'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
		}
        $definition[$nameField] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );

		$form = new ezcInputForm( INPUT_POST, $definition );
		$Errors = array();

		if ( $form->hasValidData( 'Option' ))
		{
			$votingAnswer->option_id = $form->Option;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Please choose one of the options!');
		}

		if ( $form->hasValidData( 'QuestionID' ))
		{
			$votingAnswer->question_id = $form->QuestionID;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','No question was detected');
		}

		if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
			if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 || $hashCaptcha != sha1($_SERVER['REMOTE_ADDR'].$form->$nameField.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ))){
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
			}
		} else {
			// Captcha validation
	        if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 )
	        {
	        	$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
	        }
		}
		
		if ( empty($Errors) ) {
			
			$baseFilter = array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long(erLhcoreClassIPDetect::getIP())));
			
			if ($votingRelative->revote > 0) {
				$baseFilter['filtergt']['ctime'] = time() - $votingRelative->revote_seconds;
			}
						
			if (erLhcoreClassQuestionary::getCount($baseFilter,'lh_question_option_answer') > 0) {
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','You have already voted, thank you!');
			}
		}

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionaire.before_option_chosen', array('voting' => & $votingAnswer, 'errors' => & $Errors));

		if ( count($Errors) == 0) {
			$votingAnswer->saveThis();
			
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionaire.option_chosen', array('voting' => & $votingAnswer));
			
			$tpl->set('received',true);
		} else {
	        $tpl->set('errors',$Errors);
	    }
	}

	if (isset($_POST['FeedBackAction'])) {

		$definition = array(
				'feedBack' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL,'unsafe_raw'
				),
				'QuestionID' => new ezcInputFormDefinitionElement(
						ezcInputFormDefinitionElement::OPTIONAL,'int', array('min_range' => 1)
				)
		);

		$form = new ezcInputForm( INPUT_POST, $definition );
		$Errors = array();

		if ( $form->hasValidData( 'feedBack' ) && $form->feedBack != '' && mb_strlen($form->feedBack) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
		{
			$answer->answer = $form->feedBack;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','Please enter your feedback!');
		}

		if ( $form->hasValidData( 'QuestionID' ))
		{
			$answer->question_id = $form->QuestionID;
		} else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','No question was detected');
		}

		if ( empty($Errors) ) {
			$baseFilter = array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long(erLhcoreClassIPDetect::getIP())));
			
			if ($votingRelative->revote > 0) {
				$baseFilter['filtergt']['ctime'] = time() - $votingRelative->revote_seconds;
			}
			
			if (erLhcoreClassQuestionary::getCount($baseFilter,'lh_question_answer') > 0) {
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','You have already send your feedback!');
			}
		}

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionaire.before_feedback_left', array('feedback' => & $answer, 'errors' => & $Errors));

		if ( count($Errors) == 0) {
			$answer->saveThis();
			
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionaire.feedback_left', array('feedback' => & $answer));
			
			$tpl->set('received',true);
		} else {
			$tpl->set('errors',$Errors);
		}
	}
}

if ($votingRelative !== false) {	
	$baseFilter = array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long(erLhcoreClassIPDetect::getIP())));
	
	if ($votingRelative->revote > 0) {
		$baseFilter['filtergt']['ctime'] = time() - $votingRelative->revote_seconds;
	}
	
	if ($votingRelative->is_voting == 1) {
		if (erLhcoreClassQuestionary::getCount($baseFilter,'lh_question_option_answer') > 0) {
			$tpl->set('already_voted',true);
		}
	} elseif (erLhcoreClassQuestionary::getCount($baseFilter,'lh_question_answer') > 0) {
		$tpl->set('already_voted',true);
	}
}

$tpl->set('voting',$votingRelative);
$tpl->set('answer',$answer);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_questionary';

if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_questionary_page';
	$Result['pagelayout_css_append'] = 'embed-widget';
}

?>