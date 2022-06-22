<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

$tpl = erLhcoreClassTemplate::getInstance( 'lhwidgetrestapi/chooselanguage.tpl.php');
echo $tpl->fetch();
exit;

?>