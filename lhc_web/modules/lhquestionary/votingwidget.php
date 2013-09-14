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
		$nameField = 'captcha_'.sha1($_SERVER['REMOTE_ADDR'].$_POST['tscaptcha'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
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

		// Captcha validation
        if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 )
        {
        	$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invalid captcha code, please enable Javascript!');
        }

		if ( empty($Errors) ) {
			if (erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long($_SERVER['REMOTE_ADDR']))),'lh_question_option_answer') > 0) {
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','You have already voted, thank you!');
			}
		}


		if ( count($Errors) == 0) {
			$votingAnswer->saveThis();
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

		if ( $form->hasValidData( 'feedBack' ) && $form->feedBack != '' && strlen($form->feedBack) < 500)
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
			if (erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long($_SERVER['REMOTE_ADDR']))),'lh_question_answer') > 0) {
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/votingwidget','You have already send your feedback!');
			}
		}

		if ( count($Errors) == 0) {
			$answer->saveThis();
			$tpl->set('received',true);
		} else {
			$tpl->set('errors',$Errors);
		}
	}
}

if ($votingRelative !== false){
	if ($votingRelative->is_voting == 1) {
		if (erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long($_SERVER['REMOTE_ADDR']))),'lh_question_option_answer') > 0) {
			$tpl->set('already_voted',true);
		}
	} elseif (erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $votingRelative->id, 'ip' => ip2long($_SERVER['REMOTE_ADDR']))),'lh_question_answer') > 0) {
		$tpl->set('already_voted',true);
	}
}

$tpl->set('voting',$votingRelative);
$tpl->set('answer',$answer);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_questionary';
$Result['dynamic_height_append'] = 10;



if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_questionary_page';
	$Result['pagelayout_css_append'] = 'embed-widget';
}

?>