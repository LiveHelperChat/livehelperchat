<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/confirmleave.tpl.php');
echo $tpl->fetch();
exit;

?>