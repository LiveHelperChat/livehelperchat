<?php

$bot =  erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

$exportData = erLhcoreClassGenericBotValidator::exportBot($bot);

header('Content-Disposition: attachment; filename="lhc-bot-'.$bot->id.'.json"');
header('Content-Type: application/json');
echo json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

exit;
?>