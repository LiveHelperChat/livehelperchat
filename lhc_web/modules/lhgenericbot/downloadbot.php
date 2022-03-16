<?php

$bot =  erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$exportData = erLhcoreClassGenericBotValidator::exportBot($bot);

header('Content-Disposition: attachment; filename="lhc-bot-'.$bot->id.'.json"');
header('Content-Type: application/json');
echo json_encode($exportData);

exit;
?>