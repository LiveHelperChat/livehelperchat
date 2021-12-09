<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newmailingrecipient.tpl.php');

$item = new erLhcoreClassModelMailconvMailingRecipient();

if (is_array($Params['user_parameters_unordered']['ml'])) {
    $item->ml_ids_front = $Params['user_parameters_unordered']['ml'];
}

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    $items = array();
    $Errors = erLhcoreClassMailconvMailingValidator::validateMailingRecipient($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }

    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);

$Result['content'] = $tpl->fetch();

echo $tpl->fetch();
exit;

?>