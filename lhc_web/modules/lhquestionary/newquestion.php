<?php

$Data = new erLhcoreClassModelQuestion();

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionary.new', array('questionary' => $Data));

$tpl = erLhcoreClassTemplate::getInstance( 'lhquestionary/newquestion.tpl.php');

if (isset($_POST['SaveAction']))
{
	$definition = array(
			'Question' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'QuestionIntro' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Location' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Active' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'Priority' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			),
			'Revote' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			)
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	if ( !$form->hasValidData( 'Question' ) || $form->Question == '' )
	{
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Please enter a question!');
	} else {
		$Data->question = $form->Question;
	}

	if ( $form->hasValidData( 'QuestionIntro' ) )
	{
		$Data->question_intro = $form->QuestionIntro;
	}

	if ( $form->hasValidData( 'Location' ) )
	{
		$Data->location = $form->Location;
	} else {
		$Data->location = '';
	}

	if ( $form->hasValidData( 'Priority' ) )
	{
		$Data->priority = $form->Priority;
	} else {
		$Data->priority = 0;
	}

	if ( $form->hasValidData( 'Active' ) &&  $form->Active == true)
	{
		$Data->active = 1;
	} else {
		$Data->active = 0;
	}
	
	if ( $form->hasValidData( 'Revote' ) ) {
		$Data->revote = $form->Revote;
	} else {
		$Data->revote = 0;
	}
	
	if (count($Errors) == 0) {

		$Data->saveThis();
		erLhcoreClassModule::redirect('questionary/list');
		exit ;

	} else {
		$tpl->set('errors',$Errors);
	}
}

$tpl->set('question',$Data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('questionary/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Questionary')),
		array('url' => erLhcoreClassDesign::baseurl('questionary/newquestion'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/newquestion','New question')))

?>