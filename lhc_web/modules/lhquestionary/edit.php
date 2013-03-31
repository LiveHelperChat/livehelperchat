<?php

$tpl = erLhcoreClassTemplate::getInstance('lhquestionary/edit.tpl.php');
$Question = erLhcoreClassModelQuestion::fetch((int)$Params['user_parameters']['id']);

$tab = $Params['user_parameters_unordered']['tab'] == 'answers' ? 'answers' : '';
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
			'Location' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Active' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'Priority' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			)
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( !$form->hasValidData( 'Question' ) || $form->Question == '' )
	{
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Please enter question!');
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

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('questionary/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/list','Questionary')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/edit','Edit question')))


?>