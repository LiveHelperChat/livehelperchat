<?php

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

try {
    $item = erLhcoreClassModelMailconvMailingCampaign::fetch( $Params['user_parameters']['id']);
    $item->removeThis();
    erLhcoreClassModule::redirect('mailing/campaign');
    exit;
} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors',array($e->getMessage()));
    $Result['content'] = $tpl->fetch();
}

?>