<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$item = erLhcoreClassModelGenericBotTrItem::fetch($Params['user_parameters']['id']);
$item->removeThis();

erLhcoreClassModule::redirect('genericbot/listtranslationsitems','/(group_id)/' . $item->group_id);
exit;

?>