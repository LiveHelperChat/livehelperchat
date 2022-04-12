<?php

$tpl = erLhcoreClassTemplate::getInstance('lhviews/acceptinvites.tpl.php');

if (ezcInputForm::hasPostData()) {

    $search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters_unordered']['view']);

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])  || $search->user_id != $currentUser->getUserID()) {
        erLhcoreClassModule::redirect('/');
        exit;
    }

    if (isset($_POST['ActionView']) && $_POST['ActionView'] == 0) {
        $search->status = erLhAbstractModelSavedSearch::ACTIVE;
        $search->updateThis(['update' => ['status']]);
        $tpl->set('item', $search);
        $tpl->set('updated', true);
    } elseif (isset($_POST['ActionView']) && $_POST['ActionView'] == 1) {
        $search->removeThis();
        $tpl->set('rejected', true);
    }
}

$tpl->set('shared_views', erLhAbstractModelSavedSearch::getList([
    'limit' => false,
    'filter' => [
        'status' => erLhAbstractModelSavedSearch::INVITE,
        'user_id' =>  erLhcoreClassUser::instance()->getUserID()]
]));


echo $tpl->fetch();
exit;