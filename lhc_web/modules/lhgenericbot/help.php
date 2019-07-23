<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/help.tpl.php');

$tpl->set('context',$Params['user_parameters']['context']);

echo $tpl->fetch();
exit;

?>