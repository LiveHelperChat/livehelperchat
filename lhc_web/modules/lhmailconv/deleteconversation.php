<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

try {
    $item = erLhcoreClassModelMailconvConversation::fetch( $Params['user_parameters']['id']);

    if (!($item instanceof \erLhcoreClassModelMailconvMessage)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
        if (isset($mailData['mail'])) {
            $item = \LiveHelperChat\Models\mailConv\Archive\Conversation::fetchAndLock($Params['user_parameters']['id']);
        }
    }

    $item->removeThis();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;

} catch (Exception $e) {

    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors',array($e->getMessage()));
    $Result['content'] = $tpl->fetch();
}

?>