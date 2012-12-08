<?php


$tpl = new erLhcoreClassTemplate( 'lhkernel/nopermission.tpl.php');
$tpl->set('module',$Params);

$Result['content'] = $tpl->fetch();

?>