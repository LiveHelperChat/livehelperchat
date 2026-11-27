<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/edit.tpl.php');

$archive = \LiveHelperChat\Models\mailConv\Archive\Range::fetch($Params['user_parameters']['id']);

if (isset($_POST['Cancel_archive']) )
{
	erLhcoreClassModule::redirect('mailarchive/list');
	exit;
}

if (isset($_POST['Delete_archive']) )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailarchive/list');
        exit;
    }

	$archive->removeThis();
	erLhcoreClassModule::redirect('mailarchive/list');
	exit;
}

if (isset($_POST['Save_archive']) || isset($_POST['Save_and_continue_archive']))
{
	$definition = array(
			'RangeFrom' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'RangeTo' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
            'name' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
            'type' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 1)
			)
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

    if ( $form->hasValidData( 'name' ) ) {
        $archive->name = $form->name;
    }

    if ( $form->hasValidData( 'type' ) && $archive->mails_in_archive == 0) {
        $archive->type = $form->type;
    }

    if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) {

        if ( !$form->hasValidData( 'RangeFrom' ) || $form->RangeFrom == '' )
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter a valid from date range!');
        } else {
            $range = explode('-', $form->RangeFrom);
            if (checkdate($range[1], $range[2], $range[0])){
                $archive->range_from = mktime(0,0,0,$range[1],$range[2],$range[0]);
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter a valid from date range!');
            }
        }

        if ( !$form->hasValidData( 'RangeTo' ) || $form->RangeTo == '' )
        {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter a valid to date range!');
        } else {
            $range = explode('-', $form->RangeTo);
            if (checkdate($range[1], $range[2], $range[0])){
                $archive->range_to = mktime(0,0,0,$range[1],$range[2],$range[0]);
            } else {
                $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter a valid to date range!');
            }
        }
    }


	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('mailarchive/archive');
		exit;
	}

	if (count($Errors) == 0)
	{
        $archive->saveThis();

		if (isset($_POST['Save_and_continue_archive'])){
			erLhcoreClassModule::redirect('mailarchive/process','/'.$archive->id);
			exit;
		}

		$tpl->set('updated',true);

	}  else {
		$tpl->set('errors',$Errors);
	}
}

$tpl->set('archive',$archive);
$Result['content'] = $tpl->fetch();

$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('mailarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')),
		array('url' => erLhcoreClassDesign::baseurl('mailarchive/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/editarchive','Edit archive'));

?>