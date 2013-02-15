<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/groupassignrole.tpl.php');
$tpl->set('group_id',(int)$Params['user_parameters']['group_id']);

echo $tpl->fetch();
exit;
?>