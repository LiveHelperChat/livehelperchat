<?php

header ( 'content-type: application/json; charset=utf-8' );

$trigger = erLhcoreClassModelGenericBotTrigger::fetch($Params['user_parameters']['id']);

echo json_encode(erLhcoreClassGenericBotValidator::getUseCases($trigger));

exit;
?>