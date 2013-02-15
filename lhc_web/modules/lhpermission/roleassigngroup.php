<?php

$tpl = erLhcoreClassTemplate::getInstance('lhpermission/roleassigngroup.tpl.php');
$tpl->set('role_id',(int)$Params['user_parameters']['role_id']);

echo $tpl->fetch();
exit;
?>