<?php


$tpl = new erLhcoreClassTemplate('lhuser/groupassignuser.tpl.php');
$tpl->set('group_id',(int)$Params['user_parameters']['group_id']);

echo $tpl->fetch();
exit;