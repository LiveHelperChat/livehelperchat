<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/newarchive.tpl.php');
$archive = new \LiveHelperChat\Models\mailConv\Archive\Range();

if (isset($_POST['Cancel_archive']) )
{
    erLhcoreClassModule::redirect('mailarchive/archive');
    exit;
}

if (isset($_POST['Save_archive']))
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

    if ( $form->hasValidData( 'type' )) {
        $archive->type = $form->type;
    }

    if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_DEFAULT) {
        if ( !$form->hasValidData( 'RangeFrom' ) || $form->RangeFrom == '' )
        {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter a valid from date range!');
        } else {
            $range = explode('-', $form->RangeFrom);
            if (checkdate($range[1], $range[2], $range[0])){
                $archive->range_from = mktime(0,0,0,$range[1],$range[2],$range[0]);
            } else {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter a valid from date range!');
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

    if ( $form->hasValidData( 'name' ) && $form->name != '') {
        $archive->name = $form->name;
    } else if ($form->name == '' && $archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_BACKUP){
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','Please enter an archive name!');
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailarchive/archive');
        exit;
    }

    if (count($Errors) == 0)
    {
        if ($archive->type == \LiveHelperChat\Models\mailConv\Archive\Range::ARCHIVE_TYPE_BACKUP) {
            $archive->saveThis();
            $archive->createArchive();
        }

        $tpl->set('step_2',true);

    }  else {
        $tpl->set('errors',$Errors);
    }

}

$tpl->set('archive',$archive);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('mailarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/newarchive','New archive'));

?>