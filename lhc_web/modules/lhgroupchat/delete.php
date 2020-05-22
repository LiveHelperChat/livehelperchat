<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $item = erLhcoreClassModelGroupChat::fetchAndLock($Params['user_parameters']['id']);
    $item->removeThis();
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}

erLhcoreClassModule::redirect('groupchat/list');
exit;

?>