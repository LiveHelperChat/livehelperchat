<?php

$tpl = erLhcoreClassTemplate::getInstance('lhpermission/whogrants.tpl.php');
$tpl->set('module_check',(string)$Params['user_parameters']['module_check']);
$tpl->set('function_check',(string)$Params['user_parameters']['function_check']);
$tpl->set('user_id',(int)$Params['user_parameters']['user_id']);

echo $tpl->fetch();
exit;
?>