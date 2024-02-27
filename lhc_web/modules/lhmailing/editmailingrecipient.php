<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/editmailingrecipient.tpl.php');

$item = erLhcoreClassModelMailconvMailingRecipient::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        die('Invalid CSRF Token');
        exit;
    }

    $Errors = erLhcoreClassMailconvMailingValidator::validateMailingRecipient($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            $tpl->set('updated',true);
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->setArray(array(
    'item' => $item,
));

echo $tpl->fetch();
exit;

?>