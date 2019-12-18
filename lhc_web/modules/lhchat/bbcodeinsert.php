<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/bbcodeinsert.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']) && $Params['user_parameters']['chat_id'] > 0) {
    $tpl->set('chat_id', (int)$Params['user_parameters']['chat_id']);
} else {
    $tpl->set('chat_id', null);
}

$tpl->set('mode', null);

$tpl->set('react', isset($_GET['react']));

if (isset($Params['user_parameters_unordered']['mode']) && !empty($Params['user_parameters_unordered']['mode'])){
    $tpl->set('mode', $Params['user_parameters_unordered']['mode']);
}

echo $tpl->fetch();
exit;