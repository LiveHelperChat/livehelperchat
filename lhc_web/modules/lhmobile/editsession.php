<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmobile/editsession.tpl.php');

$item =  erLhcoreClassModelUserSession::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (isset($_POST['Cancel_action'])) {
        erLhcoreClassModule::redirect('mobile/sessions');
        exit ;
    }

    if (isset($_POST['Send_notifications'])) {
        try {
            erLhcoreClassLHCMobile::sendTestNotifications($item);
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }
    }

    if (isset($_POST['Save_page'])) {
        $item->notifications_status = isset($_POST['notifications_status']);
        $item->saveThis();
    }

}

$tpl->setArray(array(
    'item' => $item,
));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Settings')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mobile/sessions'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Sessions')
    ),
    array (
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('mobile/settings', 'Edit session')
    )
);

?>