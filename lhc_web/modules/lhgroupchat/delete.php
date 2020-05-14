<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$item = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);
$item->removeThis();

erLhcoreClassModule::redirect('groupchat/list');
exit;

?>