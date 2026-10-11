<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

try {
    $item = erLhcoreClassModelMailconvConversation::fetch( $Params['user_parameters']['id']);

    if (!($item instanceof \erLhcoreClassModelMailconvConversation)) {
        $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id']);
        if (isset($mailData['mail'])) {
            $item = $mailData['mail'];
        }
    }

    if (!is_object($item) || !erLhcoreClassChat::hasAccessToRead($item) || !erLhcoreClassChat::hasAccessToWrite($item)) {
        throw new Exception('No permission to delete this conversation!');
    }

    $item->removeThis();

    header('Location: ' . erLhcoreClassChatValidator::getSafeReferer());
    exit;

} catch (Exception $e) {

    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors',array($e->getMessage()));
    $Result['content'] = $tpl->fetch();
}

?>