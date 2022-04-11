<?php

$tpl = erLhcoreClassTemplate::getInstance('lhviews/shareview.tpl.php');

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);
$user_id = [];

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('/');
        exit;
    }

    $definition = array(
        'name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'description' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'user_ids' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1),FILTER_REQUIRE_ARRAY)
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a name');
    } else {
        $search->name = $form->name;
    }

    if ( $form->hasValidData( 'description' )) {
        $search->description = $form->description;
    }

    if ( !$form->hasValidData( 'user_ids' )) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please choose at-least one user to share your view!');
    } else {
        $user_id = array_unique($form->user_ids);
    }

    if (empty($Errors)) {

        foreach ($user_id as $user_id_share_with) {
            $searchNew = clone $search;
            $searchNew->user_id = $user_id_share_with;
            $searchNew->sharer_user_id = $currentUser->getUserID();
            $searchNew->status = erLhAbstractModelSavedSearch::INVITE;
            $searchNew->id = null;
            $searchNew->saveThis();
        }

        $tpl->set('updated',true);
    } else {
        $tpl->set('errors',$Errors);
    }

}

$tpl->set('view', $search);
$tpl->set('user_id', $user_id);

echo $tpl->fetch();
exit;