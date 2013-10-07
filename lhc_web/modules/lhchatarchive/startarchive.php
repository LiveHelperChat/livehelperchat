<?php

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSFR Token' ));
	exit;
}

$archive = new erLhcoreClassModelChatArchiveRange();

$definition = array(
		'RangeFrom' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string'
		),
		'RangeTo' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string'
		)
);

$form = new ezcInputForm( INPUT_POST, $definition );
$Errors = array();

if ( !$form->hasValidData( 'RangeFrom' ) || $form->RangeFrom == '' )
{
	$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a valid from date range!');
} else {
	$range = explode('-', $form->RangeFrom);
	if (checkdate($range[1], $range[2], $range[0])){
		$archive->range_from = mktime(0,0,0,$range[1],$range[2],$range[0]);
	} else {
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a valid from date range!');
	}
}

if ( !$form->hasValidData( 'RangeTo' ) || $form->RangeTo == '' )
{
	$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a valid to date range!');
} else {
	$range = explode('-', $form->RangeTo);
	if (checkdate($range[1], $range[2], $range[0])){
		$archive->range_to = mktime(0,0,0,$range[1],$range[2],$range[0]);
	} else {
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a valid to date range!');
	}
}

if (count($Errors) == 0)
{
	try {
		echo json_encode(array('error' => 'false','id' => $archive->createArchive()));
	} catch (Exception $e) {
		echo json_encode(array('error' => 'true','msg' => $e->getMessage()));
	}
}

exit;

?>