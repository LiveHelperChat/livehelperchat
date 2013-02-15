<?php


$tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/nopermission.tpl.php');
$tpl->set('module',$Params);

$Result['content'] = $tpl->fetch();

?>