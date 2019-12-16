<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-type: text/css');

$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters']['theme']);

$tpl = erLhcoreClassTemplate::getInstance('lhwidgetrestapi/theme.tpl.php');
$tpl->set('theme',$theme);
echo $tpl->fetch();

exit;
?>