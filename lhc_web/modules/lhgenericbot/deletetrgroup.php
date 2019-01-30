<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$item = erLhcoreClassModelGenericBotTrGroup::fetch($Params['user_parameters']['id']);
$item->removeThis();

foreach (erLhcoreClassModelGenericBotTrItem::getList(array('filter' => array('group_id' => $item->id))) as $itemTr) {
    $itemTr->removeThis();
}

erLhcoreClassModule::redirect('genericbot/listtranslations');
exit;

?>