<?php

$Question = erLhcoreClassModelQuestion::fetch((int)$Params['user_parameters']['id']);

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('questionary.edit', array('questionary' => $Question));

$tpl = erLhcoreClassTemplate::getInstance('lhquestionary/edit.tpl.php');

$validTabs = array('answers','voting');

$tab = in_array((string)$Params['user_parameters_unordered']['tab'], $validTabs) ? (string)$Params['user_parameters_unordered']['tab'] : '';
$tpl->set('tab',$tab);

if ( isset($_POST['CancelAction']) ) {
	erLhcoreClassModule::redirect('questionary/list');
	exit;
}

if (isset($_POST['UpdateAction']) || isset($_POST['SaveAction'])  )
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

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( !$form->hasValidData( 'Question' ) || $form->Question == '' )
	{
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Please enter a question!');
	}

	if ( $form->hasValidData( 'QuestionIntro' ) )
	{
		$Question->question_intro = $form->QuestionIntro;
	}

	if ( $form->hasValidData( 'Location' ) )
	{
		$Question->location = $form->Location;
	} else {
		$Question->location = '';
	}

	if ( $form->hasValidData( 'Priority' ) )
	{
		$Question->priority = $form->Priority;
	} else {
		$Question->priority = 0;
	}

	if ( $form->hasValidData( 'Active' ) &&  $form->Active == true)
	{
		$Question->active = 1;
	} else {
		$Question->active = 0;
	}
	
	if ( $form->hasValidData( 'Revote' ) )
	{
		$Question->revote = $form->Revote;
	} else {
		$Question->revote = 0;
	}
	
	if (count($Errors) == 0)
	{
		$Question->question = $form->Question;
		$Question->saveThis();

		if (isset($_POST['SaveAction'])) {
			erLhcoreClassModule::redirect('questionary/list');
			exit;
		} else {
			$tpl->set('updated',true);
		}

	}  else {
		$tpl->set('errors',$Errors);
	}
}

// Voting tab actions
$Option = (int)$Params['user_parameters_unordered']['option_id'] > 0 ? erLhcoreClassModelQuestionOption::fetch((int)$Params['user_parameters_unordered']['option_id']) : new erLhcoreClassModelQuestionOption();

if ( isset($_POST['UpdateO']) )
{
	$tab = 'voting';

	$definition = array(
			'Option' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Priority' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			)
	);
	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	if ( !$form->hasValidData( 'Option' ) || $form->Option == '' )
	{
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Please enter an option!');
	}

	if ( $form->hasValidData( 'Priority' ) ) {
		$Option->priority = $form->Priority;
	} else {
		$Option->priority = 0;
	}

	if (count($Errors) == 0)
	{
		$Option->option_name = $form->Option;
		$Option->question_id = $Question->id;
		$Option->saveThis();

		// Mark question as it's voting
		$Question->is_voting = 1;
		$Question->saveThis();

		erLhcoreClassModule::redirect('questionary/edit','/'.$Question->id.'/(tab)/voting');
		exit;
	} else {
		$tpl->set('errors',$Errors);
	}
}

if ( isset($_POST['CancelO']) ) {
	erLhcoreClassModule::redirect('questionary/edit','/'.$Question->id.'/(tab)/voting');
	exit;
}



// Answers
$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('questionary/edit').'/'.$Question->id.'/(tab)/answers';
$pages->items_total = erLhcoreClassQuestionary::getCount(array('filter' => array('question_id' => $Question->id)),'lh_question_answer');
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhcoreClassQuestionary::getList(array('filter' => array('question_id' => $Question->id), 'offset' => $pages->low, 'limit' => $pages->items_per_page),'erLhcoreClassModelQuestionAnswer','lh_question_answer');
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);
$tpl->set('question',$Question);
$tpl->set('option',$Option);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('questionary/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Questionary')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Edit a question')))


?>